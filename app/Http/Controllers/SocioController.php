<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Comunidad;
use App\Models\Genero;
use App\Models\EstadoCivil;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SociosExport;
use Carbon\Carbon; // <--- IMPORTANTE: Necesario para calcular la edad

class SocioController extends Controller
{
public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $comunidadId = $request->get('comunidad_id');

        // 1. Query Principal para la Tabla (Paginada)
        $query = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->orderBy('apellidos')->orderBy('nombres');

        if ($comunidadId)
            $query->where('comunidad_id', $comunidadId);

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('cedula', 'ILIKE', "%{$search}%")
                    ->orWhere('nombres', 'ILIKE', "%{$search}%")
                    ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                    ->orWhere('codigo', 'ILIKE', "%{$search}%");
            });
        }

        $socios = $query->paginate(10)->withQueryString();

        // 2. Datos para filtros
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);

        // 3. NUEVO: Buscar "Candidatos a Exoneración" para la Alerta General
        // Condición: Nacidos hace 75 años o más Y que NO sean 'exonerado'
        $fechaLimite = \Carbon\Carbon::now()->subYears(75);
        
        $candidatos = Socio::where('fecha_nac', '<=', $fechaLimite)
                           ->where('tipo_beneficio', '!=', 'exonerado')
                           ->orderBy('apellidos')
                           ->get();

        return view('socios.socio-index', compact('socios', 'comunidades', 'candidatos'));
    }
    
    // ESTE MÉTODO SE LLAMA POR AJAX PARA EL MODAL
    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        // Retorna la vista separada (sin layout, solo el form)
        return view('socios.socio-create', compact('comunidades', 'generos', 'estados'));
    }

    public function store(Request $request)
    {
        // 1. Validaciones
        $request->validate([
            // Identificación
            'cedula'            => 'required|string|max:20|unique:socios,cedula',
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date', // Requerido para calcular edad

            // Datos de Contacto
            'telefono'          => 'nullable|string|max:30',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',

            // Relaciones
            'comunidad_id'      => 'nullable|exists:comunidades,id',
            'genero_id'         => 'nullable|exists:generos,id',
            'estado_civil_id'   => 'nullable|exists:estados_civiles,id',

            // Datos Institucionales
            'fecha_inscripcion' => 'required|date',
            'tipo_beneficio'    => 'required|in:sin_subsidio,con_subsidio,exonerado',
            // VALIDACIÓN LÓGICA: Si es exonerado, EXIGIMOS la fecha.
            'fecha_exoneracion' => 'nullable|date|required_if:tipo_beneficio,exonerado',
            'es_representante'  => 'boolean',
        ]);

        try {
            // 2. Control de Edad (Regla de Negocio: 75 Años)
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                // Si intenta registrarse como exonerado pero es menor de 75
                if ($edad < 75) {
                    return back()->withInput()->with('error', 
                        "No se puede crear como Exonerado. El socio tiene $edad años (Mínimo 75).");
                }
                // Si pasa la edad, confiamos en la fecha que tú ingresaste manualmente en el form
            } else {
                // Si no es exonerado, forzamos que la fecha sea null
                $request->merge(['fecha_exoneracion' => null]);
            }

            // 3. Preparar datos
            $data = $request->all();
            $data['created_by'] = auth()->id();

            // 4. Creación
            Socio::create($data);

            return redirect()->route('socios.index')
                ->with('success', 'Socio creado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear socio: ' . $e->getMessage());
        }
    }

    // ESTE MÉTODO SE LLAMA POR AJAX PARA EL MODAL
    public function edit(Socio $socio)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-edit', compact('socio', 'comunidades', 'generos', 'estados'));
    }

    public function update(Request $request, Socio $socio)
    {
        // 1. Validaciones (Ignorando la cédula actual)
        $request->validate([
            'cedula'            => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'telefono'          => 'nullable|string|max:30',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'comunidad_id'      => 'nullable|exists:comunidades,id',
            'genero_id'         => 'nullable|exists:generos,id',
            'estado_civil_id'   => 'nullable|exists:estados_civiles,id',
            'fecha_inscripcion' => 'required|date',
            'tipo_beneficio'    => 'required|in:sin_subsidio,con_subsidio,exonerado',
            // VALIDACIÓN LÓGICA: Si es exonerado, EXIGIMOS la fecha.
            'fecha_exoneracion' => 'nullable|date|required_if:tipo_beneficio,exonerado',
            'es_representante'  => 'boolean',
        ]);

        try {
            // 2. Control de Edad (Regla de Negocio: 75 Años)
            // Calculamos la edad con la fecha que viene del formulario (por si la corrigieron)
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                // Verificar edad
                if ($edad < 75) {
                    return back()->withInput()->with('error', 
                        "Acción denegada: El socio tiene $edad años. Solo mayores de 75 pueden ser Exonerados.");
                }
                // Si pasa la edad, el sistema acepta la fecha_exoneracion que pusiste en el input
            } else {
                // Si cambia a "sin_subsidio" o "con_subsidio", borramos la fecha de exoneración
                $request->merge(['fecha_exoneracion' => null]);
            }

            // 3. Preparar datos
            $data = $request->all();

            // Manejo de Checkbox (si no viene marcado, es 0)
            if (!$request->has('es_representante')) {
                $data['es_representante'] = 0;
            }

            // 4. Actualización
            $socio->update($data);

            return redirect()->route('socios.index')
                ->with('success', 'Socio actualizado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Socio $socio)
    {
        try {
            $socio->delete();
            return redirect()->route('socios.index')->with('success', 'Socio eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ESTE MÉTODO SE LLAMA POR AJAX PARA EL MODAL
    public function show(Socio $socio)
    {
        $socio->load(['comunidad.parroquia.canton', 'genero', 'estadoCivil']);
        return view('socios.socio-show', compact('socio'));
    }

    // ── REPORTES ───────────────────────────────────────────────────
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un socio para generar el reporte.');
        }

        $socios = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->whereIn('id', $ids)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        // 1. Encabezados
        $headings = [
            'ID',
            'Código',
            'Cédula',
            'Apellidos y Nombres',
            'Comunidad',
            'Cantón',
            'Teléfono',
            'Rep.',
            'Fecha'
        ];

        // 2. Mapeo de datos
        $data = $socios->map(function ($s) {
            return [
                'id' => $s->id,
                'codigo' => $s->codigo,
                'cedula' => $s->cedula,
                'nombres_completos' => $s->apellidos . ' ' . $s->nombres,
                'comunidad' => $s->comunidad?->nombre ?? 'N/A',
                'canton' => $s->comunidad?->parroquia?->canton?->nombre ?? 'N/A',
                'telefono' => $s->telefono,
                'representante' => $s->es_representante ? 'SÍ' : 'NO',
                'fecha' => $s->created_at ? $s->created_at->format('d/m/Y') : '',
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new SociosExport($data, $headings), 'socios_reporte_' . date('YmdHis') . '.xlsx');

        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('socios.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('socios_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}
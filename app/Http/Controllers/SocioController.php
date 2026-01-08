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
use Carbon\Carbon;

class SocioController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $comunidadId = $request->get('comunidad_id');

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
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);

        // Alerta de candidatos a exoneración
        $fechaLimite = \Carbon\Carbon::now()->subYears(75);
        $candidatos = Socio::where('fecha_nac', '<=', $fechaLimite)
                           ->where('tipo_beneficio', '!=', 'exonerado')
                           ->orderBy('apellidos')
                           ->get();

        return view('socios.socio-index', compact('socios', 'comunidades', 'candidatos'));
    }
    
    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-create', compact('comunidades', 'generos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula'            => 'required|string|max:20|unique:socios,cedula',
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'telefono'          => 'nullable|string|max:30',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            
            // CAMBIO: Ahora son required
            'comunidad_id'      => 'required|exists:comunidades,id',
            'estado_civil_id'   => 'required|exists:estados_civiles,id',
            'genero_id'         => 'nullable|exists:generos,id',

            'fecha_inscripcion' => 'required|date',
            'tipo_beneficio'    => 'required|in:sin_subsidio,con_subsidio,exonerado',
            'fecha_exoneracion' => 'nullable|date|required_if:tipo_beneficio,exonerado',
            'es_representante'  => 'boolean',

            'condicion'         => 'required|in:ninguna,discapacidad,enfermedad_terminal',
            'estatus'           => 'required|in:vivo,fallecido',
        ]);

        try {
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                if ($edad < 75) {
                    return back()->withInput()->with('error', 
                        "No se puede crear como Exonerado. El socio tiene $edad años (Mínimo 75).");
                }
            } else {
                $request->merge(['fecha_exoneracion' => null]);
            }

            $data = $request->all();
            $data['created_by'] = auth()->id();

            Socio::create($data);

            return redirect()->route('socios.index')
                ->with('success', 'Socio creado correctamente.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear socio: ' . $e->getMessage());
        }
    }
    public function edit(Socio $socio)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-edit', compact('socio', 'comunidades', 'generos', 'estados'));
    }

    public function update(Request $request, Socio $socio)
    {
        $request->validate([
            'cedula'            => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'telefono'          => 'nullable|string|max:30',
            'direccion'         => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',

            'comunidad_id'      => 'required|exists:comunidades,id',
            'estado_civil_id'   => 'required|exists:estados_civiles,id',
            'genero_id'         => 'nullable|exists:generos,id',

            'fecha_inscripcion' => 'required|date',
            'tipo_beneficio'    => 'required|in:sin_subsidio,con_subsidio,exonerado',
            'fecha_exoneracion' => 'nullable|date|required_if:tipo_beneficio,exonerado',
            'es_representante'  => 'boolean',

            'condicion'         => 'required|in:ninguna,discapacidad,enfermedad_terminal',
            'estatus'           => 'required|in:vivo,fallecido',
        ]);

        try {
            $edad = Carbon::parse($request->fecha_nac)->age;

            if ($request->tipo_beneficio === 'exonerado') {
                if ($edad < 75) {
                    return back()->withInput()->with('error', 
                        "Acción denegada: El socio tiene $edad años. Solo mayores de 75 pueden ser Exonerados.");
                }
            } else {
                $request->merge(['fecha_exoneracion' => null]);
            }

            $data = $request->all();

            if (!$request->has('es_representante')) {
                $data['es_representante'] = 0;
            }

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

    public function show(Socio $socio)
    {
        $socio->load(['comunidad.parroquia.canton', 'genero', 'estadoCivil']);
        return view('socios.socio-show', compact('socio'));
    }

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

        $headings = [
            'ID',
            'Código',
            'Cédula',
            'Apellidos y Nombres',
            'Comunidad',
            'Condición', // Nuevo
            'Estatus',   // Nuevo
            'Rep.',
            'Fecha'
        ];

        $data = $socios->map(function ($s) {
            return [
                'id' => $s->id,
                'codigo' => $s->codigo,
                'cedula' => $s->cedula,
                'nombres_completos' => $s->apellidos . ' ' . $s->nombres,
                'comunidad' => $s->comunidad?->nombre ?? 'N/A',
                'condicion' => ucfirst(str_replace('_', ' ', $s->condicion)), // Formato bonito
                'estatus' => ucfirst($s->estatus),
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
<?php

namespace App\Http\Controllers;

use App\Models\Fallecido;
use App\Models\Socio; // ← NUEVA IMPORTACIÓN
use Illuminate\Http\Request;
use App\Models\Comunidad;
use App\Models\Genero;
use App\Models\EstadoCivil;
use Illuminate\Support\Facades\Log;

// Librerías Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FallecidosExport; // (Debes crear este archivo export)

class FallecidoController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $comunidadId = $request->get('comunidad_id');

        $query = Fallecido::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->orderBy('apellidos')
            ->orderBy('nombres');

        // Filtros
        if ($comunidadId) {
            $query->where('comunidad_id', $comunidadId);
        }
        
        // Usamos el scope del modelo para buscar por cedula, nombre, apellido, codigo
        if ($search !== '') {
            $query->buscar($search);
        }

        $fallecidos = $query->paginate(10)->withQueryString();

        // Carga de datos para los Modales en el Index
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);

        return view('fallecidos.fallecido-index', compact('fallecidos', 'comunidades', 'generos', 'estados'));
    }

    // ── MÉTODOS PARA MODALES (Vistas parciales) ────────────────────

    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);
        return view('fallecidos.fallecido-create', compact('comunidades','generos','estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'comunidad_id'      => 'nullable|exists:comunidades,id',
            'genero_id'         => 'nullable|exists:generos,id',
            'estado_civil_id'   => 'nullable|exists:estados_civiles,id',
            'cedula'            => 'nullable|string|max:20|unique:fallecidos,cedula',
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'fecha_fallecimiento' => 'required|date',
            'observaciones'     => 'nullable|string',
        ]);

        try {
            // Crear fallecido
            $fallecido = Fallecido::create($request->all() + ['created_by' => auth()->id()]);
            
            // 🔥 NUEVA LÓGICA: Actualizar estatus del socio si existe
            if ($fallecido->cedula) {
                $socio = Socio::where('cedula', $fallecido->cedula)->first();
                if ($socio && $socio->estatus !== 'fallecido') {
                    $socio->update(['estatus' => 'fallecido']);
                }
            }
            
            return redirect()->route('fallecidos.index')->with('success', 'Fallecido registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function edit(Fallecido $fallecido)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);
        return view('fallecidos.fallecido-edit', compact('fallecido','comunidades','generos','estados'));
    }

    public function update(Request $request, Fallecido $fallecido)
    {
        $request->validate([
            'comunidad_id'      => 'nullable|exists:comunidades,id',
            'cedula'            => 'nullable|string|max:20|unique:fallecidos,cedula,' . $fallecido->id,
            'nombres'           => 'required|string|max:255',
            'apellidos'         => 'required|string|max:255',
            'fecha_nac'         => 'required|date',
            'fecha_fallecimiento' => 'required|date',
        ]);

        try {
            $fallecido->update($request->all());
            
            // 🔥 NUEVA LÓGICA: Actualizar estatus del socio si cambió la cédula
            if ($fallecido->cedula) {
                $socio = Socio::where('cedula', $fallecido->cedula)->first();
                if ($socio && $socio->estatus !== 'fallecido') {
                    $socio->update(['estatus' => 'fallecido']);
                }
            }
            
            return redirect()->route('fallecidos.index')->with('success', 'Fallecido actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function show(Fallecido $fallecido)
    {
        $fallecido->load(['comunidad.parroquia.canton','genero','estadoCivil','creador']);
        return view('fallecidos.fallecido-show', compact('fallecido'));
    }

    public function destroy(Fallecido $fallecido)
    {
        try {
            $fallecido->delete();
            return redirect()->route('fallecidos.index')->with('success', 'Fallecido eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('fallecidos.index')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    // ── REPORTES PDF Y EXCEL ───────────────────────────────────────
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un registro para generar el reporte.');
        }

        $fallecidos = Fallecido::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->whereIn('id', $ids)
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();

        // Encabezados
        $headings = [
            'ID', 
            'Código', 
            'Cédula', 
            'Apellidos y Nombres', // Unificado
            'Comunidad', 
            'Fecha Nac.', 
            'Fecha Fallecimiento', 
            'Edad Aprox.'
        ];

        // Mapeo de datos
        $data = $fallecidos->map(function ($f) {
            // Calcular edad aproximada al fallecer
            $edad = '';
            if ($f->fecha_nac && $f->fecha_fallecimiento) {
                $edad = $f->fecha_nac->diffInYears($f->fecha_fallecimiento) . ' años';
            } elseif ($f->fecha_nac) {
                // Si solo hay fecha de nac (y no de muerte registrada), calculamos edad actual o dejamos vacío
                $edad = $f->fecha_nac->age . ' años';
            }

            return [
                'id'                => $f->id,
                'codigo'            => $f->codigo,
                'cedula'            => $f->cedula ?? 'S/N',
                'nombres_completos' => $f->apellidos . ' ' . $f->nombres, // Concatenado
                'comunidad'         => $f->comunidad?->nombre ?? 'N/A',
                'fecha_nac'         => $f->fecha_nac ? $f->fecha_nac->format('d/m/Y') : '',
                'fecha_fall'        => $f->fecha_fallecimiento ? $f->fecha_fallecimiento->format('d/m/Y') : '',
                'edad'              => $edad,
            ];
        });

        if ($reportType === 'excel') {
            // Asegúrate de crear App\Exports\FallecidosExport con la misma estructura que SociosExport
            return Excel::download(new FallecidosExport($data, $headings), 'fallecidos_reporte_' . date('YmdHis') . '.xlsx');
            
        } elseif ($reportType === 'pdf') {
            // Asegúrate de crear la vista resources/views/fallecidos/reports-pdf.blade.php
            $pdf = Pdf::loadView('fallecidos.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('fallecidos_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }

    // ── BUSCAR SOCIO POR CÉDULA (API) ──────────────────────────────
    public function buscarSocioPorCedula(Request $request)
    {
        $cedula = $request->input('cedula');
        
        if (!$cedula) {
            return response()->json(['encontrado' => false]);
        }
        
        $socio = Socio::where('cedula', $cedula)->first();
        
        if (!$socio) {
            return response()->json(['encontrado' => false]);
        }
        
        return response()->json([
            'encontrado' => true,
            'socio' => [
                'codigo' => $socio->codigo,
                'nombres' => $socio->nombres,
                'apellidos' => $socio->apellidos,
                'fecha_nac' => $socio->fecha_nac ? $socio->fecha_nac->format('Y-m-d') : '',
                'genero_id' => $socio->genero_id,
                'estado_civil_id' => $socio->estado_civil_id,
                'comunidad_id' => $socio->comunidad_id,
                'estatus' => $socio->estatus,
            ]
        ]);
    }
}
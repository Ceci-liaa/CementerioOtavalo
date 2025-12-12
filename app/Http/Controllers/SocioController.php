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

class SocioController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $comunidadId = $request->get('comunidad_id');

        $query = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->orderBy('apellidos')->orderBy('nombres');

        if ($comunidadId) $query->where('comunidad_id', $comunidadId);

        if ($search !== '') {
            $query->where(function ($w) use ($search) {
                $w->where('cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('nombres', 'ILIKE', "%{$search}%")
                  ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                  ->orWhere('codigo', 'ILIKE', "%{$search}%");
            });
        }

        $socios = $query->paginate(10)->withQueryString();
        
        // Solo necesitamos comunidades para el filtro del index
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-index', compact('socios', 'comunidades'));
    }

    // ESTE MÉTODO SE LLAMA POR AJAX PARA EL MODAL
    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        // Retorna la vista separada (sin layout, solo el form)
        return view('socios.socio-create', compact('comunidades', 'generos', 'estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|max:20|unique:socios,cedula',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            // ... resto de validaciones ...
        ]);

        try {
            Socio::create($request->all() + ['created_by' => auth()->id()]);
            return redirect()->route('socios.index')->with('success', 'Socio creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ESTE MÉTODO SE LLAMA POR AJAX PARA EL MODAL
    public function edit(Socio $socio)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id', 'nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id', 'nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id', 'nombre']);

        return view('socios.socio-edit', compact('socio', 'comunidades', 'generos', 'estados'));
    }

    public function update(Request $request, Socio $socio)
    {
        $request->validate(['cedula' => 'required|unique:socios,cedula,' . $socio->id]);
        
        try {
            $socio->update($request->all());
            return redirect()->route('socios.index')->with('success', 'Socio actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
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

        $headings = [
            'ID', 'Código', 'Cédula', 'Apellidos', 'Nombres', 
            'Comunidad', 'Cantón', 'Teléfono', 'Representante', 'Fecha Registro'
        ];

        $data = $socios->map(function ($s) {
            return [
                'id'            => $s->id,
                'codigo'        => $s->codigo,
                'cedula'        => $s->cedula,
                'apellidos'     => $s->apellidos,
                'nombres'       => $s->nombres,
                'comunidad'     => $s->comunidad?->nombre ?? 'N/A',
                'canton'        => $s->comunidad?->parroquia?->canton?->nombre ?? 'N/A',
                'telefono'      => $s->telefono,
                'representante' => $s->es_representante ? 'SÍ' : 'NO',
                'fecha'         => $s->created_at ? $s->created_at->format('Y-m-d') : '',
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new SociosExport($data, $headings), 'socios_reporte_' . date('YmdHis') . '.xlsx');
        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('socios.reporte-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('socios_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}
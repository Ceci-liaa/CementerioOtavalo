<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServiciosExport; 

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        
        // Ordenamos por código o nombre
        $query = Servicio::orderBy('codigo');

        if ($q !== '') {
            $query->where(function($w) use ($q){
                $w->where('nombre','ILIKE',"%{$q}%")
                  ->orWhere('codigo','ILIKE',"%{$q}%")
                  ->orWhere('descripcion','ILIKE',"%{$q}%");
            });
        }

        $servicios = $query->paginate(10)->withQueryString();

        return view('servicios.servicio-index', compact('servicios','q'));
    }

    public function create()
    {
        return view('servicios.servicio-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'valor'       => 'nullable|numeric',
        ]);

        try {
            Servicio::create($request->only(['nombre','descripcion','valor']));
            return redirect()->route('servicios.index')->with('success','Servicio creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Servicio $servicio)
    {
        return view('servicios.servicio-show', compact('servicio'));
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.servicio-edit', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'valor'       => 'nullable|numeric',
        ]);

        try {
            $servicio->update($request->only(['nombre','descripcion','valor']));
            return redirect()->route('servicios.index')->with('success','Servicio actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Servicio $servicio)
    {
        try {
            $servicio->delete();
            return redirect()->route('servicios.index')->with('success','Servicio eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ── REPORTES (Adaptado a tu estilo SocioController) ───────────────────
    public function reports(Request $request)
    {
        // Recibe array de IDs seleccionados desde la vista
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type'); // 'excel' o 'pdf'

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un servicio para generar el reporte.');
        }

        $servicios = Servicio::whereIn('id', $ids)
            ->orderBy('codigo') // Ordenar por código (S001, S002...)
            ->get();

        // 1. Encabezados para el reporte
        $headings = [
            'ID',
            'Código',
            'Nombre del Servicio',
            'Descripción',
            'Precio Sugerido',
            'Fecha Creación'
        ];

        // 2. Mapeo de datos manual
        $data = $servicios->map(function ($s) {
            return [
                'id'          => $s->id,
                'codigo'      => $s->codigo,
                'nombre'      => $s->nombre,
                'descripcion' => $s->descripcion ?? 'Sin descripción',
                'valor'       => $s->valor ? '$ ' . number_format($s->valor, 2) : '$ 0.00',
                'fecha'       => $s->created_at ? $s->created_at->format('d/m/Y') : '',
            ];
        });

        if ($reportType === 'excel') {
            // Pasamos data y headings al constructor del Export
            return Excel::download(
                new ServiciosExport($data, $headings), 
                'servicios_reporte_' . date('YmdHis') . '.xlsx'
            );
            
        } elseif ($reportType === 'pdf') {
            
            $pdf = Pdf::loadView('servicios.reports-pdf', compact('data', 'headings'));
            
            // Usualmente los reportes de tablas se ven mejor en horizontal
            $pdf->setPaper('A4', 'landscape'); 
            
            return $pdf->download('servicios_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }
}
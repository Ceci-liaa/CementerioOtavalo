<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Nicho;
use App\Models\Bloque;
use App\Models\Fallecido; // <-- importar
use App\Models\Socio;     // <-- importar

class NichoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $bloqueId = $request->get('bloque_id');

        $query = Nicho::with('bloque')->orderBy('codigo');

        if ($q !== '') {
            // Estás en PostgreSQL → ILIKE ok
            $query->where('codigo','ILIKE',"%{$q}%");
        }
        if ($bloqueId) {
            $query->where('bloque_id',$bloqueId);
        }

        $nichos  = $query->paginate(10)->withQueryString();
        $bloques = Bloque::orderBy('nombre')->get();

        return view('nichos.nicho-index', compact('nichos','bloques','bloqueId','q'));
    }

    public function create()
    {
        $bloques = Bloque::orderBy('nombre')->get();

        // ¡OJO!: usar 'cedula' (no 'documento')
        $fallecidos = Fallecido::select('id','apellidos','nombres','cedula')
                        ->orderBy('apellidos')->limit(200)->get();

        $socios = Socio::select('id','apellidos','nombres','cedula')
                        ->orderBy('apellidos')->limit(200)->get();

        return view('nichos.nicho-create', compact('bloques','fallecidos','socios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'codigo'    => 'required|string|max:50|unique:nichos,codigo,NULL,id,bloque_id,'.$request->bloque_id,
            'capacidad' => 'required|integer|min:1',
            'estado'    => 'required|in:disponible,ocupado,mantenimiento',

            // Opcionales (asignaciones pivot al crear)
            'fallecido.id'               => 'nullable|exists:fallecidos,id',
            'fallecido.posicion'         => 'nullable|integer|min:1',
            'fallecido.fecha_inhumacion' => 'nullable|date',
            'fallecido.fecha_exhumacion' => 'nullable|date|after_or_equal:fallecido.fecha_inhumacion',
            'fallecido.observacion'      => 'nullable|string|max:2000',

            'responsable.socio_id' => 'nullable|exists:socios,id',
            'responsable.rol'      => 'nullable|in:TITULAR,CO-TITULAR,RESPONSABLE',
            'responsable.desde'    => 'nullable|date',
            'responsable.hasta'    => 'nullable|date|after_or_equal:responsable.desde',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $nicho = Nicho::create([
                    'bloque_id'  => $request->bloque_id,
                    'codigo'     => $request->codigo,
                    'capacidad'  => $request->capacidad,
                    'estado'     => $request->estado,
                    'disponible' => $request->estado === 'disponible',
                    // si quitaste geom del form, esto quedará en null
                    'geom'       => $request->geom ? json_decode($request->geom, true) : null,
                    'qr_uuid'    => $request->qr_uuid,
                    'created_by' => auth()->id(),
                ]);

                if ($request->filled('fallecido.id')) {
                    $nicho->fallecidos()->attach($request->input('fallecido.id'), [
                        'posicion'         => (int)$request->input('fallecido.posicion', 1),
                        'fecha_inhumacion' => $request->input('fallecido.fecha_inhumacion'),
                        'fecha_exhumacion' => $request->input('fallecido.fecha_exhumacion'),
                        'observacion'      => $request->input('fallecido.observacion'),
                    ]);
                }

                if ($request->filled('responsable.socio_id')) {
                    $nicho->socios()->attach($request->input('responsable.socio_id'), [
                        'rol'   => $request->input('responsable.rol', 'TITULAR'),
                        'desde' => $request->input('responsable.desde'),
                        'hasta' => $request->input('responsable.hasta'),
                    ]);
                }
            });

            return redirect()->route('nichos.index')->with('success','Nicho creado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error','Error: '.$e->getMessage());
        }
    }

    public function show(Nicho $nicho)
    {
        $nicho->load('bloque');
        return view('nichos.nicho-show', compact('nicho'));
    }

    public function edit(Nicho $nicho)
    {
        $bloques = Bloque::orderBy('nombre')->get();
        return view('nichos.nicho-edit', compact('nicho','bloques'));
    }

    public function update(Request $request, Nicho $nicho)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'codigo'    => 'required|string|max:50|unique:nichos,codigo,'.$nicho->id.',id,bloque_id,'.$request->bloque_id,
            'capacidad' => 'required|integer|min:1',
            'estado'    => 'required|in:disponible,ocupado,mantenimiento',
        ]);

        try {
            $nicho->update([
                'bloque_id'  => $request->bloque_id,
                'codigo'     => $request->codigo,
                'capacidad'  => $request->capacidad,
                'estado'     => $request->estado,
                'disponible' => $request->estado === 'disponible',
                'geom'       => $request->geom ? json_decode($request->geom, true) : $nicho->geom,
                'qr_uuid'    => $request->qr_uuid,
            ]);

            return redirect()->route('nichos.index')->with('success','Nicho actualizado.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error','Error: '.$e->getMessage());
        }
    }

    public function destroy(Nicho $nicho)
    {
        try {
            $nicho->delete();
            return redirect()->route('nichos.index')->with('success','Nicho eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('nichos.index')->with('error','Error: '.$e->getMessage());
        }
    }
}

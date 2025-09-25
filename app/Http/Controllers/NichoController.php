<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nicho;
use App\Models\Bloque;

class NichoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $bloqueId = $request->get('bloque_id');

        $query = Nicho::with('bloque')->orderBy('codigo');

        if ($q !== '') {
            $query->where('codigo','ILIKE',"%{$q}%");
        }
        if ($bloqueId) {
            $query->where('bloque_id',$bloqueId);
        }

        $nichos = $query->paginate(10)->withQueryString();
        $bloques = Bloque::orderBy('nombre')->get();

        return view('nichos.nicho-index', compact('nichos','bloques','bloqueId','q'));
    }

    public function create()
    {
        $bloques = Bloque::orderBy('nombre')->get();
        return view('nichos.nicho-create', compact('bloques'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'codigo'    => 'required|string|max:50|unique:nichos,codigo,NULL,id,bloque_id,'.$request->bloque_id,
            'capacidad' => 'required|integer|min:1',
            'estado'    => 'required|in:disponible,ocupado,mantenimiento',
        ]);

        try {
            Nicho::create([
                'bloque_id'  => $request->bloque_id,
                'codigo'     => $request->codigo,
                'capacidad'  => $request->capacidad,
                'estado'     => $request->estado,
                'disponible' => $request->estado === 'disponible',
                'geom'       => $request->geom ? json_decode($request->geom,true) : null,
                'qr_uuid'    => $request->qr_uuid,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('nichos.index')->with('success','Nicho creado correctamente.');
        } catch(\Exception $e) {
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
                'geom'       => $request->geom ? json_decode($request->geom,true) : $nicho->geom,
                'qr_uuid'    => $request->qr_uuid,
            ]);

            return redirect()->route('nichos.index')->with('success','Nicho actualizado.');
        } catch(\Exception $e) {
            return back()->withInput()->with('error','Error: '.$e->getMessage());
        }
    }

    public function destroy(Nicho $nicho)
    {
        try {
            $nicho->delete();
            return redirect()->route('nichos.index')->with('success','Nicho eliminado.');
        } catch(\Exception $e) {
            return redirect()->route('nichos.index')->with('error','Error: '.$e->getMessage());
        }
    }
}
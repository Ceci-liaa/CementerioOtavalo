<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bloque;

class BloqueController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Bloque::orderBy('nombre')->orderBy('codigo');

        // Postgres: ILIKE | MySQL: LIKE
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('codigo', 'ILIKE', "%{$q}%")
                  ->orWhere('nombre', 'ILIKE', "%{$q}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$q}%");
            });
        }

        $bloques = $query->paginate(10)->withQueryString();

        return view('bloques.bloque-index', compact('bloques'));
    }

    public function create()
    {
        return view('bloques.bloque-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo'      => 'required|string|max:50|unique:bloques,codigo',
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'area'        => 'nullable|numeric|min:0',
            // geom opcional, puedes validar JSON
        ]);

        try {
            Bloque::create([
                'codigo'      => $request->codigo,
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'area'        => $request->area,
                'geom'        => $request->geom ? json_decode($request->geom, true) : null,
                'created_by'  => auth()->id(),
            ]);

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function show(Bloque $bloque)
    {
        // $bloque->load('nichos'); // cuando exista la relación
        return view('bloques.bloque-show', compact('bloque'));
    }

    public function edit(Bloque $bloque)
    {
        return view('bloques.bloque-edit', compact('bloque'));
    }

    public function update(Request $request, Bloque $bloque)
    {
        $request->validate([
            'codigo'      => 'required|string|max:50|unique:bloques,codigo,' . $bloque->id,
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'area'        => 'nullable|numeric|min:0',
        ]);

        try {
            $bloque->update([
                'codigo'      => $request->codigo,
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'area'        => $request->area,
                'geom'        => $request->geom ? json_decode($request->geom, true) : $bloque->geom,
            ]);

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Bloque $bloque)
    {
        try {
            $bloque->delete();
            return redirect()->route('bloques.index')->with('success', 'Bloque eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('bloques.index')->with('error', 'No se puede eliminar: '.$e->getMessage());
        }
    }
}

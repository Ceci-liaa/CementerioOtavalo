<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Canton;

class CantonController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        // 1. Ordenar por el código generado (CA001, CA002...)
        $query = Canton::orderBy('codigo', 'asc');

        if ($q !== '') {
            // 2. Búsqueda avanzada: busca por nombre O por código
            $query->where(function ($sub) use ($q) {
                // Detectar si es Postgres o MySQL para el operador (ILIKE vs LIKE)
                $operator = \DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

                $sub->where('nombre', $operator, "%{$q}%")
                    ->orWhere('codigo', $operator, "%{$q}%");
            });
        }

        $cantones = $query->paginate(10)->withQueryString();

        return view('cantones.canton-index', compact('cantones'));
    }

// --- CORREGIDO: CREATE ---
    public function create()
    {
        // Detectamos si es AJAX para decirle a la vista que es un Modal
        $isModal = request()->ajax();
        
        // Retornamos la vista (asegúrate que el archivo sea 'create.blade.php' en la carpeta 'cantones')
        return view('cantones.canton-create', compact('isModal'));
    }

    // --- CORREGIDO: EDIT ---
    public function edit(Canton $canton)
    {
        // Detectamos si es AJAX
        $isModal = request()->ajax();

        // Retornamos la vista con los datos del cantón
        return view('cantones.canton-edit', compact('canton', 'isModal'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cantones,nombre',
        ], [
            'nombre.unique' => 'El nombre del cantón ya existe.',
            'nombre.required' => 'El nombre es obligatorio.'
        ]);

        try {
            Canton::create([
                'nombre' => $request->nombre,
                // El código se genera automáticamente en el Modelo (boot)
            ]);
            return redirect()->route('cantones.index')->with('success', 'Cantón creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Canton $canton)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:cantones,nombre,' . $canton->id,
        ]);

        try {
            $canton->update([
                'nombre' => $request->nombre,
            ]);
            return redirect()->route('cantones.index')->with('success', 'Cantón actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Canton $canton)
    {
        try {
            $canton->delete();
            return redirect()->route('cantones.index')->with('success', 'Cantón eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('cantones.index')->with('error', 'No se puede eliminar: tiene parroquias asociadas.');
        }
    }

    // Show se mantiene igual por si quieres ver detalles
    public function show(Canton $canton)
    {
        $canton->load('parroquias');
        return view('cantones.canton-show', compact('canton')); // Asegúrate que esta vista exista si la usas
    }
}
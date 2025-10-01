<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));

        $query = Servicio::orderBy('nombre');

        if ($q !== '') {
            // Postgres: ILIKE | MySQL: LIKE
            $query->where(function($w) use ($q){
                $w->where('nombre','ILIKE',"%{$q}%")
                  ->orWhere('descripcion','ILIKE',"%{$q}%");
            });
        }

        $servicios = $query->paginate(10)->withQueryString();

        return view('servicios.servicio-index', compact('servicios','q'));
    }

    public function create()
    {
        return view('servicios.servicio-create-blade');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'valor'       => 'nullable|numeric',
        ]);

        Servicio::create($request->only(['nombre','descripcion','valor']));

        return redirect()->route('servicios.index')->with('success','Servicio creado correctamente.');
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

        $servicio->update($request->only(['nombre','descripcion','valor']));

        return redirect()->route('servicios.index')->with('success','Servicio actualizado.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('servicios.index')->with('success','Servicio eliminado.');
    }
}

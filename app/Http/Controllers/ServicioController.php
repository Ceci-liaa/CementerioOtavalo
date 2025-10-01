<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q',''));
        $categoria = $request->get('categoria','');

        $query = Servicio::orderBy('nombre');

        if ($q !== '') {
            // Postgres: ILIKE | MySQL: LIKE
            $query->where(function($w) use ($q){
                $w->where('nombre','ILIKE',"%{$q}%")
                  ->orWhere('descripcion','ILIKE',"%{$q}%")
                  ->orWhere('categoria','ILIKE',"%{$q}%");
            });
        }
        if ($categoria !== '') {
            $query->where('categoria',$categoria);
        }

        $servicios = $query->paginate(10)->withQueryString();

        return view('servicios.servicio-index-blade', compact('servicios','q','categoria'));
    }

    public function create()
    {
        return view('servicios.servicio-create-blade');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'descripcion'=> 'nullable|string',
            'valor'      => 'nullable|numeric',
            'categoria'  => 'nullable|string|max:50',
        ]);

        Servicio::create($request->only(['nombre','descripcion','valor','categoria']));

        return redirect()->route('servicios.index')->with('success','Servicio creado correctamente.');
    }

    public function show(Servicio $servicio)
    {
        return view('servicios.servicio-show-blade', compact('servicio'));
    }

    public function edit(Servicio $servicio)
    {
        return view('servicios.servicio-edit-blade', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'descripcion'=> 'nullable|string',
            'valor'      => 'nullable|numeric',
            'categoria'  => 'nullable|string|max:50',
        ]);

        $servicio->update($request->only(['nombre','descripcion','valor','categoria']));

        return redirect()->route('servicios.index')->with('success','Servicio actualizado.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('servicios.index')->with('success','Servicio eliminado.');
    }
}

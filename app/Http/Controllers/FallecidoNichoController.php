<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FallecidoNicho;

class FallecidoNichoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fallecido_id' => 'required|exists:fallecidos,id',
            'nicho_id'     => 'required|exists:nichos,id',
        ]);

        FallecidoNicho::create([
            'fallecido_id' => $request->fallecido_id,
            'nicho_id'     => $request->nicho_id,
            'fecha_ingreso'=> $request->fecha_ingreso ?? now(),
            'fecha_salida' => $request->fecha_salida,
            'observacion'  => $request->observacion,
        ]);

        return back()->with('success', 'Fallecido asignado a nicho.');
    }

    public function destroy(FallecidoNicho $fallecidoNicho)
    {
        $fallecidoNicho->delete();
        return back()->with('success', 'Asignación eliminada.');
    }
}
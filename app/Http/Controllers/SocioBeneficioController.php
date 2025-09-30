<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SocioBeneficio;

class SocioBeneficioController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'socio_id'     => 'required|exists:socios,id',
            'beneficio_id' => 'required|exists:beneficios,id',
        ]);

        SocioBeneficio::create([
            'socio_id'     => $request->socio_id,
            'beneficio_id' => $request->beneficio_id,
            'fecha_inicio' => $request->fecha_inicio ?? now(),
            'fecha_fin'    => $request->fecha_fin,
        ]);

        return back()->with('success', 'Beneficio asignado al socio.');
    }

    public function destroy(SocioBeneficio $socioBeneficio)
    {
        $socioBeneficio->delete();
        return back()->with('success', 'Asignación de beneficio eliminada.');
    }
}
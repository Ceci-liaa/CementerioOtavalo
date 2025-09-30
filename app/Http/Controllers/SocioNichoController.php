<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SocioNicho;
class SocioNichoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'socio_id' => 'required|exists:socios,id',
            'nicho_id' => 'required|exists:nichos,id',
        ]);

        SocioNicho::create([
            'socio_id' => $request->socio_id,
            'nicho_id' => $request->nicho_id,
            'rol'      => $request->rol ?? 'Titular',
            'desde'    => $request->desde,
            'hasta'    => $request->hasta,
            'observacion' => $request->observacion,
        ]);

        return back()->with('success', 'Nicho asignado al socio.');
    }

    public function destroy(SocioNicho $socioNicho)
    {
        $socioNicho->delete();
        return back()->with('success', 'Asignación eliminada.');
    }
}
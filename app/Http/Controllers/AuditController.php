<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        // 🔍 Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->input('fecha'));
        }

        // 🔍 Filtro por rango de fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('created_at', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $audits = $query->paginate(10)->appends($request->all()); // mantiene filtros en la paginación

        return view('audits.index', compact('audits'));
    }
}


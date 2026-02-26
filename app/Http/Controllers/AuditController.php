<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();

        // 🔍 Filtro por rango de fechas (tiene prioridad sobre fecha específica)
        if ($request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereBetween('created_at', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59',
                ]);
            }
            elseif ($request->filled('fecha_inicio')) {
                $query->where('created_at', '>=', $request->fecha_inicio . ' 00:00:00');
            }
            else {
                $query->where('created_at', '<=', $request->fecha_fin . ' 23:59:59');
            }
        }
        // 🔍 Filtro por fecha específica (solo si no se usó rango)
        elseif ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->input('fecha'));
        }

        $audits = $query->paginate(10)->appends($request->all()); // mantiene filtros en la paginación

        return view('audits.index', compact('audits'));
    }
}

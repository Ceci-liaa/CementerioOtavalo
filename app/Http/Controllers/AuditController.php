<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = trim($request->get('search', ''));
        $query = Audit::with('user')->latest();

        // 🔍 Filtro por búsqueda general (Usuario o Módulo)
        if ($searchTerm !== '') {
            $query->where(function ($w) use ($searchTerm) {
                // Buscar por nombre de usuario
                $w->whereHas('user', function ($sub) use ($searchTerm) {
                    $sub->where('name', 'ILIKE', "%{$searchTerm}%");
                })
                // Buscar por nombre del módulo (auditable_type)
                ->orWhere('auditable_type', 'ILIKE', "%{$searchTerm}%")
                // Buscar por evento (created, updated, deleted)
                ->orWhere('event', 'ILIKE', "%{$searchTerm}%")
                // Buscar por ID del registro (Casting a TEXT para PostgreSQL)
                ->orWhereRaw('CAST(auditable_id AS TEXT) ILIKE ?', ["%{$searchTerm}%"]);
            });
        }

        // 🔍 Filtro por rango de fechas
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
        
        // 🔍 Filtro por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->input('fecha'));
        }

        $audits = $query->paginate(10)->appends($request->all());

        if ($request->ajax()) {
            return view('audits.index', compact('audits'))->render();
        }

        return view('audits.index', compact('audits'));
    }
}

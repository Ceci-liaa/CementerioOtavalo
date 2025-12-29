<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Pago;
// use App\Models\Beneficio; // Descomenta si usas tu modelo Beneficio

class PagoController extends Controller
{
    public function index(Socio $socio)
    {
        $socio->load('pagos');
        $aniosPendientes = $socio->anios_deuda; // Usa la lógica del modelo Socio
        return view('pagos.index-modal', compact('socio', 'aniosPendientes'));
    }

    // Muestra el Modal con el BUSCADOR de socios para cobrar
    public function create(Request $request)
    {
        $search = trim($request->get('search', ''));
        
        $socios = \App\Models\Socio::query()
            ->orderBy('apellidos')
            ->orderBy('nombres');

        // ─── EL BUSCADOR BLINDADO (El mismo que te di antes) ───
        if ($search !== '') {
            $socios->where(function ($w) use ($search) {
                 $w->whereRaw("CAST(cedula AS TEXT) ILIKE ?", ["%{$search}%"]) // Busca cédula como texto
                   ->orWhere('codigo', 'ILIKE', "%{$search}%")
                   // Busca Nombre Completo unido
                   ->orWhereRaw("CONCAT(nombres, ' ', apellidos) ILIKE ?", ["%{$search}%"])
                   ->orWhereRaw("CONCAT(apellidos, ' ', nombres) ILIKE ?", ["%{$search}%"]);
            });
        }

        // Solo mostramos 5 para no llenar el modal, a menos que busque
        $resultados = $socios->paginate(5)->withQueryString();

        // Si es una petición AJAX (cuando escribes en el buscador), devolvemos solo la lista
        if ($request->ajax()) {
            return view('pagos.partials.lista-socios', compact('resultados'))->render();
        }

        return view('pagos.create', compact('resultados'));
    }
    public function store(Request $request, Socio $socio)
    {
        $request->validate([
            'anios_pagados' => 'required|array|min:1', // Debe marcar al menos uno
            'fecha_pago'    => 'required|date',
        ]);

        // ─── DEFINICIÓN DEL PRECIO AUTOMÁTICO ───
        
        // OPCIÓN A: Sacarlo de la BD (Si ya tienes el modelo Beneficio)
        // $rubro = \App\Models\Beneficio::where('nombre', 'Pago Anual')->first();
        // $precio = $rubro ? $rubro->monto : 0.00;
        
        // OPCIÓN B: Valor fijo (Úsalo para probar ya mismo)
        $precio = 25.00; 

        try {
            \DB::transaction(function () use ($request, $socio, $precio) {
                // Recorremos cada casilla marcada (Ej: 2023, 2024)
                foreach ($request->anios_pagados as $anio) {
                    
                    // Verificar si ya pagó ese año (Evitar error de duplicado)
                    $existe = Pago::where('socio_id', $socio->id)
                                  ->where('anio_pagado', $anio)
                                  ->exists();

                    if (!$existe) {
                        Pago::create([
                            'socio_id'    => $socio->id,
                            'anio_pagado' => $anio,
                            'monto'       => $precio, // Se guarda el precio automático
                            'fecha_pago'  => $request->fecha_pago,
                            'observacion' => $request->observacion,
                            'created_by'  => auth()->id(),
                        ]);
                    }
                }
            });

            return back()->with('success', 'Pagos registrados correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();
        return back()->with('success', 'Pago eliminado.');
    }

public function general(Request $request)
    {
        $search = trim($request->get('search', ''));

        // Consulta base ordenando por fecha reciente
        $query = Pago::with('socio')->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->whereHas('socio', function($q) use ($search) {
                $q->where('cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('nombres', 'ILIKE', "%{$search}%")
                  ->orWhere('apellidos', 'ILIKE', "%{$search}%")
                  // ESTA LÍNEA ES LA MAGIA: Une Nombre + Espacio + Apellido
                  ->orWhereRaw("nombres || ' ' || apellidos ILIKE ?", ["%{$search}%"]);
            });
        }

        $pagos = $query->paginate(15)->withQueryString();
        $totalRecaudado = Pago::sum('monto');

        return view('pagos.general', compact('pagos', 'totalRecaudado'));
    }
}
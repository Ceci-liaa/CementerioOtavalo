<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bloque;
use App\Models\BloqueGeom;

class BloqueController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Bloque::orderBy('nombre')->orderBy('codigo');

        // Postgres: ILIKE | MySQL: LIKE
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('codigo', 'ILIKE', "%{$q}%")
                  ->orWhere('nombre', 'ILIKE', "%{$q}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$q}%");
            });
        }

        $bloques = $query->paginate(10)->withQueryString();

        return view('bloques.bloque-index', compact('bloques'));
    }

    public function create()
    {
        // Trae las geometrías creadas en QGIS que aún no están enlazadas al sistema
        $bloquesGeom = BloqueGeom::unassigned()->select('id', 'nombre')->get();

        return view('bloques.bloque-create', compact('bloquesGeom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'codigo' no se valida aquí: se genera automáticamente en el modelo
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'area_m2'         => 'nullable|numeric|min:0',
            'bloque_geom_id'  => 'nullable|exists:bloques_geom,id',
            'geom'            => 'nullable|string', // GeoJSON opcional
        ]);

        DB::beginTransaction();
        try {
            $bloque = Bloque::create([
                // 'codigo' lo genera el model automaticamente si no lo envías
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'area_m2'        => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
                'created_by'     => auth()->id(),
            ]);

            // Si se envía GeoJSON desde el formulario, lo guardamos en la columna geom de bloques
            if ($request->filled('geom')) {
                DB::statement(
                    "UPDATE bloques SET geom = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?",
                    [$request->geom, $bloque->id]
                );
            } elseif ($request->filled('bloque_geom_id')) {
                // Si el usuario seleccionó una geometría existente (bloque_geom_id), copiarla al cuadro bloques.geom
                DB::statement(
                    "UPDATE bloques SET geom = (SELECT geom FROM bloques_geom WHERE id = ?) WHERE id = ?",
                    [$request->bloque_geom_id, $bloque->id]
                );
            }

            // Si no se proporcionó area_m2, intentar calcularla a partir de la geometría (en metros cuadrados)
            if (empty($bloque->area_m2)) {
                // Solo se calculará si la geom ya fue definida en la update anterior
                DB::statement(
                    "UPDATE bloques SET area_m2 = ST_Area(geom::geography) WHERE id = ? AND geom IS NOT NULL",
                    [$bloque->id]
                );
            }

            DB::commit();

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al crear: '.$e->getMessage());
        }
    }

    public function show(Bloque $bloque)
    {
        // Cargar relación con bloqueGeom si la necesitas en la vista
        $bloque->load('bloqueGeom', 'creador');

        return view('bloques.bloque-show', compact('bloque'));
    }

    public function edit(Bloque $bloque)
    {
        // Listar geometrías no asignadas + la geometría actualmente asignada (para permitir conservarla)
        $bloquesGeom = BloqueGeom::whereNotIn('id', function ($q) {
            $q->select('bloque_geom_id')->from('bloques')->whereNotNull('bloque_geom_id');
        })
        ->orWhere('id', $bloque->bloque_geom_id)
        ->select('id', 'nombre')
        ->get();

        return view('bloques.bloque-edit', compact('bloque', 'bloquesGeom'));
    }

    public function update(Request $request, Bloque $bloque)
    {
        $request->validate([
            // codigo se mantiene único (excluyendo el actual)
            'codigo'          => 'required|string|max:50|unique:bloques,codigo,' . $bloque->id,
            'nombre'          => 'required|string|max:150',
            'descripcion'     => 'nullable|string',
            'area_m2'         => 'nullable|numeric|min:0',
            'bloque_geom_id'  => 'nullable|exists:bloques_geom,id',
            'geom'            => 'nullable|string', // GeoJSON opcional
        ]);

        DB::beginTransaction();
        try {
            $bloque->update([
                'codigo'         => $request->codigo,
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'area_m2'        => $request->area_m2,
                'bloque_geom_id' => $request->bloque_geom_id,
            ]);

            // Preferencia: si envían GeoJSON, lo usamos; si no y se seleccionó bloque_geom_id, copiamos esa geometría
            if ($request->filled('geom')) {
                DB::statement(
                    "UPDATE bloques SET geom = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?",
                    [$request->geom, $bloque->id]
                );
            } elseif ($request->filled('bloque_geom_id')) {
                DB::statement(
                    "UPDATE bloques SET geom = (SELECT geom FROM bloques_geom WHERE id = ?) WHERE id = ?",
                    [$request->bloque_geom_id, $bloque->id]
                );
            }

            // Actualizar área si no fue provista explícitamente
            if (empty($request->area_m2)) {
                DB::statement(
                    "UPDATE bloques SET area_m2 = ST_Area(geom::geography) WHERE id = ? AND geom IS NOT NULL",
                    [$bloque->id]
                );
            }

            DB::commit();

            return redirect()->route('bloques.index')
                ->with('success', 'Bloque actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    public function destroy(Bloque $bloque)
    {
        try {
            $bloque->delete();
            return redirect()->route('bloques.index')->with('success', 'Bloque eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('bloques.index')->with('error', 'No se puede eliminar: '.$e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BloqueGeomController extends Controller
{
    public function geojson($id)
    {
        $row = DB::selectOne('SELECT ST_AsGeoJSON(geom) AS geojson FROM bloques_geom WHERE id = ?', [$id]);

        if (! $row || ! $row->geojson) {
            return response()->json(['error' => 'Geometría no encontrada'], 404);
        }

        return response()->json(json_decode($row->geojson, true));
    }
}
        
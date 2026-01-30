<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use OwenIt\Auditing\Contracts\Auditable;
class BloqueGeom extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'bloques_geom';
    protected $guarded = [];

    // Si tu tabla no tiene timestamps, pon esto a false
    public $timestamps = true;

    /**
     * Relación con la tabla 'bloques' (datos administrativos).
     * Un BloqueGeom puede tener 0 o 1 Bloque asociado.
     */
    public function bloque()
    {
        return $this->hasOne(Bloque::class, 'bloque_geom_id');
    }

    /**
     * Scope: filtra las geometrías que NO están asignadas a la tabla 'bloques'
     * Útil para mostrar en el select del formulario "crear bloque".
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNotIn('id', function ($q) {
            $q->select('bloque_geom_id')->from('bloques');
        });
    }

    /**
     * Devuelve la geometría como GeoJSON decodificado (array).
     * Realiza una consulta ST_AsGeoJSON en la BD.
     */
    public function getGeomGeojsonAttribute()
    {
        $row = DB::selectOne('SELECT ST_AsGeoJSON(geom) AS geojson FROM ' . $this->getTable() . ' WHERE id = ?', [$this->id]);

        return $row && $row->geojson ? json_decode($row->geojson, true) : null;
    }

    /**
     * Alias conveniente: ->geom_array devuelve lo mismo que geom_geojson
     */
    public function getGeomArrayAttribute()
    {
        return $this->geom_geojson;
    }

    /**
     * Genera una Feature GeoJSON (útil para enviar al frontend/Leaflet)
     */
    public function toGeoJsonFeature(): array
    {
        return [
            'type' => 'Feature',
            'id' => $this->id,
            'properties' => [
                'nombre' => $this->nombre,
            ],
            'geometry' => $this->geom_geojson,
        ];
    }

    /**
     * (Opcional) Crear un registro en bloques_geom desde un GeoJSON (string o array).
     * Útil si alguna vez quieres insertar geometrías desde Laravel en vez de QGIS.
     */
    public static function createFromGeoJSON(string $nombre, $geojson)
    {
        // Asegurar que llegue string JSON
        $geojsonStr = is_string($geojson) ? $geojson : json_encode($geojson);

        DB::statement(
            'INSERT INTO bloques_geom (nombre, geom, created_at, updated_at) VALUES (?, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326), now(), now())',
            [$nombre, $geojsonStr]
        );

        // Devolver el último creado (o puedes retornar el id usando RETURNING en PG si lo prefieres)
        return self::latest('id')->first();
    }
}

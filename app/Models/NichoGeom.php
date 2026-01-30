<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NichoGeom extends Model
{
    protected $table = 'nichos_geom'; // Asegúrate que coincida con tu tabla de BD
    protected $guarded = [];

    // Scope para filtrar los no asignados
    public function scopeUnassigned($query)
    {
        return $query->whereNotIn('id', function($q) {
            $q->select('nicho_geom_id')->from('nichos')->whereNotNull('nicho_geom_id')->whereNull('deleted_at');
        });
    }
}
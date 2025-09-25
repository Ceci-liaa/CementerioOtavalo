<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nicho extends Model
{
    use SoftDeletes;

    protected $table = 'nichos';
    protected $guarded = [];

    protected $casts = [
        'geom' => 'array',
        'disponible' => 'boolean',
    ];

    public function bloque() { return $this->belongsTo(Bloque::class); }
    public function creador() { return $this->belongsTo(User::class, 'created_by'); }

    // relaciones futuras: socioNicho, fallecidoNicho
}

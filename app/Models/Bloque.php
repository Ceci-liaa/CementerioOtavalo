<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloque extends Model
{
    use SoftDeletes;

    protected $table = 'bloques';
    protected $guarded = [];

    protected $casts = [
        'geom' => 'array',
        'area' => 'decimal:2',
    ];

    // Relaciones futuras
    // public function nichos() { return $this->hasMany(Nicho::class); }
    public function creador() { return $this->belongsTo(User::class, 'created_by'); }
}

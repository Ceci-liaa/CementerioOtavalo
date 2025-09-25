<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fallecido extends Model
{
    use SoftDeletes;

    protected $table = 'fallecidos';
    protected $guarded = [];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_fallecimiento' => 'date',
    ];

    // Relaciones
    public function comunidad()   { return $this->belongsTo(Comunidad::class); }
    public function genero()      { return $this->belongsTo(Genero::class); }
    public function estadoCivil() { return $this->belongsTo(EstadoCivil::class, 'estado_civil_id'); }
    public function creador()     { return $this->belongsTo(User::class, 'created_by'); }
}
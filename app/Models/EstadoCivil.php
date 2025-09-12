<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCivil extends Model
{
    protected $table = 'estados_civiles';
    public $timestamps = false;
    protected $guarded = [];

    public function socios()
    {
        return $this->hasMany(Socio::class, 'estado_civil_id');
    }
}
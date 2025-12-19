<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SocioNicho extends Pivot 
{
    protected $table = 'socio_nicho';
    public $incrementing = true; // IMPORTANTE: Tu tabla tiene columna 'id'

    protected $casts = [
        'desde' => 'date',
        'hasta' => 'date',
    ];
}
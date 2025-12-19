<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FallecidoNicho extends Pivot
{
    protected $table = 'fallecido_nicho';
    public $incrementing = true; // IMPORTANTE: Tu tabla tiene columna 'id'

    protected $casts = [
        'fecha_inhumacion' => 'date',
        'fecha_exhumacion' => 'date',
    ];
}
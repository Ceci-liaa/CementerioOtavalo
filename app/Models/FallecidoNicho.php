<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;
class FallecidoNicho extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'fallecido_nicho';
    public $incrementing = true; // IMPORTANTE: Tu tabla tiene columna 'id'

    protected $casts = [
        'fecha_inhumacion' => 'date',
        'fecha_exhumacion' => 'date',
    ];
}
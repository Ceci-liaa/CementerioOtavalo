<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use OwenIt\Auditing\Contracts\Auditable;

class SocioNicho extends Pivot implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'socio_nicho';
    public $incrementing = true;

    protected $casts = [
        'desde' => 'date',
        'hasta' => 'date',
    ];
}
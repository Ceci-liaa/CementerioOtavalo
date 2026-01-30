<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Pago extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'pagos';
    protected $guarded = []; // Permite guardar masivamente

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class);
    }
    public function recibo()
    {
        return $this->belongsTo(Recibo::class);
    }
}
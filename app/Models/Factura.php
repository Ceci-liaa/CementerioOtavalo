<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Factura extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'facturas';
    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date',
        'total' => 'decimal:2',
    ];

    // Relación (opcional) con socio
    public function socio()
    {
        return $this->belongsTo(Socio::class);
    }

    // Detalles
    public function detalles()
    {
        return $this->hasMany(FacturaDetalle::class);
    }
}

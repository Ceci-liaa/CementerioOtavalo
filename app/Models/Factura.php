<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
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

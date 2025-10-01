<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model
{
    protected $table = 'factura_detalles';
    protected $guarded = [];

    protected $casts = [
        'cantidad' => 'integer',
        'precio'   => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // ----- Relaciones -----
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function beneficio()
    {
        return $this->belongsTo(Beneficio::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    // ----- Helpers útiles -----

    /**
     * Nombre mostrado del ítem:
     * 1) usa el snapshot 'descripcion' si existe,
     * 2) si no, intenta desde beneficio/servicio.
     */
    public function getNombreItemAttribute(): string
    {
        if (!empty($this->descripcion)) {
            return $this->descripcion;
        }
        if ($this->beneficio) {
            return $this->beneficio->nombre;
        }
        if ($this->servicio) {
            return $this->servicio->nombre;
        }
        return 'Ítem';
    }

    /**
     * Tipo de ítem: BENEFICIO | SERVICIO | DESCONOCIDO
     */
    public function getTipoItemAttribute(): string
    {
        if ($this->beneficio_id) return 'BENEFICIO';
        if ($this->servicio_id)  return 'SERVICIO';
        return 'DESCONOCIDO';
    }
}

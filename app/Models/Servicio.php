<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Servicio extends Model implements Auditable
{
    use HasFactory; 
    use \OwenIt\Auditing\Auditable;

    protected $table = 'servicios';
    protected $guarded = [];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    /**
     * Boot del modelo para generar código automático
     */
    protected static function booted()
    {
        static::creating(function ($servicio) {
            // Buscamos el último ID para calcular el siguiente
            // Nota: Si borras el último registro, este número podría reutilizarse 
            // dependiendo de la lógica. Para algo simple, esto funciona bien.
            $ultimo = Servicio::latest('id')->first();
            $siguienteId = $ultimo ? $ultimo->id + 1 : 1;

            // Genera S001, S002, S0010, etc.
            $servicio->codigo = 'S' . str_pad($siguienteId, 3, '0', STR_PAD_LEFT);
        });
    }
}
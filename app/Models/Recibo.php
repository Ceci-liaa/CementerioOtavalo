<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $guarded = [];
    protected $casts = ['fecha_pago' => 'date'];

    // Relación con el Socio
    public function socio() {
        return $this->belongsTo(Socio::class);
    }

    // Relación con los Pagos individuales (Años)
    public function pagos() {
        return $this->hasMany(Pago::class);
    }

    // TRUCO: Un atributo virtual para mostrar los años bonitos en la tabla
    // Ejemplo: Devuelve "2021, 2022, 2023"
    public function getAniosDescAttribute()
    {
        return $this->pagos->pluck('anio_pagado')->sort()->implode(', ');
    }
}
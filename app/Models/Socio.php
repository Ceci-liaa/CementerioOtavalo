<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Pago;
use OwenIt\Auditing\Contracts\Auditable;

class Socio extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'socios';
    protected $guarded = []; // Esto permite los nuevos campos automáticamente

    protected $casts = [
        'fecha_nac' => 'date',
        'fecha_inscripcion' => 'date',
        'fecha_exoneracion' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($socio) {
            $ultimoId = Socio::max('id') ?? 0;
            $nuevoId = $ultimoId + 1;
            $socio->codigo = 'SO-' . str_pad($nuevoId, 2, '0', STR_PAD_LEFT);
            
            $socio->created_by = auth()->id() ?? 1;

        });
    }
    public function getEdadAttribute()
    {
        if (!$this->fecha_nac) return 'N/A';
        return \Carbon\Carbon::parse($this->fecha_nac)->age;
    }

    public function getAniosInscritoAttribute()
    {
        if (!$this->fecha_inscripcion) return 0;
        return \Carbon\Carbon::parse($this->fecha_inscripcion)->age;
    }

    public function comunidad()
    {
        return $this->belongsTo(Comunidad::class);
    }
    public function genero()
    {
        return $this->belongsTo(Genero::class);
    }
    public function estadoCivil()
    {
        return $this->belongsTo(EstadoCivil::class, 'estado_civil_id');
    }
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function nichos()
    // {
    //     return $this->belongsToMany(Nicho::class, 'socio_nicho')
    //         ->using(SocioNicho::class)
    //         ->withPivot('rol', 'desde', 'hasta')
    //         ->withTimestamps();
    // }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->apellidos ?? '') . ' ' . ($this->nombres ?? ''));
    }

    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '')
            return $q;
        return $q->where(function ($qq) use ($term) {
            $qq->where('cedula', 'LIKE', "%{$term}%")
                ->orWhere('nombres', 'LIKE', "%{$term}%")
                ->orWhere('apellidos', 'LIKE', "%{$term}%")
                ->orWhere('codigo', 'LIKE', "%{$term}%")
                ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$term}%"])
                ->orWhereRaw("CONCAT(apellidos, ' ', nombres) LIKE ?", ["%{$term}%"]);
        });
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class)->orderBy('anio_pagado', 'desc');
    }

    public function getAniosDeudaAttribute()
    {
        if (!$this->fecha_inscripcion)
            return [];

        $anioInicio = \Carbon\Carbon::parse($this->fecha_inscripcion)->year;
        $anioActual = now()->year;

        $aniosDebidos = range($anioInicio, $anioActual);
        $aniosPagados = $this->pagos->pluck('anio_pagado')->toArray();

        return array_values(array_diff($aniosDebidos, $aniosPagados));
    }

    /**
     * Calcula el precio anual basado en el beneficio y la edad (para exonerados).
     */
    public function getPrecioParaAnio($anio)
    {
        if ($this->tipo_beneficio === 'sin_subsidio') return 10.00;
        if ($this->tipo_beneficio === 'con_subsidio') return 5.00;
        
        if ($this->tipo_beneficio === 'exonerado') {
            if (!$this->fecha_nac) return 5.00; // Por defecto si no hay fecha, se asume subsidio previo
            
            $fechaNac = \Carbon\Carbon::parse($this->fecha_nac);
            $anio75 = $fechaNac->year + 75;
            
            // Si el año a pagar es menor o igual al año en que cumplió 75, paga $5
            if ($anio <= $anio75) return 5.00;
            
            // A partir del año que cumple 75, paga $0
            return 0.00;
        }

        return 10.00; // Valor por defecto
    }

    public function nichos()
    {
        // Relación directa: Un socio tiene muchos nichos asociados a su ID
        return $this->hasMany(Nicho::class, 'socio_id');
    }
}
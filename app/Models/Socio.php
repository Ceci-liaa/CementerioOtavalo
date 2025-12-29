<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon; // Importante para calcular la edad
use App\Models\Pago;


class Socio extends Model
{
    use SoftDeletes;

    protected $table = 'socios';
    protected $guarded = [];

    protected $casts = [
        'fecha_nac' => 'date',
        'fecha_inscripcion' => 'date', // Nuevo casting
        'fecha_exoneracion' => 'date', // Nuevo casting
        'es_representante' => 'boolean',
    ];

    // ── Generación de Código Automático (SOC0001) ──────────────────
    protected static function booted()
    {
        static::creating(function ($socio) {
            $ultimoId = Socio::max('id') ?? 0;
            $nuevoId = $ultimoId + 1;
            $socio->codigo = 'SOC' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Accesor para LA EDAD (Requerimiento del Index) ─────────────
    // Se usa como $socio->edad en la vista
    public function getEdadAttribute()
    {
        return $this->fecha_nac ? $this->fecha_nac->age : 'N/A';
    }

    // ── Accesor para Antigüedad (Opcional, útil para reportes) ─────
    public function getAniosInscritoAttribute()
    {
        return $this->fecha_inscripcion ? $this->fecha_inscripcion->age : 0;
    }

    // ── Relaciones ─────────────────────────────────────────────
    public function comunidad()     { return $this->belongsTo(Comunidad::class); }
    public function genero()        { return $this->belongsTo(Genero::class); }
    public function estadoCivil()   { return $this->belongsTo(EstadoCivil::class, 'estado_civil_id'); }
    public function creador()       { return $this->belongsTo(User::class, 'created_by'); }

    public function nichos()
    {
        return $this->belongsToMany(Nicho::class, 'socio_nicho')
                    ->using(SocioNicho::class)
                    ->withPivot('rol', 'desde', 'hasta')
                    ->withTimestamps();
    }
    
    // ... tus otros scopes y accesors existentes ...
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->apellidos ?? '').' '.($this->nombres ?? ''));
    }
    
    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string)$term);
        if ($term === '') return $q;
        return $q->where(function ($qq) use ($term) {
            $qq->where('cedula', 'ILIKE', "%{$term}%")
               ->orWhere('nombres', 'ILIKE', "%{$term}%")
               ->orWhere('apellidos', 'ILIKE', "%{$term}%")
               ->orWhere('codigo', 'ILIKE', "%{$term}%");
        });
    }

    // Relación con sus pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class)->orderBy('anio_pagado', 'desc');
    }

    // LÓGICA AUTOMÁTICA DE DEUDA
    public function getAniosDeudaAttribute()
    {
        // Si no tiene fecha de inscripción, no debe nada (o asumimos año actual)
        if (!$this->fecha_inscripcion) return [];

        $anioInicio = $this->fecha_inscripcion->year;
        $anioActual = now()->year;
        
        // 1. Lista de todos los años que DEBERÍA haber pagado desde que entró
        $aniosDebidos = range($anioInicio, $anioActual);

        // 2. Lista de años que YA pagó (sacados de la base de datos)
        $aniosPagados = $this->pagos->pluck('anio_pagado')->toArray();

        // 3. La resta son los pendientes
        return array_values(array_diff($aniosDebidos, $aniosPagados));
    }
}
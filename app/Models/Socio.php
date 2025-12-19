<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Socio extends Model
{
    use SoftDeletes;

    protected $table = 'socios';
    protected $guarded = [];

    protected $casts = [
        'fecha_nac' => 'date',
        'es_representante' => 'boolean',
    ];


    // ── Generación de Código Automático (SOC0001) ──────────────────
    protected static function booted()
    {
        static::creating(function ($socio) {
            // 1. Obtenemos el ID más alto actual
            $ultimoId = Socio::max('id') ?? 0;
            
            // 2. Sumamos 1 para el nuevo ID
            $nuevoId = $ultimoId + 1;
            
            // 3. Generamos el código: SOC + el número relleno con ceros a la izquierda (4 cifras)
            // Ejemplo: Si el ID es 5, genera SOC0005. Si es 23, genera SOC0023.
            $socio->codigo = 'SOC' . str_pad($nuevoId, 4, '0', STR_PAD_LEFT);
        });
    }

    // ── Relaciones ─────────────────────────────────────────────
    public function comunidad()     { return $this->belongsTo(Comunidad::class); }
    public function genero()        { return $this->belongsTo(Genero::class); }
    public function estadoCivil()   { return $this->belongsTo(EstadoCivil::class, 'estado_civil_id'); }
    public function creador()       { return $this->belongsTo(User::class, 'created_by'); }

    // ── Accesor de nombre completo ─────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->apellidos ?? '').' '.($this->nombres ?? ''));
    }

    // ── Scope búsqueda simple ──────────────────────────────────
    public function scopeBuscar($q, ?string $term)
    {
        $term = trim((string)$term);
        if ($term === '') return $q;

        // Postgres: ILIKE — si usas MySQL cambia a LIKE
        return $q->where(function ($qq) use ($term) {
            $qq->where('cedula', 'ILIKE', "%{$term}%")
               ->orWhere('nombres', 'ILIKE', "%{$term}%")
               ->orWhere('apellidos', 'ILIKE', "%{$term}%");
        });
    }
    public function nichos()
    {
        return $this->belongsToMany(Nicho::class, 'socio_nicho')
                    ->using(SocioNicho::class)
                    ->withPivot('rol', 'desde', 'hasta')
                    ->withTimestamps();
    }
}

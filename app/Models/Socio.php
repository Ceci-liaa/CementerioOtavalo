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
}

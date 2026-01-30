<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Parroquia extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'parroquias';
    
    // 'codigo' debe estar en fillable para que se guarde
    protected $fillable = ['codigo', 'canton_id', 'nombre']; 

    /**
     * BOOT: Lógica automática al crear (Igual que en User)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($parroquia) {
            // 1. Obtenemos el último ID (max id) para evitar saltos si borras registros
            // Si no hay registros, empieza en 0.
            $ultimoId = Parroquia::max('id') ?? 0;
            
            // 2. Calculamos el siguiente ID
            $siguienteId = $ultimoId + 1;

            // 3. Generamos el string: PAR + 3 dígitos (001, 002, 010, etc.)
            // str_pad rellena con ceros a la izquierda
            $parroquia->codigo = 'PAR' . str_pad($siguienteId, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relaciones
    public function canton()
    {
        return $this->belongsTo(Canton::class);
    }
    
    public function comunidades()
    {
        return $this->hasMany(Comunidad::class);
    }
}
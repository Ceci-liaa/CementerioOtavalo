<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class Genero extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'generos';   // nombre real de la tabla
    public $timestamps = false;     // si tu tabla no tiene created_at/updated_at
    protected $guarded = [];

    public function socios()
    {
        return $this->hasMany(Socio::class);
    }
}
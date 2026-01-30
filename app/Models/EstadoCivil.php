<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class EstadoCivil extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'estados_civiles';
    public $timestamps = false;
    protected $guarded = [];

    public function socios()
    {
        return $this->hasMany(Socio::class, 'estado_civil_id');
    }
}
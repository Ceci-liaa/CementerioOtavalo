<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class SocioBeneficio extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'socio_beneficio';
    protected $guarded = [];

    public function socio()    { return $this->belongsTo(Socio::class); }
    public function beneficio(){ return $this->belongsTo(Beneficio::class); }
}
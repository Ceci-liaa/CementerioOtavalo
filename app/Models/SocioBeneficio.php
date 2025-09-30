<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocioBeneficio extends Model
{
    protected $table = 'socio_beneficio';
    protected $guarded = [];

    public function socio()    { return $this->belongsTo(Socio::class); }
    public function beneficio(){ return $this->belongsTo(Beneficio::class); }
}
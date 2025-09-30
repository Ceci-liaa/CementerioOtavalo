<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocioNicho extends Model
{
    protected $table = 'socio_nicho';
    protected $guarded = [];

    public function socio()   { return $this->belongsTo(Socio::class); }
    public function nicho()   { return $this->belongsTo(Nicho::class); }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FallecidoNicho extends Model
{
    protected $table = 'fallecido_nicho';
    protected $guarded = [];

    public function fallecido() { return $this->belongsTo(Fallecido::class); }
    public function nicho()     { return $this->belongsTo(Nicho::class); }
}
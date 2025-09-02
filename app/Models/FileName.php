<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FileName extends Model implements AuditableContract
{
    use HasFactory, AuditableTrait;

    protected $table = 'file_names';
    protected $fillable = ['name', 'activo'];
    protected $auditExclude = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

// Laravel Auditing
// use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
// use OwenIt\Auditing\Auditable as AuditableTrait;


class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Accessor: leer status como boolean PHP
    public function getStatusAttribute($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    // Mutator: escribir status como string 'true'/'false' para PostgreSQL
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    // Mutador para generar codigo_usuario
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Genera el código de usuario basado en el ID, como 'US0001', 'US0002', etc.
            $user->codigo_usuario = 'US-' . str_pad(User::count() + 1, 2, '0', STR_PAD_LEFT);
        });
    }

}

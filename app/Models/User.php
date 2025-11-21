<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// Laravel Auditing
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Mutador para generar codigo_usuario
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Genera el código de usuario basado en el ID, como 'US0001', 'US0002', etc.
            $user->codigo_usuario = 'US' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}

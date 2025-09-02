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


class User extends Authenticatable implements AuditableContract
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, AuditableTrait;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

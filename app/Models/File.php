<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class File extends Model implements AuditableContract
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'file_name_id',
        'prefix',
        'suffix',
        'name_original',
        'name_stored',
        'type',
        'folder_id',
        'user_id',
    ];

    protected $auditExclude = [];

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $parts = [];

        if ($this->prefix) $parts[] = $this->prefix;
        if ($this->file_name) $parts[] = $this->file_name->name;
        if ($this->suffix) $parts[] = $this->suffix;

        return implode(' ', $parts) . '.' . strtolower($this->type);
    }

    public function getFullPathAttribute()
    {
        $folder = $this->folder;
        $path = [];

        while ($folder) {
            array_unshift($path, $folder->name);
            $folder = $folder->parent;
        }

        return count($path) === 1 ? $path[0] : implode('\\', $path);
    }

    // Relaciones
    public function file_name()
    {
        return $this->belongsTo(FileName::class, 'file_name_id');
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

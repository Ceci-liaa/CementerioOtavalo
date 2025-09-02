<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Folder extends Model implements AuditableContract
{
    use HasFactory, AuditableTrait;

    protected $fillable = ['name', 'parent_id', 'user_id'];
    protected $auditExclude = [];

    // Relaciones
    public function subfolders()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Funciones de navegación
    public function getAncestors()
    {
        $ancestors = collect();
        $folder = $this;

        while ($folder->parent) {
            $ancestors->prepend($folder->parent);
            $folder = $folder->parent;
        }

        return $ancestors;
    }

    public function isChild($folderId)
    {
        $parent = $this->parent;

        while ($parent) {
            if ($parent->id == $folderId) return true;
            $parent = $parent->parent;
        }

        return false;
    }

    public function getFullPathAttribute()
    {
        $folder = $this;
        $path = [];

        while ($folder) {
            array_unshift($path, $folder->name);
            $folder = $folder->parent;
        }

        return implode('\\', $path);
    }

    public function subfoldersRecursive()
    {
        return $this->subfolders()->with('subfoldersRecursive');
    }

    public function isDescendantOf($folderId)
    {
        $parent = $this->parent;
        while ($parent) {
            if ($parent->id == $folderId) return true;
            $parent = $parent->parent;
        }
        return false;
    }

    public function hasDescendant($folderId)
    {
        foreach ($this->subfoldersRecursive as $child) {
            if ($child->id == $folderId || $child->hasDescendant($folderId)) {
                return true;
            }
        }
        return false;
    }

    public function getNombreCompletoAttribute()
    {
        return trim(
            ($this->prefix ? $this->prefix . ' ' : '') .
            ($this->file_name->name ?? '') .
            ($this->suffix ? ' ' . $this->suffix : '')
        );
    }
}

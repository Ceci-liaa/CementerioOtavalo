<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionManagerController extends Controller
{
    public function index()
    {
        // Si manejas varios guards, puedes filtrar por el que uses (normalmente 'web')
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Mapa: role_id => [permission_id, ...] para marcar checks
        $rolePerms = [];
        foreach ($roles as $role) {
            $rolePerms[$role->id] = $role->permissions->pluck('id')->all();
        }

        // ✅ Usa SIEMPRE esta vista (coincide con la ruta): resources/views/roles/permissions-manager.blade.php
        return view('roles.role-permissions-manager', compact('roles', 'permissions', 'rolePerms'));
    }

    public function update(Request $request)
    {
        // Matriz: permission_role[permission_id][role_id] = "on"
        $matrix = $request->input('permission_role', []);

        $roles = Role::all();
        $locked = ['Administrador']; // ⛔ roles bloqueados

        foreach ($roles as $role) {
            if (in_array($role->name, $locked)) {
                continue; // no tocar la columna del Admin
            }

            $newPermIds = [];
            foreach ($matrix as $permId => $byRole) {
                if (isset($byRole[$role->id])) {
                    $newPermIds[] = (int) $permId;
                }
            }
            $role->syncPermissions($newPermIds);
        }

        // ✅ Asegura que el Admin tenga TODOS los permisos
        if ($admin = Role::where('name', 'Administrador')->first()) {
            $admin->syncPermissions(Permission::all());
        }

        // Limpiar caché de spatie/permission
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return back()->with('ok', 'Permisos actualizados.');
    }
}

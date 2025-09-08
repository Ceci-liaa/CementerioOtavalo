<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    // Asegúrate de proteger estas rutas con middleware en web.php:
    // ->middleware(['auth','role:Administrador'])

    public function edit(Role $role)
    {
        // 1) Solo permisos del mismo guard que el rol
        $perms = Permission::where('guard_name', $role->guard_name ?? 'web')
            ->orderBy('name')
            ->get();

        // 2) Agrupa por la última palabra del nombre (p.ej. "crear carpeta" => "carpeta")
        $permissions = $perms->groupBy(function ($p) {
            $parts = preg_split('/\s+/', trim($p->name));
            return strtolower(end($parts)); // carpeta, archivo, usuario, formato, auditoria...
        });

        $rolePermissionIds = $role->permissions()->pluck('id')->toArray();

        return view('roles.role-permissions', compact('role','permissions','rolePermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        // (Opcional) no permitir editar permisos del rol Administrador
        // if ($role->name === 'Administrador') {
        //     return back()->with('error', 'No se pueden modificar los permisos del rol Administrador.');
        // }

        // 3) Validación fuerte
        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        // 4) Sincroniza (si no envían nada, deja vacío)
        $role->syncPermissions($validated['permissions'] ?? []);

        // 5) Limpia caché del paquete (bien hacerlo)
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        return redirect()->route('roles.index')->with('ok','Permisos actualizados.');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles base
        $roles = ['Administrador', 'Auditor', 'Usuario'];
        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name], ['guard_name' => 'web']);
        }

        // Permisos base (cementerio)
        $permissions = [
            'crear carpeta',
            'editar carpeta',
            'eliminar carpeta',
            'ver carpeta',
            'subir archivo',
            'editar archivo',
            'eliminar archivo',
            'ver archivo',
            'editar usuario',
            'eliminar usuario',
            'ver usuario',
            'crear formato',
            'editar formato',
            'eliminar formato',
            'ver formato',
            'ver auditoria',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p], ['guard_name' => 'web']);
        }

        // ✅ Admin: todos los permisos
        $adminRole = Role::findByName('Administrador');
        $adminRole->syncPermissions(Permission::all());

        // Usuario: permisos específicos
        $userRole = Role::findByName('Usuario');
        $userRole->syncPermissions([
            'subir archivo', 'editar archivo', 'eliminar archivo', 'ver archivo', 'ver formato'
        ]);

        // Auditor: ver auditoría
        $auditorRole = Role::findByName('Auditor');
        $auditorRole->syncPermissions(['ver auditoria']);

        // Limpiar caché del paquete
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }
}

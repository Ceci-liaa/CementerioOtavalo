<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Definir Roles iniciales
        $roles = ['Administrador', 'Auditor', 'Usuario'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Definir Permisos iniciales
        $permissions = [
            'crear carpeta',
            'editar carpeta',
            'eliminar carpeta',
            'ver carepeta',
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

        foreach ($permissions as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Asignar permisos al rol Administrador
        $adminRole = Role::findByName('Administrador');
        $adminRole->syncPermissions($permissions);

        // Asignar permisos al rol Usuario
        // Se le asignan permisos específicos al rol Usuario
        $userRole = Role::findByName('Usuario');
        $userRole->syncPermissions(['subir archivo', 'editar archivo', 'eliminar archivo','ver archivo','ver archivo', 'ver formato']);

        // Al rol Auditor se le asigna solo el permiso de ver auditoria
        $auditorRole = Role::findByName('Auditor');
        $auditorRole->syncPermissions(['ver auditoria']);
    }
}

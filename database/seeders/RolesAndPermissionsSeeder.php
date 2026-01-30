<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; 
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =================================================================
        // DEFINICIÓN DE PERMISOS (El Menú de Opciones)
        // =================================================================
        
        // 1. GESTIÓN DE USUARIOS (Administrar a otros)
        $this->createCrudWithReportAndCustom('usuario', ['cambiar estado']); 
        
        // 2. ROLES Y PERMISOS
        $this->createCrud('rol');
        $this->createPermission('gestionar permisos'); 

        // 3. UBICACIÓN
        $this->createCrudWithReport('canton');
        $this->createCrudWithReport('parroquia');
        $this->createCrudWithReport('comunidad');

        // 4. PERSONAS
        $this->createCrudWithReport('socio');
        $this->createCrudWithReport('fallecido');

        // 5. CEMENTERIO
        $this->createCrudWithReport('bloque');
        $this->createCrudWithReport('servicio');
        $this->createCrudWithReport('beneficio');
        $this->createCrudWithReport('nicho');
        $this->createPermission('ver qr nicho'); 

        // 6. ASIGNACIONES (Operativo)
        $this->createCrudWithReport('asignacion');
        $this->createPermission('exhumar cuerpo');
        $this->createPermission('generar certificado');

        // 7. FINANCIERO
        // Pagos
        $this->createPermission('ver pago');
        $this->createPermission('crear pago');
        $this->createPermission('editar pago');
        $this->createPermission('eliminar pago');
        $this->createPermission('ver historial socio');
        // Facturas
        $this->createCrud('factura'); 
        $this->createPermission('emitir factura');
        $this->createPermission('anular factura');
        $this->createPermission('descargar factura');

        // 8. AUDITORÍA
        $this->createPermission('ver auditoria');

        // 9. MI PERFIL (¡ESTO FALTABA!)
        // Estos son los permisos para que un usuario vea SU propio perfil
        $this->createPermission('ver perfil');
        $this->createPermission('editar perfil');


        // =================================================================
        // ASIGNACIÓN DE ROLES
        // =================================================================

        // 1. ADMINISTRADOR
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        // 2. AUDITOR
        $auditor = Role::firstOrCreate(['name' => 'Auditor']);
        $auditor->syncPermissions(['ver auditoria', 'ver perfil', 'editar perfil']);

        // 3. USUARIO (Rol Básico)
        $usuario = Role::firstOrCreate(['name' => 'Usuario']);
        
        // AHORA SÍ FUNCIONARÁ: Asignamos los permisos que acabamos de crear arriba
        $usuario->syncPermissions([
            'ver perfil', 
            'editar perfil'
        ]);
    }

    // --- FUNCIONES DE AYUDA ---

    private function createCrud($modulo) {
        foreach (['ver', 'crear', 'editar', 'eliminar'] as $action) {
            $this->createPermission("$action $modulo");
        }
    }

    private function createCrudWithReport($modulo) {
        $this->createCrud($modulo);
        $this->createPermission("reportar $modulo");
    }

    private function createCrudWithReportAndCustom($modulo, $customs = []) {
        $this->createCrudWithReport($modulo);
        foreach ($customs as $custom) {
            $this->createPermission("$custom $modulo");
        }
    }

    private function createPermission($name) {
        // Usamos firstOrCreate para evitar duplicados si corres el seeder varias veces
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }
}
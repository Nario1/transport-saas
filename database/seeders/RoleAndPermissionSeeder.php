<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. DEFINICIÓN DE PERMISOS (Sincronizados con Sidebar y Web.php)
        $permissions = [
            'ver dashboard',
            'ver vehiculos',
            'ver conductores',
            'ver propietarios',
            'ver rutas',
            'ver vueltas',
            'ver tributos',   
            'ver sanciones',
            'ver reportes',
            'gestionar usuarios',
            'gestionar roles',
            'gestionar empresas', 
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. CREACIÓN DE ROLES
        $superAdminRole = Role::updateOrCreate(['name' => 'SUPER_ADMIN', 'guard_name' => 'web']);
        $adminRole      = Role::updateOrCreate(['name' => 'ADMIN', 'guard_name' => 'web']);
        $operatorRole   = Role::updateOrCreate(['name' => 'OPERADOR', 'guard_name' => 'web']);

        // 3. ASIGNACIÓN DE PERMISOS AL ADMIN (DUEÑO DE EMPRESA)
        $adminRole->syncPermissions([
            'ver dashboard',
            'ver vehiculos',
            'ver conductores',
            'ver propietarios',
            'ver rutas',
            'ver vueltas',
            'ver tributos',
            'ver sanciones',
            'ver reportes',
            'gestionar usuarios',
            'gestionar roles'
        ]);

        // 4. CREACIÓN DE TU EMPRESA MATRIZ (Campos corregidos según tu tabla)
        $empresaMatriz = Empresa::updateOrCreate(['ruc' => '20000000000'], [
            'nombre'         => 'SaaS TransJunín',
            'razon_social'   => 'German Reyes SaaS Solutions',
            'telefono'       => '999888777',
            'direccion'      => 'Huancayo, Perú',
            'plan'           => 'enterprise',
            'activa'         => true,
            'tributo_diario' => 0.00 // Nombre de columna corregido
        ]);

        // 5. TU USUARIO MAESTRO
        $user = User::updateOrCreate(['email' => 'superadmin@transjunin.com'], [
            'empresa_id' => $empresaMatriz->id,
            'name'       => 'German Reyes',
            'password'   => Hash::make('password'),
            'activo'     => true
        ]);
        
        $user->syncRoles($superAdminRole);

        $this->command->info('Seeder ejecutado con éxito. Empresa matriz creada con campos correctos.');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permisos, roles globales y usuario SUPER_ADMIN
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Empresa demo, admin, operador y conductor de prueba
        $this->call(AdminSeeder::class);

        // 3. Limpieza: Asegurar que todos los ADMIN de cualquier empresa tengan el permiso de ajustes
        \Spatie\Permission\Models\Role::where('name', 'like', 'e%_ADMIN')->get()->each(function($role) {
            $role->givePermissionTo('gestionar ajustes de empresa');
        });

        // 4. Cargar datos de prueba estandarizados (20 registros, 2 rutas)
        $this->call(TestDataSeeder::class);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Este archivo es el "director de orquesta", solo llama al otro.
        $this->call(RoleAndPermissionSeeder::class);
    }
}
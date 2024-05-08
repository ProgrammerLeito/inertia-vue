<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'Crear Categoria', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 2, 'name' => 'Acciones Productos', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 3, 'name' => 'Acciones Entradas', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 4, 'name' => 'Ingresar Salida', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 5, 'name' => 'Ingresar Entrada', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 6, 'name' => 'Acciones Categorias', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 7, 'name' => 'Acciones Administrador', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 8, 'name' => 'Acciones Salidas', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45'],
            ['id' => 9, 'name' => 'Crear Producto', 'guard_name' => 'web', 'created_at' => '2024-04-23 03:59:57', 'updated_at' => '2024-04-25 06:41:45']
        ]);
    }
}

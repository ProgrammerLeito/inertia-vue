<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'id' => '1',
            'name' => 'Insumos',
        ]);
        Category::create([
            'id' => '2',
            'name' => 'Accesorios',
        ]);
        Category::create([
            'id' => '3',
            'name' => 'Materiales',
        ]);
        Category::create([
            'id' => '4',
            'name' => 'Herramientas',
        ]);
        Category::create([
            'id' => '5',
            'name' => 'Productos en Venta',
        ]);
        Category::create([
            'id' => '6',
            'name' => 'Indumentaria',
        ]);
        Category::create([
            'id' => '7',
            'name' => 'Materiales de Oficina y de Limpieza',
        ]);
    }
}

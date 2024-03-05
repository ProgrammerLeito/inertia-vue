<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Producto::create([
            'id' => '1',
            'insumo' => 'Discos de Corte',
            'marca' => 'CAT',
            'modelo' => 'X9342',
            'cantidad' => 10,
            'unidad_medida' => 'und',
            'fecha' => '2024-01-25',
            'comprador' => 'Franco',
            'comentario' => 'en buen estado',
            'stock' => 10,
            'ultima_entrada' => 10,
            'precio' => 'S/ 15.00',
            'category_id' => '1',
        ]);
    }
}

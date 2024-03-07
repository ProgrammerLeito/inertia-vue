<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Salida;

class SalidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Salida::create([
            'id' => '1',
            'empresa' => 'Sakana',
            'unidad_salida' => '1',
            'comentario_salida' => 'Ninguno',
            'tecnico' => '1',
            'fecha' => '2024-01-25',
            'producto_id' => '1',
        ]);
    }
}
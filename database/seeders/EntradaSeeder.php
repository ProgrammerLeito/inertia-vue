<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entrada;

class EntradaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entrada::create([
            'id' => '1',
            'cantidad' => 5,
            'fecha' => '2024-01-25',
            'producto_id' => '1',
        ]);
    }
}

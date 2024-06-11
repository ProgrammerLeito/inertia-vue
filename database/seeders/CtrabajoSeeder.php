<?php

namespace Database\Seeders;

use App\Models\Ctrabajo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CtrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            'Baterias',
            'Indicadores',
            'Fuentes de Alimentacion',
            'Celdas de Carga',
            'Cajas de Paso',
            'Estructura',
            'Calibracion y Certificacion de Balanzas',
            'Importes',
            'Pesas',
            'Otros',
            'Camaras',
            'Termomentros',
            'Plataforma',
            'Mantenimiento',
            'Impresoras',
            'Materiales Diversos',
            'Balanzas Mecanicas',
            'Calibracion y Certificacion de Pesas',
            'Calibracion y Certificacion de Termometros',
            'Estocas',
            'Sistemas'
        ];

        foreach ($categorias as $categoria) {
            Ctrabajo::create(['categoria' => $categoria]);
        }
    }

}

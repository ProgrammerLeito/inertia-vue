<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use GuzzleHttp\Client;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'idCliente' => '1',
            'cli_razonSocial' => 'FERNANADEZ S.A.C',
            'cli_ruc' => '20202054789548',
            'cli_ciudad' => 'Piura',
            'cli_direccionlegal' => 'Av. Separadora',
            'cli_direccion1' => 'Av. Separadora2',
            'cli_direccion2' => 'Av. Separadora3',
            'cli_observacion' => 'en mantenimiento',
            'estado' => '1'
        ]);
    }
}

<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_menus')->insert([
            ['idMenu' => 1, 'nombreMenu' => 'Clientes', 'iconHtml' => 'bi bi-people'],
            ['idMenu' => 2, 'nombreMenu' => 'Visitas y Llamadas', 'iconHtml' => 'bi bi-telephone'],
            ['idMenu' => 3, 'nombreMenu' => 'Productos', 'iconHtml' => 'bi bi-cart'],
            ['idMenu' => 4, 'nombreMenu' => 'Servicio Tecnico', 'iconHtml' => 'bi bi-gear-wide-connected'],
            ['idMenu' => 5, 'nombreMenu' => 'Cotizaciones', 'iconHtml' => 'bi bi-graph-up-arrow'],
            ['idMenu' => 6, 'nombreMenu' => 'Prestamos o Alquiler', 'iconHtml' => 'bi bi-bar-chart-line'],
            ['idMenu' => 7, 'nombreMenu' => 'Certificaciones', 'iconHtml' => 'bi bi-patch-check'],
            ['idMenu' => 8, 'nombreMenu' => 'Configuraciones', 'iconHtml' => 'bi bi-gear'],
            ['idMenu' => 9, 'nombreMenu' => 'Recursos', 'iconHtml' => 'bi bi-inboxes'],
        ]);
    }
}

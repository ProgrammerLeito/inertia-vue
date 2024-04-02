<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_submenus')->insert([
            ['idSubMenu' => 1, 'idMenu' => 0, 'nombreSubMenu' => 'Inicio', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 2, 'idMenu' => 1, 'nombreSubMenu' => 'Listar Clientes', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 3, 'idMenu' => 2, 'nombreSubMenu' => 'Control de llamadas Diarias', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 4, 'idMenu' => 3, 'nombreSubMenu' => 'Catalago de Productos', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 5, 'idMenu' => 4, 'nombreSubMenu' => 'Manuales y Calibracion', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 6, 'idMenu' => 4, 'nombreSubMenu' => 'Listar Hojas de Servicio', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 7, 'idMenu' => 4, 'nombreSubMenu' => 'Generar Ticket', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 8, 'idMenu' => 5, 'nombreSubMenu' => 'Generar Cotizacion por Venta', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 9, 'idMenu' => 5, 'nombreSubMenu' => 'Generar Cotizacion por Servicio', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 10, 'idMenu' => 5, 'nombreSubMenu' => 'Listar Cotizaciones', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 11, 'idMenu' => 6, 'nombreSubMenu' => 'Listar Equipos', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 12, 'idMenu' => 7, 'nombreSubMenu' => 'Registrar Solicitud Certificacion', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 13, 'idMenu' => 7, 'nombreSubMenu' => 'Solicitudes Emitidas', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 14, 'idMenu' => 7, 'nombreSubMenu' => 'Certificaciones Emitidas', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 15, 'idMenu' => 7, 'nombreSubMenu' => 'Certificaciones por Caducar', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 16, 'idMenu' => 7, 'nombreSubMenu' => 'Lista Certificados emitidos TW', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 17, 'idMenu' => 8, 'nombreSubMenu' => 'Usuarios', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 18, 'idMenu' => 9, 'nombreSubMenu' => 'Inventario', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 19, 'idMenu' => 9, 'nombreSubMenu' => 'Reuniones Semanales', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
            ['idSubMenu' => 20, 'idMenu' => 9, 'nombreSubMenu' => 'Ocurrencias', 'idNombreSubMenu' => '#', 'hrefSubMenu' => '#', 'estadoSubMenu' => 1],
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hojaservicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('n_informe', 9)->unique();
            $table->string('servicio_a_realizar')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('direccion')->nullable();
            $table->string('contacto')->nullable();
            $table->string('area_de_contacto')->nullable();
            $table->string('telefono_de_contacto')->nullable();
            $table->string('asesor_encargado')->nullable();
            $table->string('tecnico_encargado')->nullable();
            $table->time('hora_del_servicio')->nullable();
            $table->integer('cantidad_de_instrumentos')->nullable();
            $table->text('datos_del_instrumento')->nullable();
            $table->text('trabajos_a_realizar')->nullable();
            $table->timestamps();
        });
         // Agregamos un evento para autoincrementar el campo 'n_informe'
         DB::statement('ALTER TABLE hojaservicios AUTO_INCREMENT = 1');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hojaservicios');
    }
};

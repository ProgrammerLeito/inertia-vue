<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->string('empresa', 250);
            $table->string('unidad_salida', 250);
            $table->string('comentario_salida', 250);
            $table->string('tecnico', 250);
            $table->date('fecha');
            $table->time('hora_salida');
            $table->string('devolucion', 2)->nullable();
            $table->string('unidad_devolucion', 100)->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};

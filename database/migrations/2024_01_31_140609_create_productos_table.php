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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('insumo', 250)->unique();
            $table->string('marca', 250);
            $table->string('modelo', 250);
            $table->string('cantidad', 250);
            $table->string('unidad_medida', 250);
            $table->date('fecha');
            $table->string('comprador', 250);
            $table->string('comentario', 250);
            $table->string('stock', 250);
            $table->string('ultima_entrada', 250);
            $table->string('precio', 250);
            $table->string('imagen_producto');
            $table->timestamps();

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

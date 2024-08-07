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
        Schema::create('tbproductos_agregados', function (Blueprint $table) {
            $table->id();
            $table->string('idCotizacion');
            $table->string('categoria_id');
            $table->string('modelo')->nullable();
            $table->string('especificaciones')->nullable();
            $table->string('marca')->nullable();
            $table->string('capacidades')->nullable(); 
            $table->string('precio_list')->nullable();
            $table->string('precio_min')->nullable();
            $table->string('precio_max')->nullable();
            $table->string('cantidad')->nullable(); 
            $table->string('importe')->nullable();
            $table->string('garantia')->nullable();
            $table->string('dias_entrega')->nullable(); 
            $table->string('forma_pago')->nullable();
            $table->string('moneda')->nullable();
            $table->string('foto')->nullable();
            $table->string('requerimientos')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbproductos_agregados');
    }
};

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
        Schema::create('hservicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained()->onDelete('cascade');
            $table->foreignId('hmarca_id')->constrained()->onDelete('cascade');
            $table->string('modelo');
            $table->string('serie');
            $table->string('division');
            $table->string('capacidad');
            $table->string('n_informe');
            $table->string('razonSocial');
            $table->string('plataforma');
            $table->string('fecha');
            $table->string('requiere');
            $table->string('diagnostico');
            $table->string('trabajos');
            $table->string('tecnico')->nullable();
            $table->string('foto',1000)->nullable();
            $table->string('foto2',1000)->nullable();
            $table->string('foto3',1000)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hservicios');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('n_informe', 9)->unique();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->string('direccion');
            $table->foreignId('dato_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('n_guia');
            $table->string('fecha');
            $table->string('hora');
            $table->string('tecnico')->nullable();
            $table->string('descripcion');
            $table->string('estado')->nullable();
            $table->binary('pdf')->nullable();
            $table->string('e_servicio')->nullable();
            $table->timestamps();
        });
          // Agregamos un evento para autoincrementar el campo 'n_informe'
          DB::statement('ALTER TABLE servicios AUTO_INCREMENT = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};

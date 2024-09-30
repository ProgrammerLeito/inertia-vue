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
        Schema::create('tb_pendientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('razon_social')->nullable();
            $table->string('cantidad')->nullable();
            $table->string('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->string('lugar')->nullable();
            $table->string('contacto')->nullable();
            $table->string('usuario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pendientes');
    }
};

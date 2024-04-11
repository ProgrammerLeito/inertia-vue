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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id('id');
            $table->string('numeroDocumento');
            $table->string('razonSocial', 250);
            $table->string('direccion', 250);
            $table->string('distrito', 250);
            $table->string('provincia', 250);
            $table->string('departamento', 250);
            $table->string('estado');
            $table->string('cli_direccion2', 250);
            $table->string('cli_observacion', 250);
            $table->timestamps();

            $table->unsignedBigInteger('prov_clientes');
            $table->foreign('prov_clientes')->references('id')->on('tbprovincias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

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
            $table->id('idCliente');
            $table->string('cli_razonSocial', 250);
            $table->string('cli_ruc', 250);
            $table->string('cli_ciudad', 250);
            $table->string('cli_direccionlegal', 250);
            $table->string('cli_direccion1', 250);
            $table->string('cli_direccion2', 250);
            $table->string('cli_observacion', 250);
            $table->string('estado', 2)->nullable()->default(1);
            $table->timestamps();
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

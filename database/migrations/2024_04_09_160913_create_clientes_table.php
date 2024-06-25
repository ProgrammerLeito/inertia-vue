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
            $table->string('ctg');
            $table->string('asesor');
            $table->date('fechafactura')->nullable();
            $table->string('codigofactura', 250)->nullable();
            $table->string('cli_direccion2', 250)->nullable();
            $table->string('cli_observacion', 250)->nullable();
            $table->foreignId('tbprovincia_id')->constrained()->onDelete('cascade');
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

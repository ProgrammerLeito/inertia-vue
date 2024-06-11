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
        Schema::create('cservicios', function (Blueprint $table) {
            $table->id();
            $table->string('n_cotizacion', 10)->unique();
            $table->string('descripcion')->nullable();
            $table->string('fecha');
            $table->string('marca');
            $table->string('modelo');
            $table->string('capacidad');
            $table->string('requiere');
            $table->string('div');
            $table->string('plataforma');
            $table->string('serie')->nullable();

            $table->string('n_informe');
            $table->string('razonSocial');

            $table->text('tselecionado')->nullable();
            $table->text('cselecionado')->nullable();
            $table->text('oselecionado')->nullable();
            $table->text('dselecionado')->nullable();
            $table->text('rselecionado')->nullable();

            $table->string('precio');
            $table->string('moneda');
            $table->string('fpago');
            $table->string('garantia');
            $table->string('dentrega');
            $table->string('foto')->nullable();
            $table->string('foto1')->nullable();
            $table->string('total')->nullable();
            $table->string('codesunat')->nullable();
            $table->string('subtotal');
            $table->string('igv');

            $table->foreignId('plantilla_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c__servicios');
    }
};

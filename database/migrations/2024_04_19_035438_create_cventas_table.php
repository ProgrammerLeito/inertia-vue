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
        Schema::create('cventas', function (Blueprint $table) {
            $table->id();
            $table->string('n_cotizacion', 10)->unique();
            $table->string('codesunat')->nullable();
            $table->string('estado')->nullable();
            $table->string('tecnico')->nullable();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenor_id')->constrained()->onDelete('cascade');
            $table->string('numeroDocumento')->nullable();
            $table->string('direccion')->nullable();
            $table->date('fecha');
            $table->string('moneda', 10)->nullable();
            $table->string('garantia')->nullable();
            $table->string('forma_pago')->nullable();
            $table->string('dias_entrega')->nullable();
            $table->string('validez_cot')->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('igv', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cventas');
    }
};

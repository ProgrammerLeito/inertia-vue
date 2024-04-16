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
        Schema::create('tbproductos', function (Blueprint $table) {
            $table->id();
            $table->softDeletes('deleted_at', precision: 0);
            $table->foreignId('tbcategoria_id')->constrained()->onDelete('cascade');
            $table->foreignId('tbsubcategoria_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tbmarca_id')->constrained()->onDelete('cascade');
            $table->string('modelo');
            $table->string('medida');
            $table->string('moneda');
            $table->decimal('precio', 10, 2);
            $table->decimal('descuento', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('codigo')->unique();
            $table->string('estado');
            $table->text('capacidades')->nullable();
            $table->text('especificaciones')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbproductos');
    }
};

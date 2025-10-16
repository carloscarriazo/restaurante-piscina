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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('unit'); // ej: gramos, litros, unidades
            $table->decimal('current_stock', 10, 3)->default(0); // Stock actual
            $table->decimal('minimum_stock', 10, 3)->default(0); // Stock mínimo (alerta)
            $table->decimal('cost_per_unit', 10, 2)->default(0); // Costo por unidad
            $table->string('supplier')->nullable(); // Proveedor
            $table->boolean('is_active')->default(true); // Si está activo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};

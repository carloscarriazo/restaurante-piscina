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
        Schema::create('daily_discounts', function (Blueprint $table) {
            $table->id();
            $table->date('discount_date'); // Fecha del descuento
            $table->decimal('minimum_purchase', 8, 2)->default(5000); // Compra mínima para aplicar descuento
            $table->decimal('product_max_price', 8, 2)->default(3500); // Precio máximo del producto de descuento
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // Producto en descuento
            $table->decimal('discount_amount', 8, 2)->default(0); // Monto del descuento
            $table->decimal('discount_percentage', 5, 2)->default(0); // Porcentaje del descuento
            $table->boolean('is_active')->default(true); // Si el descuento está activo
            $table->text('description')->nullable(); // Descripción del descuento
            $table->timestamps();

            $table->unique(['discount_date', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_discounts');
    }
};

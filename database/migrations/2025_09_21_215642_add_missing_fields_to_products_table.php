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
        Schema::table('products', function (Blueprint $table) {
            // Agregar unit_id para relacionar con la tabla units
            $table->foreignId('unit_id')->nullable()->constrained()->after('category_id');

            // Agregar campos de stock
            $table->integer('stock')->default(0)->after('price');
            $table->integer('stock_minimo')->default(0)->after('stock');

            // Agregar campo available
            $table->boolean('available')->default(true)->after('stock_minimo');

            // Agregar campo para imagen
            $table->string('image_url')->nullable()->after('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit_id', 'stock', 'stock_minimo', 'available', 'image_url']);
        });
    }
};

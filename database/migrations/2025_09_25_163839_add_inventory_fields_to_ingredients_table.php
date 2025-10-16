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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('current_stock', 10, 3)->default(0)->after('unit');
            $table->decimal('minimum_stock', 10, 3)->default(0)->after('current_stock');
            $table->decimal('cost_per_unit', 8, 2)->nullable()->after('minimum_stock');
            $table->string('supplier')->nullable()->after('cost_per_unit');
            $table->boolean('is_active')->default(true)->after('supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'current_stock',
                'minimum_stock',
                'cost_per_unit',
                'supplier',
                'is_active'
            ]);
        });
    }
};

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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Cambiar tipos de enum para coincidir con nuestros Services
            $table->enum('type', ['added', 'consumed', 'adjusted'])->change();
            
            // Agregar campos faltantes
            $table->decimal('previous_quantity', 10, 3)->after('reason')->nullable();
            $table->decimal('new_quantity', 10, 3)->after('previous_quantity')->nullable();
            $table->text('notes')->after('new_quantity')->nullable();
            
            // Renombrar campos existentes para consistencia
            $table->renameColumn('previous_stock', 'previous_stock_backup');
            $table->renameColumn('new_stock', 'new_stock_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->enum('type', ['increase', 'decrease', 'adjustment'])->change();
            $table->dropColumn(['previous_quantity', 'new_quantity', 'notes']);
            $table->renameColumn('previous_stock_backup', 'previous_stock');
            $table->renameColumn('new_stock_backup', 'new_stock');
        });
    }
};
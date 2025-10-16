<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar sistema de asignación de mesas a meseros
     */
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            // Agregar columna para asignar mesero a la mesa
            $table->foreignId('waiter_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();

            // Fecha de asignación
            $table->timestamp('assigned_at')->nullable()->after('waiter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->dropColumn(['waiter_id', 'assigned_at']);
        });
    }
};

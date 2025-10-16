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
        Schema::table('tables', function (Blueprint $table) {
            $table->integer('number')->after('id')->nullable(); // Número de mesa
            $table->string('location')->after('capacity')->nullable(); // Ubicación (terraza, interior, etc.)
            $table->timestamp('occupied_at')->after('status')->nullable(); // Hora de ocupación
            $table->timestamp('cleaned_at')->after('occupied_at')->nullable(); // Hora de limpieza
            $table->boolean('is_available')->after('cleaned_at')->default(true); // Disponibilidad general
            $table->enum('status', ['available', 'occupied', 'cleaning', 'out_of_order'])->default('available')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['number', 'location', 'occupied_at', 'cleaned_at', 'is_available']);
            $table->enum('status', ['available', 'reserved', 'occupied'])->default('available')->change();
        });
    }
};
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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['increase', 'decrease', 'adjustment']);
            $table->decimal('quantity', 10, 3);
            $table->string('reason');
            $table->string('reference_type')->nullable(); // Para relaciones polimórficas
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('previous_stock', 10, 3);
            $table->decimal('new_stock', 10, 3);
            $table->timestamp('moved_at');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

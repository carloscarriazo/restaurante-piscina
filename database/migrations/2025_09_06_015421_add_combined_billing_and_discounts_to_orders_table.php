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
        Schema::table('orders', function (Blueprint $table) {
            $table->json('combined_tables')->nullable(); // IDs de mesas combinadas para facturación
            $table->decimal('discount_amount', 8, 2)->default(0); // Monto de descuento aplicado
            $table->string('discount_reason')->nullable(); // Razón del descuento
            $table->boolean('is_editable')->default(true); // Si el pedido puede ser editado por meseros
            $table->timestamp('last_edited_at')->nullable(); // Última vez que fue editado
            $table->foreignId('last_edited_by')->nullable()->constrained('users')->nullOnDelete(); // Quién lo editó por última vez
            $table->boolean('kitchen_notified')->default(false); // Si la cocina ha sido notificada
            $table->timestamp('kitchen_notified_at')->nullable(); // Cuando se notificó a la cocina
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'combined_tables',
                'discount_amount',
                'discount_reason',
                'is_editable',
                'last_edited_at',
                'last_edited_by',
                'kitchen_notified',
                'kitchen_notified_at'
            ]);
        });
    }
};

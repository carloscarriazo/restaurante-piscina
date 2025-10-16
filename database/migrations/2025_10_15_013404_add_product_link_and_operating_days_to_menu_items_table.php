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
        Schema::table('menu_items', function (Blueprint $table) {
            // Vincular con productos (opcional - puede ser independiente o vinculado)
            $table->foreignId('product_id')->nullable()->after('menu_category_id')->constrained()->onDelete('set null');

            // Hacer el precio nullable ya que puede venir del producto vinculado
            $table->decimal('price', 8, 2)->nullable()->change();

            // Días de operación (viernes, sábado, domingo)
            $table->json('operating_days')->nullable()->after('is_featured')
                ->comment('Días de la semana en que el ítem está disponible [5,6,0] para viernes, sábado, domingo');

            // Fecha de vigencia del menú
            $table->date('valid_from')->nullable()->after('operating_days');
            $table->date('valid_until')->nullable()->after('valid_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'operating_days', 'valid_from', 'valid_until']);

            // Revertir el cambio de nullable en price
            $table->decimal('price', 8, 2)->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega índices estratégicos para optimizar queries frecuentes:
     * - Búsquedas por nombre/email
     * - Filtros por estado/activo
     * - Foreign keys para JOINs
     * - Ordenamiento y filtros de fecha
     */
    public function up(): void
    {
        $indexes = [
            // Categories
            'categories' => [
                'ALTER TABLE categories ADD INDEX idx_categories_nombre (nombre)',
                'ALTER TABLE categories ADD INDEX idx_categories_activo (activo)',
                'ALTER TABLE categories ADD INDEX idx_categories_activo_nombre (activo, nombre)',
            ],
            // Users
            'users' => [
                'ALTER TABLE users ADD INDEX idx_users_name (name)',
            ],
            // Orders
            'orders' => [
                'ALTER TABLE orders ADD INDEX idx_orders_status (status)',
                'ALTER TABLE orders ADD INDEX idx_orders_created_at (created_at)',
                'ALTER TABLE orders ADD INDEX idx_orders_status_date (status, created_at)',
                'ALTER TABLE orders ADD INDEX idx_orders_table_id (table_id)',
            ],
            // Order Items
            'order_items' => [
                'ALTER TABLE order_items ADD INDEX idx_order_items_order_id (order_id)',
                'ALTER TABLE order_items ADD INDEX idx_order_items_product_id (product_id)',
                'ALTER TABLE order_items ADD INDEX idx_order_items_order_product (order_id, product_id)',
            ],
            // Products
            'products' => [
                'ALTER TABLE products ADD INDEX idx_products_name (name)',
                'ALTER TABLE products ADD INDEX idx_products_category_id (category_id)',
                'ALTER TABLE products ADD INDEX idx_products_available (available)',
                'ALTER TABLE products ADD INDEX idx_products_category_available (category_id, available)',
            ],
            // Invoices
            'invoices' => [
                'ALTER TABLE invoices ADD INDEX idx_invoices_order_id (order_id)',
                'ALTER TABLE invoices ADD INDEX idx_invoices_created_at (created_at)',
                'ALTER TABLE invoices ADD INDEX idx_invoices_total (total)',
            ],
            // Tables
            'tables' => [
                'ALTER TABLE `tables` ADD INDEX idx_tables_status (status)',
                'ALTER TABLE `tables` ADD INDEX idx_tables_number (number)',
            ],
        ];

        // Session Logs (si existe)
        if (Schema::hasTable('session_logs')) {
            $indexes['session_logs'] = [
                'ALTER TABLE session_logs ADD INDEX idx_session_logs_user_id (user_id)',
                'ALTER TABLE session_logs ADD INDEX idx_session_logs_created_at (created_at)',
            ];
        }

        // Ejecutar cada índice con try-catch para evitar duplicados
        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $sql) {
                try {
                    DB::statement($sql);
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_nombre');
            $table->dropIndex('idx_categories_activo');
            $table->dropIndex('idx_categories_activo_nombre');
        });

        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_name');
        });

        // Orders
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_created_at');
            $table->dropIndex('idx_orders_status_date');
            $table->dropIndex('idx_orders_table_id');
        });

        // Order Items
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_order_id');
            $table->dropIndex('idx_order_items_product_id');
            $table->dropIndex('idx_order_items_order_product');
        });

        // Products
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_available');
            $table->dropIndex('idx_products_category_available');
        });

        // Invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_order_id');
            $table->dropIndex('idx_invoices_created_at');
            $table->dropIndex('idx_invoices_total');
        });

        // Tables
        Schema::table('tables', function (Blueprint $table) {
            $table->dropIndex('idx_tables_status');
            $table->dropIndex('idx_tables_number');
        });

        // Session Logs
        if (Schema::hasTable('session_logs')) {
            Schema::table('session_logs', function (Blueprint $table) {
                $table->dropIndex('idx_session_logs_user_id');
                $table->dropIndex('idx_session_logs_created_at');
            });
        }
    }
};

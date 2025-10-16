<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'orders';
    
    private const ORDER_INFO_COLUMNS = [
        'code' => ['type' => 'string', 'after' => 'id', 'unique' => true],
        'customer_name' => ['type' => 'string', 'after' => 'user_id'],
        'notes' => ['type' => 'text', 'after' => 'customer_name'],
    ];
    
    private const FINANCIAL_COLUMNS = [
        'total' => ['type' => 'decimal', 'after' => 'notes', 'default' => 0],
        'subtotal' => ['type' => 'decimal', 'after' => 'total', 'default' => 0],
        'tax' => ['type' => 'decimal', 'after' => 'subtotal', 'default' => 0],
        'discount' => ['type' => 'decimal', 'after' => 'tax', 'default' => 0],
    ];
    
    private const TIMESTAMP_COLUMNS = [
        'completed_at' => ['type' => 'timestamp', 'after' => 'kitchen_notified'],
    ];

    public function up(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $this->addOrderInfoColumns($table);
            $this->addFinancialColumns($table);
            $this->addTimestampColumns($table);
        });
    }

    private function addOrderInfoColumns(Blueprint $table): void
    {
        foreach (self::ORDER_INFO_COLUMNS as $column => $config) {
            if (!Schema::hasColumn(self::TABLE_NAME, $column)) {
                $this->addColumn($table, $column, $config);
            }
        }
    }

    private function addFinancialColumns(Blueprint $table): void
    {
        foreach (self::FINANCIAL_COLUMNS as $column => $config) {
            if (!Schema::hasColumn(self::TABLE_NAME, $column)) {
                $this->addColumn($table, $column, $config);
            }
        }
    }

    private function addTimestampColumns(Blueprint $table): void
    {
        foreach (self::TIMESTAMP_COLUMNS as $column => $config) {
            if (!Schema::hasColumn(self::TABLE_NAME, $column)) {
                $this->addColumn($table, $column, $config);
            }
        }
    }

    private function addColumn(Blueprint $table, string $column, array $config): void
    {
        $columnDefinition = match($config['type']) {
            'string' => $table->string($column),
            'text' => $table->text($column),
            'decimal' => $table->decimal($column, 10, 2),
            'timestamp' => $table->timestamp($column),
            default => $table->string($column),
        };

        if (isset($config['after'])) {
            $columnDefinition->after($config['after']);
        }

        if (isset($config['unique']) && $config['unique']) {
            $columnDefinition->unique();
        }

        if (isset($config['default'])) {
            $columnDefinition->default($config['default']);
        }

        $columnDefinition->nullable();
    }

    public function down(): void
    {
        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            $allColumns = array_merge(
                array_keys(self::ORDER_INFO_COLUMNS),
                array_keys(self::FINANCIAL_COLUMNS),
                array_keys(self::TIMESTAMP_COLUMNS)
            );

            foreach ($allColumns as $column) {
                if (Schema::hasColumn(self::TABLE_NAME, $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

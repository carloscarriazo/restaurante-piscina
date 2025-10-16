<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Exception;

class TableService extends BaseService
{
    /**
     * Obtener estado actual de todas las mesas
     */
    public function getAllTablesStatus(): array
    {
        $tables = Table::with(['currentOrders' => function ($query) {
            $query->whereIn('status', ['pending', 'preparing', 'ready']);
        }])->orderBy('number')->get();

        return [
            'tables' => $tables->map(function ($table) {
                return $this->formatTableStatus($table);
            }),
            'statistics' => [
                'total_tables' => $tables->count(),
                'occupied_tables' => $tables->where('status', 'occupied')->count(),
                'available_tables' => $tables->where('status', 'available')->count(),
                'cleaning_tables' => $tables->where('status', 'cleaning')->count(),
                'occupancy_rate' => $this->calculateOccupancyRate($tables)
            ]
        ];
    }

    /**
     * Formatear información de estado de mesa
     */
    private function formatTableStatus(Table $table): array
    {
        $currentOrders = $table->currentOrders;
        $totalAmount = $currentOrders->sum('total');
        $totalItems = $currentOrders->sum(function ($order) {
            return $order->items->sum('quantity');
        });

        return [
            'id' => $table->id,
            'number' => $table->number,
            'capacity' => $table->capacity,
            'status' => $table->status,
            'location' => $table->location,
            'current_orders' => $currentOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at->format('H:i'),
                    'elapsed_time' => $order->created_at->diffInMinutes(now())
                ];
            }),
            'total_amount' => $totalAmount,
            'total_items' => $totalItems,
            'occupied_since' => $table->occupied_at ? $table->occupied_at->format('H:i') : null,
            'can_merge' => $this->canMergeTable($table),
            'estimated_completion' => $this->estimateTableCompletion($currentOrders)
        ];
    }

    /**
     * Verificar si una mesa puede fusionarse
     */
    private function canMergeTable(Table $table): bool
    {
        return $table->status === 'occupied' && $table->currentOrders->isNotEmpty();
    }

    /**
     * Estimar tiempo de finalización de mesa
     */
    private function estimateTableCompletion(Collection $orders): array
    {
        if ($orders->isEmpty()) {
            return ['minutes' => 0, 'time' => null];
        }

        $maxPreparationTime = 0;
        foreach ($orders as $order) {
            $orderTime = $order->items->sum(function ($item) {
                return ($item->product->preparation_time ?? 15) * $item->quantity;
            });
            $maxPreparationTime = max($maxPreparationTime, $orderTime);
        }

        $estimatedMinutes = ceil($maxPreparationTime * 0.8); // Factor de eficiencia
        $completionTime = now()->addMinutes($estimatedMinutes);

        return [
            'minutes' => $estimatedMinutes,
            'time' => $completionTime->format('H:i')
        ];
    }

    /**
     * Calcular tasa de ocupación
     */
    private function calculateOccupancyRate(Collection $tables): float
    {
        $totalTables = $tables->count();
        $occupiedTables = $tables->where('status', 'occupied')->count();

        return $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100, 2) : 0;
    }

    /**
     * Fusionar pedidos de múltiples mesas
     */
    public function mergeTableOrders(array $tableIds, int $mainTableId): array
    {
        $this->validateRequiredFields(['table_ids' => $tableIds, 'main_table_id' => $mainTableId],
                                     ['table_ids', 'main_table_id']);

        return $this->executeTransaction(function () use ($tableIds, $mainTableId) {
            // Validar que las mesas existan y tengan pedidos
            $tables = Table::whereIn('id', $tableIds)
                          ->where('status', 'occupied')
                          ->with('currentOrders')
                          ->get();

            if ($tables->count() !== count($tableIds)) {
                throw new \InvalidArgumentException('Algunas mesas no están disponibles para fusión');
            }

            $mainTable = $tables->where('id', $mainTableId)->first();
            if (!$mainTable) {
                throw new \InvalidArgumentException('La mesa principal no es válida');
            }

            // Recopilar todos los pedidos
            $allOrders = collect();
            foreach ($tables as $table) {
                $allOrders = $allOrders->merge($table->currentOrders);
            }

            if ($allOrders->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No hay pedidos para fusionar'
                ];
            }

            // Crear pedido fusionado usando OrderService
            $orderService = new OrderService();
            $mergedOrderData = [
                'table_id' => $mainTableId,
                'items' => $this->extractItemsFromOrders($allOrders),
                'notes' => 'Pedido fusionado de mesas: ' . $tables->pluck('number')->join(', ')
            ];

            // Cancelar pedidos originales y restaurar inventario
            foreach ($allOrders as $order) {
                $orderService->changeStatus($order->id, 'cancelled');
            }

            // Liberar mesas secundarias
            Table::whereIn('id', $tableIds)
                 ->where('id', '!=', $mainTableId)
                 ->update([
                     'status' => 'available',
                     'occupied_at' => null
                 ]);

            // Crear nuevo pedido fusionado
            $mergedOrder = $orderService->create($mergedOrderData);

            $this->logActivity('tables_merged', 'tables', [
                'main_table_id' => $mainTableId,
                'merged_table_ids' => $tableIds,
                'merged_order_id' => $mergedOrder['data']->id,
                'original_order_ids' => $allOrders->pluck('id')->toArray()
            ]);

            return [
                'success' => true,
                'message' => 'Mesas fusionadas exitosamente',
                'merged_order' => $mergedOrder['data'],
                'main_table' => $mainTable->fresh(),
                'freed_tables' => $tableIds
            ];
        });
    }

    /**
     * Extraer items de pedidos para fusión
     */
    private function extractItemsFromOrders(Collection $orders): array
    {
        $groupedItems = [];

        // Agrupar items similares
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $key = $item->product_id . '_' . ($item->notes ?? '');

                if (!isset($groupedItems[$key])) {
                    $groupedItems[$key] = [
                        'product_id' => $item->product_id,
                        'quantity' => 0,
                        'notes' => $item->notes
                    ];
                }

                $groupedItems[$key]['quantity'] += $item->quantity;
            }
        }

        return array_values($groupedItems);
    }

    /**
     * Cambiar estado de mesa
     */
    public function changeTableStatus(int $tableId, string $status): array
    {
        $validStatuses = ['available', 'occupied', 'cleaning', 'out_of_order'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Estado de mesa inválido');
        }

        return $this->executeTransaction(function () use ($tableId, $status) {
            $table = Table::findOrFail($tableId);
            $oldStatus = $table->status;

            // Validaciones específicas
            if ($status === 'available' && $table->currentOrders()->exists()) {
                throw new \RuntimeException('No se puede liberar una mesa con pedidos pendientes');
            }

            if ($status === 'occupied' && $table->status === 'occupied') {
                throw new \RuntimeException('La mesa ya está ocupada');
            }

            $updateData = ['status' => $status];

            // Actualizar timestamps según el estado
            switch ($status) {
                case 'occupied':
                    $updateData['occupied_at'] = now();
                    break;
                case 'available':
                    $updateData['occupied_at'] = null;
                    $updateData['cleaned_at'] = now();
                    break;
                case 'cleaning':
                    $updateData['cleaned_at'] = null;
                    break;
                default:
                    // No action needed for other statuses
                    break;
            }

            $table->update($updateData);

            $this->logActivity('table_status_changed', 'tables', [
                'table_id' => $tableId,
                'table_number' => $table->number,
                'old_status' => $oldStatus,
                'new_status' => $status
            ]);

            return [
                'success' => true,
                'message' => "Estado de mesa {$table->number} actualizado a {$status}",
                'table' => $this->formatTableStatus($table->fresh())
            ];
        });
    }

    /**
     * Obtener estadísticas de mesas
     */
    public function getTableStatistics(array $filters = []): array
    {
        $query = Table::query();

        if (isset($filters['location'])) {
            $query->where('location', $filters['location']);
        }

        $tables = $query->get();
        $occupiedTables = $tables->where('status', 'occupied');

        // Estadísticas de rotación
        $rotationStats = $this->calculateTableRotation($filters);

        return [
            'total_tables' => $tables->count(),
            'capacity_breakdown' => $tables->groupBy('capacity')->map->count(),
            'location_breakdown' => $tables->groupBy('location')->map->count(),
            'current_occupancy' => [
                'occupied' => $occupiedTables->count(),
                'available' => $tables->where('status', 'available')->count(),
                'cleaning' => $tables->where('status', 'cleaning')->count(),
                'out_of_order' => $tables->where('status', 'out_of_order')->count(),
                'occupancy_rate' => $this->calculateOccupancyRate($tables)
            ],
            'rotation_statistics' => $rotationStats,
            'peak_hours' => $this->getTablePeakHours($filters),
            'average_dining_time' => $this->calculateAverageDiningTime($filters)
        ];
    }

    /**
     * Calcular estadísticas de rotación de mesas
     */
    private function calculateTableRotation(array $filters): array
    {
        $query = Order::with('table')
                     ->whereIn('status', ['completed', 'served']);

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $completedOrders = $query->get();
        $tableRotations = $completedOrders->groupBy('table_id')->map->count();

        return [
            'total_rotations' => $completedOrders->count(),
            'average_rotations_per_table' => $tableRotations->avg(),
            'most_rotated_table' => $tableRotations->max(),
            'least_rotated_table' => $tableRotations->min(),
            'rotation_by_table' => $tableRotations->toArray()
        ];
    }

    /**
     * Obtener horas pico de ocupación
     */
    private function getTablePeakHours(array $filters): array
    {
        $query = Order::query();

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $orders = $query->get();

        $hourlyOccupancy = $orders->groupBy(function ($order) {
            return $order->created_at->format('H');
        })->map->count()->sortDesc();

        return $hourlyOccupancy->take(5)->toArray();
    }

    /**
     * Calcular tiempo promedio de comida
     */
    private function calculateAverageDiningTime(array $filters): float
    {
        $query = Order::whereNotNull('completed_at');

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $completedOrders = $query->get();

        if ($completedOrders->isEmpty()) {
            return 0;
        }

        $totalTime = $completedOrders->sum(function ($order) {
            return $order->created_at->diffInMinutes($order->completed_at);
        });

        return round($totalTime / $completedOrders->count(), 2);
    }

    /**
     * Limpiar mesa después del servicio
     */
    public function cleanTable(int $tableId): array
    {
        return $this->executeTransaction(function () use ($tableId) {
            $table = Table::findOrFail($tableId);

            if ($table->status !== 'cleaning' && $table->status !== 'occupied') {
                throw new \InvalidArgumentException('Solo se pueden limpiar mesas ocupadas o en proceso de limpieza');
            }

            $table->update([
                'status' => 'available',
                'cleaned_at' => now(),
                'occupied_at' => null
            ]);

            $this->logActivity('table_cleaned', 'tables', [
                'table_id' => $tableId,
                'table_number' => $table->number,
                'cleaned_by' => Auth::id()
            ]);

            return [
                'success' => true,
                'message' => "Mesa {$table->number} limpiada y disponible",
                'table' => $this->formatTableStatus($table->fresh())
            ];
        });
    }

    /**
     * Obtener todas las mesas con su estado actual
     */
    public function getAllTablesWithStatus(): array
    {
        try {
            $tables = Table::with(['currentOrders.user', 'currentOrders.items'])
                          ->orderBy('number')
                          ->get();

            return $tables->map(function ($table) {
                $currentOrder = $table->currentOrders->first();

                return [
                    'id' => $table->id,
                    'number' => $table->number,
                    'capacity' => $table->capacity,
                    'location' => $table->location,
                    'status' => $table->status,
                    'status_label' => $this->getStatusLabel($table->status),
                    'status_color' => $this->getStatusColor($table->status),
                    'is_occupied' => $table->status === 'occupied',
                    'occupied_since' => $table->occupied_at?->diffForHumans(),
                    'current_order' => $currentOrder ? [
                        'id' => $currentOrder->id,
                        'code' => $currentOrder->code ?? "#{$currentOrder->id}",
                        'waiter' => $currentOrder->user?->name ?? 'Sin asignar',
                        'items_count' => $currentOrder->items->count(),
                        'total' => $currentOrder->total,
                        'status' => $currentOrder->status
                    ] : null,
                    'can_occupy' => $table->status === 'available',
                    'can_free' => $table->status === 'occupied'
                ];
            })->toArray();

        } catch (Exception $e) {
            $this->logError('Error obteniendo mesas con estado', $e);
            return [];
        }
    }

    /**
     * Ocupar una mesa
     */
    public function occupyTable(int $tableId, int $userId): array
    {
        try {
            return $this->executeTransaction(function () use ($tableId, $userId) {
                $table = Table::findOrFail($tableId);

                if ($table->status !== 'available') {
                    throw new Exception('La mesa no está disponible');
                }

                $table->update([
                    'status' => 'occupied',
                    'occupied_at' => now(),
                    'cleaned_at' => null
                ]);

                $this->logActivity('table_occupied', 'tables', [
                    'table_id' => $tableId,
                    'table_number' => $table->number,
                    'occupied_by' => $userId
                ]);

                return [
                    'success' => true,
                    'message' => "Mesa {$table->number} ocupada correctamente",
                    'table' => $this->formatTableStatus($table->fresh())
                ];
            });

        } catch (Exception $e) {
            $this->logError('Error ocupando mesa', $e, ['table_id' => $tableId, 'user_id' => $userId]);
            return [
                'success' => false,
                'message' => 'Error al ocupar la mesa: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Liberar una mesa
     */
    public function freeTable(int $tableId): array
    {
        try {
            return $this->executeTransaction(function () use ($tableId) {
                $table = Table::findOrFail($tableId);

                // Verificar que no tenga pedidos activos
                $activeOrders = $table->currentOrders()
                                    ->whereIn('status', ['pending', 'preparing', 'ready'])
                                    ->count();

                if ($activeOrders > 0) {
                    throw new Exception('No se puede liberar la mesa, tiene pedidos activos');
                }

                $table->update([
                    'status' => 'cleaning',
                    'occupied_at' => null
                ]);

                $this->logActivity('table_freed', 'tables', [
                    'table_id' => $tableId,
                    'table_number' => $table->number,
                    'freed_by' => Auth::id()
                ]);

                return [
                    'success' => true,
                    'message' => "Mesa {$table->number} liberada, pendiente de limpieza",
                    'table' => $this->formatTableStatus($table->fresh())
                ];
            });

        } catch (Exception $e) {
            $this->logError('Error liberando mesa', $e, ['table_id' => $tableId]);
            return [
                'success' => false,
                'message' => 'Error al liberar la mesa: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener etiqueta de estado
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'available' => 'Disponible',
            'occupied' => 'Ocupada',
            'cleaning' => 'Limpieza',
            'maintenance' => 'Mantenimiento',
            default => 'Desconocido'
        };
    }

    /**
     * Obtener color de estado
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'available' => 'green',
            'occupied' => 'red',
            'cleaning' => 'yellow',
            'maintenance' => 'gray',
            default => 'gray'
        };
    }
}

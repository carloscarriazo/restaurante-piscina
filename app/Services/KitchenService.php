<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Exception;

class KitchenService extends BaseService
{
    /**
     * Obtener pedidos para la cocina con detalles completos
     */
    public function getKitchenOrders(): array
    {
        $orders = Order::with(['items.product', 'table', 'user'])
            ->whereIn('status', ['pending', 'preparing'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('status');

        $pendingOrders = $orders->get('pending', collect());
        $preparingOrders = $orders->get('preparing', collect());

        return [
            'pending_orders' => $pendingOrders->map(function ($order) {
                return $this->formatOrderForKitchen($order);
            }),
            'preparing_orders' => $preparingOrders->map(function ($order) {
                return $this->formatOrderForKitchen($order);
            }),
            'statistics' => [
                'total_pending' => $pendingOrders->count(),
                'total_preparing' => $preparingOrders->count(),
                'estimated_time' => $this->calculateEstimatedTime($pendingOrders->merge($preparingOrders)),
                'average_items_per_order' => $this->calculateAverageItems($pendingOrders->merge($preparingOrders))
            ]
        ];
    }

    /**
     * Formatear pedido para la vista de cocina
     */
    private function formatOrderForKitchen(Order $order): array
    {
        return [
            'id' => $order->id,
            'code' => $order->code,
            'table_number' => $order->table->number,
            'waiter_name' => $order->user->name,
            'status' => $order->status,
            'created_at' => $order->created_at->format('H:i'),
            'elapsed_time' => $order->created_at->diffInMinutes(now()),
            'priority' => $this->calculateOrderPriority($order),
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'preparation_time' => $item->product->preparation_time ?? 15,
                    'special_notes' => $item->notes,
                    'allergens' => $item->product->allergens,
                    'recipe' => $this->getProductRecipe($item->product_id)
                ];
            }),
            'total_items' => $order->items->sum('quantity'),
            'estimated_completion' => $this->calculateEstimatedCompletion($order),
            'notes' => $order->notes
        ];
    }

    /**
     * Obtener receta detallada de un producto
     */
    private function getProductRecipe(int $productId): array
    {
        $recipes = Recipe::where('product_id', $productId)
            ->with(['ingredient.unit'])
            ->get();

        return $recipes->map(function ($recipe) {
            return [
                'ingredient' => $recipe->ingredient->name,
                'quantity' => $recipe->quantity,
                'unit' => $recipe->ingredient->unit->abbreviation ?? 'unidad',
                'notes' => $recipe->notes
            ];
        })->toArray();
    }

    /**
     * Calcular prioridad del pedido
     */
    private function calculateOrderPriority(Order $order): string
    {
        $elapsedMinutes = $order->created_at->diffInMinutes(now());
        $totalItems = $order->items->sum('quantity');

        if ($elapsedMinutes > 30) {
            return 'high';
        } elseif ($elapsedMinutes > 15 || $totalItems > 5) {
            return 'medium';
        }

        return 'normal';
    }

    /**
     * Calcular tiempo estimado de finalización
     */
    private function calculateEstimatedCompletion(Order $order): array
    {
        $totalPreparationTime = $order->items->sum(function ($item) {
            return ($item->product->preparation_time ?? 15) * $item->quantity;
        });

        $estimatedMinutes = ceil($totalPreparationTime * 0.8); // Factor de eficiencia
        $completionTime = now()->addMinutes($estimatedMinutes);

        return [
            'minutes' => $estimatedMinutes,
            'completion_time' => $completionTime->format('H:i'),
            'is_delayed' => $estimatedMinutes > 45
        ];
    }

    /**
     * Marcar pedido como en preparación
     */
    public function startOrderPreparation(int $orderId): array
    {
        return $this->executeTransaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);

            if ($order->status !== 'pending') {
                throw new Exception('Solo se pueden preparar pedidos pendientes');
            }

            $order->update([
                'status' => 'preparing',
                'preparation_started_at' => now()
            ]);

            $this->logActivity('order_preparation_started', 'kitchen', [
                'order_id' => $orderId,
                'started_by' => Auth::id()
            ]);

            return $this->formatOrderForKitchen($order->fresh());
        });
    }

    /**
     * Marcar pedido como listo y notificar al mesero
     */
    public function markOrderReady(int $orderId): array
    {
        return $this->executeTransaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);

            if (!in_array($order->status, ['pending', 'preparing'])) {
                throw new Exception('Solo se pueden marcar como listos pedidos pendientes o en preparación');
            }

            $oldStatus = $order->status;
            $order->update([
                'status' => 'ready',
                'completed_at' => now()
            ]);

            // Notificar al mesero
            $this->notifyWaiterOrderReady($order);

            // Registrar tiempo de preparación si estaba en preparación
            if ($oldStatus === 'preparing' && $order->preparation_started_at) {
                $preparationTime = $order->preparation_started_at->diffInMinutes(now());
                $this->recordPreparationTime($order, $preparationTime);
            }

            $this->logActivity('order_marked_ready', 'kitchen', [
                'order_id' => $orderId,
                'completed_by' => Auth::id(),
                'preparation_time' => $preparationTime ?? null
            ]);

            return [
                'success' => true,
                'message' => "Pedido #{$order->code} marcado como listo. Mesero notificado.",
                'order' => $this->formatOrderForKitchen($order->fresh())
            ];
        });
    }

    /**
     * Notificar al mesero que el pedido está listo
     */
    private function notifyWaiterOrderReady(Order $order): void
    {
        $waiter = $order->user;
        $tableNumber = $order->table->number;

        // Registrar notificación en logs para implementación futura de WebSockets
        $this->logActivity('waiter_notification_sent', 'notifications', [
            'waiter_id' => $waiter->id,
            'waiter_name' => $waiter->name,
            'order_id' => $order->id,
            'order_code' => $order->code,
            'table_number' => $tableNumber,
            'message' => "Pedido #{$order->code} de mesa {$tableNumber} listo para servir"
        ]);

        // Notificar usando el método base
        $this->notifyUsers(
            [$waiter->id],
            'Pedido Listo - Mesa ' . $tableNumber,
            "El pedido #{$order->code} está listo para servir",
            [
                'type' => 'order_ready',
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'priority' => 'high'
            ]
        );
    }

    /**
     * Registrar tiempo de preparación para estadísticas
     */
    private function recordPreparationTime(Order $order, int $minutes): void
    {
        // Aquí se podría crear una tabla específica para tiempos de preparación
        $this->logActivity('preparation_time_recorded', 'statistics', [
            'order_id' => $order->id,
            'preparation_minutes' => $minutes,
            'total_items' => $order->items->sum('quantity'),
            'chef_id' => Auth::id()
        ]);
    }

    /**
     * Obtener estadísticas de rendimiento de cocina
     */
    public function getKitchenStatistics(array $filters = []): array
    {
        $query = Order::whereIn('status', ['ready', 'served', 'completed']);

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $completedOrders = $query->get();

        return [
            'total_orders_completed' => $completedOrders->count(),
            'average_preparation_time' => $this->calculateAveragePreparationTime($completedOrders),
            'orders_by_hour' => $this->getOrdersByHour($completedOrders),
            'most_prepared_items' => $this->getMostPreparedItems($filters),
            'efficiency_rate' => $this->calculateEfficiencyRate($completedOrders),
            'peak_hours' => $this->getPeakHours($completedOrders)
        ];
    }

    /**
     * Calcular tiempo promedio de preparación
     */
    private function calculateAveragePreparationTime($orders): float
    {
        $totalTime = 0;
        $ordersWithTime = 0;

        foreach ($orders as $order) {
            if ($order->preparation_started_at && $order->completed_at) {
                $totalTime += $order->preparation_started_at->diffInMinutes($order->completed_at);
                $ordersWithTime++;
            }
        }

        return $ordersWithTime > 0 ? round($totalTime / $ordersWithTime, 2) : 0;
    }

    /**
     * Obtener pedidos por hora
     */
    private function getOrdersByHour($orders): array
    {
        return $orders->groupBy(function ($order) {
            return $order->created_at->format('H');
        })->map->count()->toArray();
    }

    /**
     * Obtener productos más preparados
     */
    private function getMostPreparedItems(array $filters): array
    {
        $query = OrderItem::selectRaw('product_id, SUM(quantity) as total_prepared')
            ->whereHas('order', function ($q) use ($filters) {
                $q->whereIn('status', ['ready', 'served', 'completed']);

                if (isset($filters['date_from'])) {
                    $q->where('created_at', '>=', $filters['date_from']);
                }

                if (isset($filters['date_to'])) {
                    $q->where('created_at', '<=', $filters['date_to']);
                }
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_prepared')
            ->limit(10)
            ->get();

        return $query->map(function ($item) {
            return [
                'product_name' => $item->product->name,
                'total_prepared' => $item->total_prepared,
                'preparation_time' => $item->product->preparation_time ?? 15
            ];
        })->toArray();
    }

    /**
     * Calcular tiempo estimado total de pedidos activos
     */
    private function calculateEstimatedTime($orders): int
    {
        return $orders->sum(function ($order) {
            return $order->items->sum(function ($item) {
                return ($item->product->preparation_time ?? 15) * $item->quantity;
            });
        });
    }

    /**
     * Calcular promedio de items por pedido
     */
    private function calculateAverageItems($orders): float
    {
        if ($orders->isEmpty()) {
            return 0;
        }

        $totalItems = $orders->sum(function ($order) {
            return $order->items->sum('quantity');
        });

        return round($totalItems / $orders->count(), 2);
    }

    /**
     * Calcular tasa de eficiencia
     */
    private function calculateEfficiencyRate($orders): float
    {
        $onTimeOrders = $orders->filter(function ($order) {
            if (!$order->preparation_started_at || !$order->completed_at) {
                return false;
            }

            $actualTime = $order->preparation_started_at->diffInMinutes($order->completed_at);
            $expectedTime = $order->items->sum(function ($item) {
                return ($item->product->preparation_time ?? 15) * $item->quantity;
            });

            return $actualTime <= ($expectedTime * 1.2); // 20% de tolerancia
        });

        return $orders->count() > 0 ?
            round(($onTimeOrders->count() / $orders->count()) * 100, 2) : 0;
    }

    /**
     * Obtener horas pico
     */
    private function getPeakHours($orders): array
    {
        $hourlyStats = $this->getOrdersByHour($orders);
        arsort($hourlyStats);

        return array_slice($hourlyStats, 0, 3, true);
    }
}
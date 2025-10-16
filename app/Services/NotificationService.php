<?php

namespace App\Services;

use App\Contracts\NotificationServiceInterface;
use App\Constants\MessageConstants;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Broadcast;

class NotificationService implements NotificationServiceInterface
{
    /**
     * Enviar notificación a un mesero específico
     */
    public function notifyWaiter(int $waiterId, array $data): bool
    {
        try {
            Notification::create([
                'user_id' => $waiterId,
                'type' => $data['type'] ?? 'general',
                'title' => $data['title'],
                'message' => $data['message'],
                'data' => $data['extra_data'] ?? null,
                'order_id' => $data['order_id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación a mesero', [
                'waiter_id' => $waiterId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar notificación a todos los meseros
     */
    public function notifyAllWaiters(array $data): bool
    {
        try {
            $waiters = User::whereHas('roles', function ($query) {
                $query->where('nombre', 'Mesero');
            })->get();

            foreach ($waiters as $waiter) {
                $this->notifyWaiter($waiter->id, $data);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar notificación a todos los meseros', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener notificaciones pendientes para un mesero
     */
    public function getPendingNotifications(int $waiterId): array
    {
        $notifications = Notification::where('user_id', $waiterId)
            ->unread()
            ->with('order.table')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'order_id' => $notification->order_id,
                'table_name' => $notification->order?->table?->name,
                'created_at' => $notification->created_at->format('H:i'),
                'time_ago' => $notification->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    /**
     * Obtener todas las notificaciones (para pantallas generales)
     */
    public function getAllNotifications(): array
    {
        $notifications = Notification::with('order.table')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'order_id' => $notification->order_id,
                'table_name' => $notification->order?->table?->name,
                'created_at' => $notification->created_at->format('H:i'),
                'time_ago' => $notification->created_at->diffForHumans(),
                'is_read' => !is_null($notification->read_at),
            ];
        })->toArray();
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead(int $notificationId): bool
    {
        try {
            $notification = Notification::findOrFail($notificationId);
            return $notification->markAsRead();
        } catch (\Exception $e) {
            Log::error('Error al marcar notificación como leída', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar que un pedido está listo
     */
    public function notifyOrderReady(int $orderId): bool
    {
        try {
            $order = Order::with(['table', 'user'])->findOrFail($orderId);

            // Notificar al mesero que tomó el pedido
            if ($order->user) {
                $this->notifyWaiter($order->user->id, [
                    'type' => 'order_ready',
                    'title' => MessageConstants::ORDER_READY,
                    'message' => "El pedido #{$order->id} de la {$order->table->name} está listo para servir",
                    'order_id' => $order->id,
                    'extra_data' => [
                        'table_name' => $order->table->name,
                        'ready_at' => now()->format('H:i')
                    ]
                ]);
            }

            // También notificar a todos los meseros como respaldo
            $this->notifyAllWaiters([
                'type' => 'order_ready',
                'title' => MessageConstants::ORDER_READY,
                'message' => "Pedido #{$order->id} de la {$order->table->name} listo para servir",
                'order_id' => $order->id,
                'extra_data' => [
                    'table_name' => $order->table->name,
                    'ready_at' => now()->format('H:i')
                ]
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al notificar pedido listo', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Enviar notificación de stock bajo
     */
    public function notifyLowStock(int $ingredientId, float $currentStock, float $minimumStock): bool
    {
        try {
            $ingredient = Ingredient::findOrFail($ingredientId);

            // Notificar a administradores y encargados
            $managers = User::where('role', 'admin')
                          ->orWhere('role', 'manager')
                          ->get();

            foreach ($managers as $manager) {
                Notification::create([
                    'user_id' => $manager->id,
                    'type' => 'low_stock',
                    'title' => 'Stock Bajo',
                    'message' => "El ingrediente '{$ingredient->nombre}' tiene stock bajo ({$currentStock} {$ingredient->unit->nombre}). Mínimo requerido: {$minimumStock}",
                    'data' => json_encode([
                        'ingredient_id' => $ingredient->id,
                        'ingredient_name' => $ingredient->nombre,
                        'current_stock' => $currentStock,
                        'minimum_stock' => $minimumStock,
                        'unit' => $ingredient->unit->nombre
                    ])
                ]);
            }

            // Broadcast en tiempo real
            $this->broadcastLowStockAlert([
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->nombre,
                'current_stock' => $currentStock,
                'minimum_stock' => $minimumStock,
                'unit' => $ingredient->unit->nombre
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al notificar stock bajo', [
                'ingredient_id' => $ingredientId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar pedido listo con broadcasting en tiempo real
     */
    public function notifyOrderReadyRealtime(int $orderId): bool
    {
        try {
            $order = Order::with(['table', 'user'])->findOrFail($orderId);

            $notificationData = [
                'order_id' => $order->id,
                'order_code' => $order->code ?? "#{$order->id}",
                'table_name' => $order->table->name,
                'waiter_id' => $order->user_id,
                'ready_at' => now()->format('H:i')
            ];

            // Crear notificación en base de datos
            if ($order->user) {
                Notification::create([
                    'user_id' => $order->user->id,
                    'type' => 'kitchen_ready',
                    'title' => MessageConstants::ORDER_READY,
                    'message' => "El pedido #{$order->code} de la {$order->table->name} está listo para servir",
                    'data' => json_encode($notificationData),
                    'order_id' => $order->id
                ]);
            }

            // Broadcast en tiempo real
            $this->broadcastKitchenOrderReady($notificationData);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al notificar pedido listo en tiempo real', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar cambio de estado de pedido
     */
    public function notifyOrderStatusChange(int $orderId, string $newStatus, string $previousStatus = null): bool
    {
        try {
            $order = Order::with(['table', 'user'])->findOrFail($orderId);

            $notificationData = [
                'order_id' => $order->id,
                'order_code' => $order->code ?? "#{$order->id}",
                'status' => $newStatus,
                'previous_status' => $previousStatus,
                'table_name' => $order->table->name,
                'waiter_id' => $order->user_id,
                'updated_at' => now()->format('H:i')
            ];

            // Crear notificación solo para el mesero asignado
            if ($order->user) {
                Notification::create([
                    'user_id' => $order->user->id,
                    'type' => 'order_update',
                    'title' => 'Estado de Pedido Actualizado',
                    'message' => "El pedido #{$order->code} cambió a: {$newStatus}",
                    'data' => json_encode($notificationData),
                    'order_id' => $order->id
                ]);

                // Broadcast específico para el mesero
                $this->broadcastOrderStatusChange($notificationData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al notificar cambio de estado', [
                'order_id' => $orderId,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener estadísticas de notificaciones
     */
    public function getNotificationStats(int $userId): array
    {
        try {
            $unreadCount = Notification::where('user_id', $userId)
                                     ->whereNull('read_at')
                                     ->count();

            $todayCount = Notification::where('user_id', $userId)
                                    ->whereDate('created_at', today())
                                    ->count();

            $criticalCount = Notification::where('user_id', $userId)
                                       ->whereIn('type', ['kitchen_ready', 'low_stock'])
                                       ->whereNull('read_at')
                                       ->count();

            return [
                'unread_total' => $unreadCount,
                'today_total' => $todayCount,
                'critical_unread' => $criticalCount,
                'last_notification' => Notification::where('user_id', $userId)
                                                  ->latest()
                                                  ->first()
                                                  ?->created_at
                                                  ?->diffForHumans()
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas de notificaciones', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Broadcast para pedido listo desde cocina
     */
    private function broadcastKitchenOrderReady(array $data): void
    {
        try {
            // Broadcast general para kitchen updates
            broadcast(new \App\Events\KitchenOrderReady($data));

            // También enviar evento de Livewire
            Event::dispatch('notification.kitchen', $data);

            Log::info('Notificación de pedido listo enviada', $data);
        } catch (\Exception $e) {
            Log::error('Error en broadcast de pedido listo', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Broadcast para alerta de stock bajo
     */
    private function broadcastLowStockAlert(array $data): void
    {
        try {
            // Broadcast para inventory alerts
            broadcast(new \App\Events\LowStockAlert($data));

            Event::dispatch('notification.inventory', $data);

            Log::info('Alerta de stock bajo enviada', $data);
        } catch (\Exception $e) {
            Log::error('Error en broadcast de stock bajo', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Broadcast para cambio de estado de pedido
     */
    private function broadcastOrderStatusChange(array $data): void
    {
        try {
            // Broadcast específico para el mesero
            broadcast(new \App\Events\OrderStatusChanged($data));

            Event::dispatch('notification.order', $data);

            Log::info('Notificación de cambio de estado enviada', $data);
        } catch (\Exception $e) {
            Log::error('Error en broadcast de cambio de estado', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Limpiar notificaciones antiguas (más de 7 días)
     */
    public function cleanupOldNotifications(): int
    {
        try {
            $deletedCount = Notification::where('created_at', '<', now()->subDays(7))
                                      ->delete();

            Log::info("Limpieza de notificaciones completada", [
                'deleted_count' => $deletedCount
            ]);

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Error en limpieza de notificaciones', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Marcar todas las notificaciones como leídas para un usuario
     */
    public function markAllAsReadForUser(int $userId): bool
    {
        try {
            $updatedCount = Notification::where('user_id', $userId)
                                      ->whereNull('read_at')
                                      ->update(['read_at' => now()]);

            Log::info("Notificaciones marcadas como leídas", [
                'user_id' => $userId,
                'updated_count' => $updatedCount
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al marcar notificaciones como leídas', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Notificar a cocina cuando se crea un nuevo pedido
     */
    public function notifyNewOrderToKitchen(int $orderId): bool
    {
        try {
            $order = Order::with(['table', 'user', 'items.product'])->findOrFail($orderId);

            // Obtener todos los cocineros
            $cooks = User::whereHas('roles', function ($query) {
                $query->where('nombre', 'Cocinero');
            })->get();

            $itemsSummary = $order->items->map(function ($item) {
                return "{$item->quantity}x {$item->product->name}";
            })->join(', ');

            // Notificar a cada cocinero
            foreach ($cooks as $cook) {
                Notification::create([
                    'user_id' => $cook->id,
                    'type' => 'new_order',
                    'title' => '🔔 Nuevo Pedido',
                    'message' => "Nuevo pedido #{$order->id} de {$order->table->name} - {$itemsSummary}",
                    'data' => json_encode([
                        'order_id' => $order->id,
                        'table_name' => $order->table->name,
                        'waiter_name' => $order->user->name ?? 'Sin asignar',
                        'items_count' => $order->items->count(),
                        'created_at' => now()->format('H:i')
                    ]),
                    'order_id' => $order->id
                ]);
            }

            Log::info('Cocineros notificados de nuevo pedido', [
                'order_id' => $orderId,
                'cooks_notified' => $cooks->count()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al notificar nuevo pedido a cocina', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\WaiterServiceInterface;
use App\Contracts\NotificationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WaiterController extends Controller
{
    protected WaiterServiceInterface $waiterService;
    protected NotificationServiceInterface $notificationService;

    public function __construct(
        WaiterServiceInterface $waiterService,
        NotificationServiceInterface $notificationService
    ) {
        $this->waiterService = $waiterService;
        $this->notificationService = $notificationService;
    }

    /**
     * Obtener pedidos del mesero
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $waiterId = $request->get('waiter_id', Auth::id());
            $orders = $this->waiterService->getWaiterOrders($waiterId);

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_name' => $order->table->name ?? 'Sin mesa',
                    'table_id' => $order->table_id,
                    'status' => $this->getStatusDisplayName($order->status),
                    'status_enum' => $order->status,
                    'total' => $order->total ?? $this->calculateOrderTotal($order),
                    'items_count' => $order->items->count(),
                    'created_at' => $order->created_at->format('H:i'),
                    'time_since_order' => $order->created_at->diffForHumans(),
                    'waiting_time' => $order->created_at->diffInMinutes(now()),
                    'items_summary' => $order->items->map(function ($item) {
                        return [
                            'product_name' => $item->product->nombre ?? 'Producto no encontrado',
                            'quantity' => $item->quantity,
                            'price' => $item->product->precio ?? 0
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedOrders,
                'meta' => [
                    'total_orders' => $orders->count(),
                    'pending_orders' => $orders->where('status', 'pending')->count(),
                    'preparing_orders' => $orders->where('status', 'in_process')->count(),
                    'ready_orders' => $orders->where('status', 'served')->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pedidos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener pedidos listos para servir
     */
    public function getReadyOrders(Request $request): JsonResponse
    {
        try {
            $waiterId = $request->get('waiter_id', Auth::id());
            $orders = $this->waiterService->getReadyOrders($waiterId);

            $formattedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table_name' => $order->table->name ?? 'Sin mesa',
                    'table_id' => $order->table_id,
                    'total' => $order->total ?? $this->calculateOrderTotal($order),
                    'items_count' => $order->items->count(),
                    'ready_at' => $order->updated_at->format('H:i'),
                    'ready_since' => $order->updated_at->diffForHumans(),
                    'waiting_time' => $order->updated_at->diffInMinutes(now()),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedOrders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pedidos listos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar pedido como entregado
     */
    public function markAsDelivered(int $orderId): JsonResponse
    {
        try {
            $success = $this->waiterService->markOrderAsServed($orderId);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pedido marcado como entregado exitosamente',
                    'data' => [
                        'order_id' => $orderId,
                        'delivered_at' => now()->format('H:i'),
                        'notification' => "¡Pedido #{$orderId} entregado al cliente!"
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar el pedido como entregado'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar pedido como entregado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener notificaciones del mesero
     */
    public function getNotifications(Request $request): JsonResponse
    {
        try {
            $waiterId = $request->get('waiter_id');

            // Si no se especifica un mesero, obtener todas las notificaciones
            if (!$waiterId) {
                $notifications = $this->notificationService->getAllNotifications();
            } else {
                $notifications = $this->notificationService->getPendingNotifications($waiterId);
            }

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'meta' => [
                    'unread_count' => count($notifications)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error en getNotifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener notificaciones: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function markNotificationAsRead(int $notificationId): JsonResponse
    {
        try {
            $success = $this->notificationService->markAsRead($notificationId);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notificación marcada como leída'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar la notificación como leída'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas del mesero
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $waiterId = $request->get('waiter_id', Auth::id());
            $stats = $this->waiterService->getWaiterStats($waiterId);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener nombre del estado para mostrar
     */
    private function getStatusDisplayName(string $status): string
    {
        return match($status) {
            'pending' => 'Pendiente',
            'in_process' => 'En Preparación',
            'served' => 'Listo para Servir',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            default => 'Desconocido'
        };
    }

    /**
     * Calcular total del pedido
     */
    private function calculateOrderTotal($order): float
    {
        return $order->items->sum(function ($item) {
            return ($item->product->precio ?? 0) * $item->quantity;
        });
    }

    /**
     * Crear un nuevo pedido
     */
    public function createOrder(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'table_id' => 'required|exists:tables,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = $this->waiterService->createOrder([
                'table_id' => $request->table_id,
                'items' => $request->items,
                'notes' => $request->notes,
                'waiter_id' => $request->get('waiter_id', Auth::id()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => [
                    'order_id' => $order->id,
                    'table_name' => $order->table->name,
                    'total' => $order->getTotal(),
                    'items_count' => $order->items->count(),
                    'status' => $this->getStatusDisplayName($order->status),
                    'created_at' => $order->created_at->format('H:i'),
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creando pedido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modificar un pedido existente
     */
    public function updateOrder(Request $request, int $orderId): JsonResponse
    {
        try {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:500'
            ]);

            $order = $this->waiterService->updateOrder($orderId, [
                'items' => $request->items,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pedido actualizado exitosamente',
                'data' => [
                    'order_id' => $order->id,
                    'table_name' => $order->table->name,
                    'total' => $order->getTotal(),
                    'items_count' => $order->items->count(),
                    'status' => $this->getStatusDisplayName($order->status),
                    'updated_at' => $order->updated_at->format('H:i'),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error actualizando pedido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar pedido: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de un pedido específico
     */
    public function getOrderDetails(int $orderId): JsonResponse
    {
        try {
            $order = $this->waiterService->getOrderDetails($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'table_name' => $order->table->name,
                    'table_id' => $order->table_id,
                    'status' => $this->getStatusDisplayName($order->status),
                    'status_enum' => $order->status,
                    'total' => $order->getTotal(),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $order->updated_at->format('Y-m-d H:i:s'),
                    'can_modify' => in_array($order->status, ['pending', 'confirmed']),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name,
                            'product_price' => $item->product->price,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->product->price * $item->quantity,
                        ];
                    }),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error obteniendo detalles del pedido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalles del pedido: ' . $e->getMessage()
            ], 500);
        }
    }
}

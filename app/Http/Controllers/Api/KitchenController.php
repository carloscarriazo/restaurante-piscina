<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class KitchenController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Obtener órdenes para la cocina
     */
    public function orders(): JsonResponse
    {
        try {
            $orders = Order::with(['table', 'items.product', 'user'])
                ->whereIn('status', ['pending', 'in_process'])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'table_number' => $order->table->name ?? 'N/A',
                        'table_name' => $order->table->name ?? 'Sin mesa',
                        'status' => $order->status,
                        'status_display' => $this->getStatusDisplayName($order->status),
                        'total' => $order->total,
                        'created_at' => $order->created_at->format('H:i'),
                        'items_count' => $order->items->count(),
                        'customer_name' => $order->customer_name ?? 'Sin nombre',
                        'waiter_name' => $order->user->name ?? 'N/A',
                        'priority' => $this->calculateOrderPriority($order),
                        'time_elapsed' => $order->created_at->diffInMinutes(Carbon::now()),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $orders,
                'count' => $orders->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener órdenes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalles de una orden
     */
    public function orderDetails($orderId): JsonResponse
    {
        try {
            $order = Order::with(['table', 'items.product.category', 'user'])->findOrFail($orderId);

            $items = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'unit_price' => number_format($item->price, 2),
                    'total_price' => number_format($item->quantity * $item->price, 2),
                    'notes' => $item->notes ?? '',
                    'special_instructions' => '',
                    'product' => [
                        'name' => $item->product->name,
                        'description' => $item->product->description ?? '',
                        'category' => $item->product->category->name ?? 'Sin categoría',
                        'ingredients' => []
                    ]
                ];
            });

            $subtotal = $order->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });
            $tax = $subtotal * 0.10; // 10% de impuesto
            $total = $subtotal + $tax;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'table' => [
                        'number' => $order->table->id ?? 0,
                        'name' => $order->table->name ?? 'Sin mesa'
                    ],
                    'status' => [
                        'id' => $this->getStatusId($order->status),
                        'name' => $this->getStatusDisplayName($order->status)
                    ],
                    'timing' => [
                        'created_at' => $order->created_at->format('d/m/Y H:i'),
                        'time_since_order' => $order->created_at->diffForHumans(),
                        'waiting_minutes' => $order->created_at->diffInMinutes(Carbon::now()),
                        'priority' => $this->calculateOrderPriority($order)
                    ],
                    'customer_info' => [
                        'waiter' => $order->user->name ?? 'Sin asignar',
                        'notes' => $order->notes ?? ''
                    ],
                    'items' => $items,
                    'totals' => [
                        'subtotal' => number_format($subtotal, 2),
                        'tax' => number_format($tax, 2),
                        'total' => number_format($total, 2)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Empezar a preparar una orden
     */
    public function startPreparing($orderId): JsonResponse
    {
        try {
            $result = $this->orderService->changeStatus($orderId, 'in_process');

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al empezar preparación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar orden como lista
     */
    public function markAsReady($orderId): JsonResponse
    {
        try {
            $result = $this->orderService->changeStatus($orderId, 'served');

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como lista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificar meseros
     */
    public function notifyWaiters($orderId): JsonResponse
    {
        try {
            $order = Order::with(['table', 'user'])->findOrFail($orderId);

            // Marcar que la cocina ha notificado
            $order->update([
                'kitchen_notified' => true,
                'kitchen_notified_at' => Carbon::now()
            ]);

            // Aquí se podría implementar notificación push, websockets, etc.
            // Por ahora simulamos la notificación

            return response()->json([
                'success' => true,
                'message' => "Meseros notificados: Orden #{$orderId} de {$order->table->name} está lista para servir",
                'data' => [
                    'order_id' => $orderId,
                    'table_name' => $order->table->name,
                    'notified_at' => Carbon::now()->format('H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al notificar meseros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de cocina
     */
    public function kitchenStats(): JsonResponse
    {
        try {
            $today = Carbon::today();

            $stats = [
                'orders_pending' => Order::where('status', 'pending')->count(),
                'orders_in_process' => Order::where('status', 'in_process')->count(),
                'orders_ready' => Order::where('status', 'served')->whereDate('created_at', $today)->count(),
                'orders_today' => Order::whereDate('created_at', $today)->count(),
                'current_time' => Carbon::now()->format('H:i:s')
            ];

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

    // Métodos de utilidad

    private function getStatusDisplayName(string $status): string
    {
        $statuses = [
            'pending' => 'Pendiente',
            'in_process' => 'En Preparación',
            'served' => 'Lista',
            'cancelled' => 'Cancelada'
        ];

        return $statuses[$status] ?? ucfirst($status);
    }

    private function getStatusId(string $status): int
    {
        $statusIds = [
            'pending' => 2,
            'in_process' => 3,
            'served' => 4,
            'cancelled' => 5
        ];

        return $statusIds[$status] ?? 2;
    }

    private function calculateOrderPriority(Order $order): string
    {
        $minutesElapsed = $order->created_at->diffInMinutes(Carbon::now());

        if ($minutesElapsed > 30) {
            return 'urgente';
        }

        if ($minutesElapsed > 20) {
            return 'alta';
        }

        return $minutesElapsed > 10 ? 'media' : 'normal';
    }
}

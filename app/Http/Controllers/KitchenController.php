<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Exceptions\OrderException;
use App\Contracts\NotificationServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Mostrar la vista principal de cocina
     */
    public function dashboard()
    {
        return view('kitchen.dashboard');
    }

    /**
     * Vista de login específica para cocina (opcional)
     */
    public function login()
    {
        return view('kitchen.login');
    }

    /**
     * API: Test simple para verificar que funciona
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Kitchen API funcionando',
            'data' => []
        ]);
    }

    /**
     * API: Obtener todas las órdenes pendientes para la cocina
     */
    public function orders()
    {
        try {
            // Cargar órdenes con sus items y productos
            $orders = Order::with(['items.product'])
                ->whereNotIn('status', ['completed', 'cancelled', 'served'])
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders->map(function ($order) {
                    // Calcular prioridad basada en tiempo de espera
                    $minutesWaiting = $order->created_at->diffInMinutes(now());
                    $priority = 'normal';
                    if ($minutesWaiting > 30) { $priority = 'urgente'; }
                    elseif ($minutesWaiting > 20) { $priority = 'alta'; }
                    elseif ($minutesWaiting > 10) { $priority = 'media'; }

                    // Obtener items de la orden
                    $items = $order->items->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->product->name ?? 'Producto',
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'notes' => $item->notes ?? ''
                        ];
                    });

                    return [
                        'id' => $order->id,
                        'table_number' => $order->table_id,
                        'table_name' => 'Mesa ' . $order->table_id,
                        'table_id' => $order->table_id,
                        'status' => $order->status,
                        'status_id' => $this->getStatusId($order->status),
                        'priority' => $priority,
                        'created_at' => $order->created_at->format('H:i'),
                        'time_since_order' => $order->created_at->diffForHumans(),
                        'total' => $order->total ?? 0,
                        'items_count' => $items->count(),
                        'items' => $items,
                        'items_summary' => $items->pluck('name')->take(3)->toArray()
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las órdenes: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatusId($status)
    {
        $statusMap = [
            'pending' => 2,
            'in_process' => 3,
            'served' => 4,
            'cancelled' => 6
        ];

        return $statusMap[$status] ?? 2;
    }

    /**
     * API: Obtener detalles de una orden específica
     */
    public function orderDetails($orderId)
    {
        try {
            $order = Order::with(['items.product.recipes.ingredient', 'table'])
                ->findOrFail($orderId);

            // Calcular prioridad basada en tiempo de espera
            $minutesWaiting = $order->created_at->diffInMinutes(now());
            $priority = 'normal';
            if ($minutesWaiting > 30) { $priority = 'urgente'; }
            elseif ($minutesWaiting > 20) { $priority = 'alta'; }
            elseif ($minutesWaiting > 10) { $priority = 'media'; }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'table' => [
                        'number' => $order->table->number ?? $order->table_id,
                        'name' => $order->table->name ?? 'Mesa ' . $order->table_id
                    ],
                    'status' => [
                        'name' => ucfirst($order->status)
                    ],
                    'timing' => [
                        'priority' => $priority,
                        'created_at' => $order->created_at->format('H:i'),
                        'time_since_order' => $order->created_at->diffForHumans()
                    ],
                    'total' => number_format($order->total ?? 0, 2),
                    'totals' => [
                        'subtotal' => number_format(($order->total ?? 0) * 0.84, 2), // Asumiendo 16% de impuesto
                        'tax' => number_format(($order->total ?? 0) * 0.16, 2),
                        'total' => number_format($order->total ?? 0, 2)
                    ],
                    'status_id' => $this->getStatusId($order->status),
                    'timing' => [
                        'priority' => $priority,
                        'created_at' => $order->created_at->format('H:i'),
                        'time_since_order' => $order->created_at->diffForHumans(),
                        'waiting_minutes' => $minutesWaiting
                    ],
                    'customer_info' => [
                        'waiter' => $order->user->name ?? 'No asignado',
                        'notes' => $order->notes ?? null
                    ],
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => number_format($item->price ?? 0, 2),
                            'total_price' => number_format(($item->price ?? 0) * $item->quantity, 2),
                            'notes' => $item->notes,
                            'special_instructions' => $item->special_instructions,
                            'product' => [
                                'name' => $item->product->name,
                                'description' => $item->product->description,
                                'category' => $item->product->category->name ?? 'Sin categoría',
                                'ingredients' => $item->product->recipes->pluck('ingredient.name')->toArray()
                            ],
                            'ingredients_needed' => $item->product->recipes->map(function ($recipe) use ($item) {
                                $totalNeeded = $recipe->quantity * $item->quantity;
                                return [
                                    'name' => $recipe->ingredient->name,
                                    'needed_per_unit' => $recipe->quantity,
                                    'total_needed' => $totalNeeded,
                                    'available' => $recipe->ingredient->current_stock,
                                    'unit' => $recipe->ingredient->unit,
                                    'sufficient' => $recipe->ingredient->hasStock($totalNeeded)
                                ];
                            }),
                            'can_prepare' => $item->product->canBePrepared($item->quantity),
                            'missing_ingredients' => $item->product->getMissingIngredients($item->quantity)
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles de la orden: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * API: Iniciar preparación de una orden
     */
    public function startPreparing($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            $order->update([
                'status' => 'in_process'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Orden marcada como "En Preparación"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Marcar orden como lista y consumir ingredientes automáticamente
     */
    public function markAsReady($orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::with(['items.product.recipes.ingredient'])->findOrFail($orderId);

            // Verificar que todos los productos se pueden preparar
            foreach ($order->items as $item) {
                if (!$item->product->canBePrepared($item->quantity)) {
                    $missingIngredients = $item->product->getMissingIngredients($item->quantity);
                    $reason = implode(', ', array_map(function($ing) {
                        return $ing['ingredient']->name . " (faltan " . $ing['missing'] . " " . $ing['ingredient']->unit . ")";
                    }, $missingIngredients));
                    throw OrderException::cannotComplete($item->product->name, $reason);
                }
            }

            // Consumir ingredientes automáticamente
            foreach ($order->items as $item) {
                $item->product->consumeIngredients($item->quantity, $order->id, Auth::id());
            }

            // Actualizar estado de la orden
            $order->status = 'ready';
            $order->kitchen_notified_at = now();
            $order->save();

            // Notificar a los meseros que el pedido está listo
            $this->notificationService->notifyOrderReady($order->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden marcada como lista. Ingredientes descontados y meseros notificados.',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API: Notificar a los meseros que una orden está lista
     */
    public function notifyWaiters($orderId)
    {
        try {
            $order = Order::with('table')->findOrFail($orderId);

            // Aquí puedes implementar notificaciones push, websockets, etc.
            // Por ahora, solo actualizamos el timestamp
            $order->kitchen_notified_at = now();
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Meseros notificados exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al notificar meseros: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener estadísticas de cocina
     */
    public function kitchenStats()
    {
        try {
            $pendingOrders = Order::where('status', 'pending')->count();
            $preparingOrders = Order::where('status', 'in_process')->count();
            $completedToday = Order::whereDate('completed_at', today())->count();

            // Calcular tiempo promedio de preparación (órdenes completadas hoy)
            $avgTime = Order::whereDate('completed_at', today())
                ->whereNotNull('completed_at')
                ->whereNotNull('created_at')
                ->get()
                ->avg(function($order) {
                    return $order->created_at->diffInMinutes($order->completed_at);
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'pending_count' => $pendingOrders,
                    'preparing_count' => $preparingOrders,
                    'completed_today' => $completedToday,
                    'total_orders_today' => Order::whereDate('created_at', today())->count(),
                    'avg_preparation_time' => round($avgTime ?? 0, 0)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}

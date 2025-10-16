<?php

namespace App\Services;

use App\Contracts\WaiterServiceInterface;
use App\Contracts\NotificationServiceInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use App\Exceptions\OrderException;
use App\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WaiterService implements WaiterServiceInterface
{
    protected NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Obtener todos los pedidos asignados a un mesero
     */
    public function getWaiterOrders(int $waiterId): Collection
    {
        return Order::with(['table', 'items.product'])
            ->where('user_id', $waiterId)
            ->whereIn('status', ['pending', 'in_process', 'served'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener pedidos listos para servir de un mesero
     */
    public function getReadyOrders(int $waiterId): Collection
    {
        return Order::with(['table', 'items.product'])
            ->where('user_id', $waiterId)
            ->where('status', 'served')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Asignar pedido a un mesero
     */
    public function assignOrderToWaiter(int $orderId, int $waiterId): bool
    {
        try {
            $order = Order::findOrFail($orderId);
            return $order->update(['user_id' => $waiterId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Marcar pedido como servido por el mesero
     */
    public function markOrderAsServed(int $orderId): bool
    {
        try {
            $order = Order::findOrFail($orderId);

            // Solo se puede marcar como entregado si está en estado "served" (listo)
            if ($order->status !== 'served') {
                return false;
            }

            return $order->update([
                'status' => 'delivered',
                'delivered_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener estadísticas del mesero
     */
    public function getWaiterStats(int $waiterId): array
    {
        $today = Carbon::today();

        $orders = Order::where('user_id', $waiterId)
            ->whereDate('created_at', $today)
            ->get();

        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'delivered')->count();
        $pendingOrders = $orders->whereIn('status', ['pending', 'in_process'])->count();
        $readyOrders = $orders->where('status', 'served')->count();

        $totalSales = $orders->where('status', 'delivered')->sum(function ($order) {
            return $order->total ?? $this->calculateOrderTotal($order);
        });

        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'ready_orders' => $readyOrders,
            'total_sales' => $totalSales,
            'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0,
        ];
    }

    /**
     * Obtener mesas asignadas a un mesero
     */
    public function getWaiterTables(int $waiterId): Collection
    {
        // Por ahora retornamos todas las mesas
        // En el futuro se podría implementar asignación específica de mesas
        return Table::with(['orders' => function ($query) use ($waiterId) {
            $query->where('user_id', $waiterId)
                  ->whereIn('status', ['pending', 'in_process', 'served']);
        }])->get();
    }

    /**
     * Crear un nuevo pedido
     */
    public function createOrder(array $orderData)
    {
        try {
            DB::beginTransaction();

            // Crear el pedido
            $order = Order::create([
                'table_id' => $orderData['table_id'],
                'user_id' => $orderData['waiter_id'] ?? null,
                'status' => 'pending',
            ]);

            $total = 0;

            // Agregar items al pedido
            foreach ($orderData['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $subtotal = $product->price * $itemData['quantity'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $product->price,
                ]);
            }

            // Actualizar total del pedido
            // Nota: La tabla orders no tiene campo total, se calcula dinámicamente

            // Cargar relaciones
            $order->load(['table', 'items.product']);

            DB::commit();

            // Notificar a cocina del nuevo pedido
            $this->notificationService->notifyNewOrderToKitchen($order->id);

            Log::info('Pedido creado por mesero', [
                'order_id' => $order->id,
                'table_id' => $order->table_id,
                'waiter_id' => $orderData['waiter_id'] ?? null,
                'total' => $total
            ]);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creando pedido: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualizar un pedido existente
     */
    public function updateOrder(int $orderId, array $orderData)
    {
        try {
            DB::beginTransaction();

            $order = Order::with(['items', 'table'])->findOrFail($orderId);

            // Verificar que el pedido se puede modificar
            if (!in_array($order->status, ['pending', 'confirmed'])) {
                throw ValidationException::orderCannotBeModified($order->status);
            }

            // Eliminar items existentes
            $order->items()->delete();

            $total = 0;

            // Agregar nuevos items
            foreach ($orderData['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $subtotal = $product->price * $itemData['quantity'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $product->price,
                ]);
            }

            // Actualizar pedido
            $order->update([
                'updated_at' => now(),
            ]);

            // Recargar relaciones
            $order->load(['table', 'items.product']);

            DB::commit();

            Log::info('Pedido actualizado por mesero', [
                'order_id' => $order->id,
                'new_total' => $total
            ]);

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando pedido: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener detalles de un pedido
     */
    public function getOrderDetails(int $orderId)
    {
        try {
            return Order::with(['table', 'items.product', 'user'])
                ->findOrFail($orderId);
        } catch (\Exception $e) {
            Log::error('Error obteniendo detalles del pedido: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcular total del pedido
     */
    private function calculateOrderTotal(Order $order): float
    {
        return $order->items->sum(function ($item) {
            return ($item->product->precio ?? 0) * $item->quantity;
        });
    }
}

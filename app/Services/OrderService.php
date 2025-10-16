<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Table;
use App\Models\User;
use App\Exceptions\OrderException;
use App\Exceptions\ProductException;
use App\Exceptions\TableException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    /**
     * Obtener todos los pedidos con filtros
     */
    public function getAllOrders(array $filters = [])
    {
        $query = Order::with(['table', 'items.product', 'user']);

        // Aplicar filtros
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['table_id'])) {
            $query->where('table_id', $filters['table_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Crear un nuevo pedido
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            // Validar que la mesa esté disponible
            $table = Table::find($data['table_id']);
            if (!$table || $table->status !== 'available') {
                throw TableException::notAvailable();
            }

            // Crear el pedido
            $order = Order::create([
                'table_id' => $data['table_id'],
                'user_id' => Auth::id(),
                'status' => 'pending',
                'customer_name' => $data['customer_name'] ?? null,
                'notes' => $data['notes'] ?? null,
                'total' => 0,
                'is_editable' => true,
                'last_edited_at' => now(),
                'last_edited_by' => Auth::id(),
                'kitchen_notified' => false
            ]);

            // Ocupar la mesa
            $table->update(['status' => 'occupied']);

            DB::commit();

            return [
                'success' => true,
                'order' => $order,
                'message' => 'Pedido creado exitosamente'
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Agregar productos al pedido
     */
    public function addProduct($orderId, $productId, $quantity, $notes = null)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($orderId);
            if (!$order) {
                throw OrderException::notFound();
            }

            $product = Product::find($productId);
            if (!$product) {
                throw ProductException::notFound();
            }

            if (!$product->available) {
                throw ProductException::notAvailable();
            }

            // Verificar si el producto ya existe en el pedido
            $existingItem = OrderItem::where('order_id', $orderId)
                                   ->where('product_id', $productId)
                                   ->first();

            if ($existingItem) {
                // Actualizar cantidad
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $quantity,
                    'notes' => $notes ?? $existingItem->notes
                ]);
            } else {
                // Crear nuevo item
                OrderItem::create([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'notes' => $notes
                ]);
            }

            // Actualizar total del pedido
            $this->updateOrderTotal($order);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Producto agregado al pedido',
                'order' => $order->fresh(['items.product'])
            ];

        } catch (\Exception $e) {
            DB::rollback();
            return [
                'success' => false,
                'message' => 'Error al agregar producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener mesas disponibles
     */
    public function getAvailableTables()
    {
        return Table::where('status', 'available')
                   ->orderBy('name')
                   ->get();
    }

    /**
     * Obtener pedidos por mesero
     */
    public function getOrdersByWaiter($waiterId = null)
    {
        $waiterId = $waiterId ?? Auth::id();

        return Order::with(['table', 'items.product', 'user'])
                   ->where('user_id', $waiterId)
                   ->whereIn('status', ['pending', 'in_process'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Cambiar estado del pedido
     */
    public function changeStatus($orderId, $newStatus)
    {
        try {
            $order = Order::find($orderId);

            // Validar pedido y estado
            if (!$order || !in_array($newStatus, ['pending', 'in_process', 'served', 'cancelled'])) {
                $message = !$order ? 'Pedido no encontrado' : 'Estado no válido';
                return $this->errorResponse($message);
            }

            $order->update(['status' => $newStatus]);

            return [
                'success' => true,
                'message' => 'Estado del pedido actualizado correctamente',
                'order' => $order
            ];

        } catch (\Exception $e) {
            return $this->errorResponse('Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Helper para respuestas de error
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message
        ];
    }

    /**
     * Actualizar total del pedido
     */
    private function updateOrderTotal(Order $order)
    {
        $total = $order->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $order->update(['total' => $total]);
    }

    /**
     * Buscar pedido por ID
     */
    public function find(int $id)
    {
        return Order::with(['table', 'items.product', 'user'])->find($id);
    }

    /**
     * Actualizar estado del pedido
     */
    public function updateOrderStatus(int $orderId, string $status): array
    {
        try {
            $order = Order::findOrFail($orderId);
            $previousStatus = $order->status;

            // Validar estados permitidos
            $allowedStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'served', 'cancelled'];

            if (!in_array($status, $allowedStatuses)) {
                throw OrderException::invalidStatus($status);
            }

            // Actualizar estado
            $order->update(['status' => $status]);

            // Si se marca como servido, actualizar mesa
            if ($status === 'served') {
                $this->markTableAsOccupied($order->table_id, false);
            }

            // Log de la transacción
            $this->logTransaction('order_status_update', [
                'order_id' => $orderId,
                'previous_status' => $previousStatus,
                'new_status' => $status,
                'user_id' => Auth::id()
            ]);

            return $this->success('Estado del pedido actualizado correctamente', [
                'order_id' => $orderId,
                'previous_status' => $previousStatus,
                'new_status' => $status
            ]);

        } catch (\Exception $e) {
            $this->logError('Error al actualizar estado del pedido', $e, [
                'order_id' => $orderId,
                'status' => $status
            ]);

            return $this->error('Error al actualizar el estado del pedido: ' . $e->getMessage());
        }
    }

    /**
     * Marcar mesa como ocupada o libre
     */
    private function markTableAsOccupied(int $tableId, bool $occupied): void
    {
        $table = Table::find($tableId);
        if ($table) {
            $table->update([
                'is_occupied' => $occupied,
                'occupied_at' => $occupied ? now() : null,
                'cleaned_at' => !$occupied ? now() : $table->cleaned_at
            ]);
        }
    }

    /**
     * Obtener estadísticas de pedidos
     */
    public function getOrderStats(array $filters = []): array
    {
        try {
            $query = Order::query();

            // Aplicar filtros de fecha
            if (isset($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $totalOrders = $query->count();
            $totalRevenue = $query->sum('total');

            $statusStats = $query->selectRaw('status, COUNT(*) as count')
                                ->groupBy('status')
                                ->pluck('count', 'status')
                                ->toArray();

            $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            return [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'average_order_value' => $averageOrderValue,
                'status_breakdown' => $statusStats,
                'orders_by_hour' => $this->getOrdersByHour($filters),
                'top_products' => $this->getTopProducts($filters)
            ];

        } catch (\Exception $e) {
            $this->logError('Error al obtener estadísticas de pedidos', $e, $filters);
            return [];
        }
    }

    /**
     * Obtener pedidos por hora
     */
    private function getOrdersByHour(array $filters = []): array
    {
        $query = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                     ->groupBy('hour');

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->pluck('count', 'hour')->toArray();
    }

    /**
     * Obtener productos más vendidos
     */
    private function getTopProducts(array $filters = [], int $limit = 10): array
    {
        $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                         ->join('products', 'order_items.product_id', '=', 'products.id')
                         ->selectRaw('products.nombre, SUM(order_items.quantity) as total_quantity, SUM(order_items.total) as total_revenue')
                         ->groupBy('products.id', 'products.nombre')
                         ->orderBy('total_quantity', 'desc')
                         ->limit($limit);

        if (isset($filters['date_from'])) {
            $query->whereDate('orders.created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('orders.created_at', '<=', $filters['date_to']);
        }

        return $query->get()->toArray();
    }

    /**
     * Editar un pedido existente
     */
    public function editOrder(Order $order, User $user, array $data): Order
    {
        DB::beginTransaction();
        try {
            // Verificar permisos
            if (!$order->canBeEditedBy($user)) {
                throw OrderException::notEditable();
            }

            // Actualizar datos del pedido
            if (isset($data['table_id'])) {
                $order->table_id = $data['table_id'];
            }

            if (isset($data['notes'])) {
                $order->notes = $data['notes'];
            }

            // Si hay items, actualizar
            if (isset($data['items'])) {
                // Eliminar items actuales
                $order->items()->delete();

                // Agregar nuevos items
                foreach ($data['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->precio,
                        'subtotal' => $product->precio * $item['quantity'],
                        'total' => $product->precio * $item['quantity'],
                    ]);
                }
            }

            // Recalcular totales
            $order->calculateTotals();
            $order->save();

            DB::commit();
            return $order->fresh(['items.product', 'table']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Combinar facturación de múltiples pedidos
     */
    public function combineBilling(array $orderIds, User $user): Order
    {
        DB::beginTransaction();
        try {
            // Obtener todos los pedidos
            $orders = Order::whereIn('id', $orderIds)->get();

            if ($orders->isEmpty()) {
                throw OrderException::noOrdersToCombin();
            }

            // Verificar que todos los pedidos se puedan combinar
            foreach ($orders as $order) {
                if ($order->status === 'completed' || $order->status === 'cancelled') {
                    throw OrderException::cannotCombine($order->id, $order->status);
                }
            }

            // Crear pedido combinado
            $firstOrder = $orders->first();
            $combinedOrder = Order::create([
                'table_id' => $firstOrder->table_id,
                'user_id' => $user->id,
                'status' => 'pending',
                'notes' => 'Pedido combinado de: ' . $orders->pluck('id')->join(', '),
            ]);

            // Transferir todos los items
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    $combinedOrder->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'subtotal' => $item->subtotal,
                        'total' => $item->total,
                    ]);
                }

                // Cancelar pedido original
                $order->update(['status' => 'cancelled']);
            }

            // Calcular totales
            $combinedOrder->calculateTotals();
            $combinedOrder->save();

            DB::commit();
            return $combinedOrder->fresh(['items.product', 'table']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Verificar y aplicar descuentos diarios
     */
    public function checkAndApplyDailyDiscounts(Order $order): bool
    {
        // Aquí puedes implementar la lógica de descuentos diarios
        // Por ahora retornamos false

        return false;
    }
}

<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderRepository implements OrderRepositoryInterface
{
    public function getAllWithFilters(Request $request)
    {
        $query = Order::with(['table', 'items.product', 'status', 'payment']);

        // Aplicar filtros
        if ($request->has('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->has('table_id')) {
            $query->where('table_id', $request->table_id);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        return $query->latest()->paginate(20);
    }

    public function findById(int $id)
    {
        return Order::with(['table', 'items.product', 'status', 'payment'])->findOrFail($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getKitchenOrders()
    {
        return Order::with(['table', 'items.product'])
            ->whereIn('status_id', [1, 2]) // Pendiente y En preparación
            ->latest()
            ->get();
    }

    public function getOrdersByTable(int $tableId)
    {
        return Order::with(['items.product', 'status', 'payment'])
            ->where('table_id', $tableId)
            ->latest()
            ->get();
    }

    public function updateStatus(Order $order, int $statusId): Order
    {
        $order->update(['status_id' => $statusId]);
        return $order->fresh();
    }
}

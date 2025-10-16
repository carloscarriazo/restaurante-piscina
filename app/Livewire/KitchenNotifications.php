<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OrderService;
use App\Services\NotificationService;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class KitchenNotifications extends Component
{
    public $orders = [];
    public $selectedOrder = null;
    public $showOrderDetails = false;

    protected $listeners = [
        'order.new' => 'loadOrders',
        'order.updated' => 'loadOrders',
        'kitchen.refresh' => 'loadOrders'
    ];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Cargar pedidos en preparación y listos
        $this->orders = Order::with(['table', 'user', 'orderItems.product'])
                            ->whereIn('status', ['preparing', 'ready'])
                            ->orderBy('created_at', 'asc')
                            ->get()
                            ->map(function ($order) {
                                return [
                                    'id' => $order->id,
                                    'code' => $order->code ?? "#{$order->id}",
                                    'table_name' => $order->table->name,
                                    'waiter_name' => $order->user->name ?? 'Sin asignar',
                                    'status' => $order->status,
                                    'created_at' => $order->created_at->format('H:i'),
                                    'time_elapsed' => $order->created_at->diffForHumans(),
                                    'priority' => $this->calculatePriority($order),
                                    'items_count' => $order->orderItems->count(),
                                    'total' => $order->total
                                ];
                            })->toArray();
    }

    public function markAsReady($orderId)
    {
        try {
            $orderService = app(OrderService::class);
            $notificationService = app(NotificationService::class);

            // Actualizar estado del pedido
            $result = $orderService->updateOrderStatus($orderId, 'ready');

            if ($result['success']) {
                // Enviar notificación en tiempo real
                $notificationService->notifyOrderReadyRealtime($orderId);

                // Recargar órdenes
                $this->loadOrders();

                // Mostrar mensaje de éxito
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'Pedido Listo',
                    'message' => 'El pedido ha sido marcado como listo y se notificó al mesero'
                ]);

                // Cerrar detalles si es el pedido actual
                if ($this->selectedOrder && $this->selectedOrder['id'] === $orderId) {
                    $this->showOrderDetails = false;
                    $this->selectedOrder = null;
                }
            } else {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $result['message'] ?? 'No se pudo actualizar el pedido'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    public function viewOrderDetails($orderId)
    {
        $order = Order::with(['table', 'user', 'orderItems.product'])
                     ->findOrFail($orderId);

        $this->selectedOrder = [
            'id' => $order->id,
            'code' => $order->code ?? "#{$order->id}",
            'table_name' => $order->table->name,
            'waiter_name' => $order->user->name ?? 'Sin asignar',
            'status' => $order->status,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
            'notes' => $order->notes,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product->nombre,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                    'notes' => $item->notes
                ];
            })->toArray(),
            'total' => $order->total
        ];

        $this->showOrderDetails = true;
    }

    public function closeOrderDetails()
    {
        $this->showOrderDetails = false;
        $this->selectedOrder = null;
    }

    public function refreshOrders()
    {
        $this->loadOrders();

        $this->dispatch('show-toast', [
            'type' => 'info',
            'title' => 'Actualizado',
            'message' => 'Lista de pedidos actualizada'
        ]);
    }

    private function calculatePriority(Order $order): string
    {
        $minutesElapsed = $order->created_at->diffInMinutes(now());

        if ($minutesElapsed > 30) {
            return 'high';
        } elseif ($minutesElapsed > 15) {
            return 'medium';
        }

        return 'low';
    }

    public function getOrdersByStatus($status)
    {
        return array_filter($this->orders, function ($order) use ($status) {
            return $order['status'] === $status;
        });
    }

    public function render()
    {
        return view('livewire.kitchen-notifications', [
            'preparingOrders' => $this->getOrdersByStatus('preparing'),
            'readyOrders' => $this->getOrdersByStatus('ready')
        ]);
    }
}
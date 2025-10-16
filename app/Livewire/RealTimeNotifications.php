<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class RealTimeNotifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showNotifications = false;

    protected $listeners = [
        'notification.new' => 'loadNotifications',
        'notification.refresh' => 'loadNotifications',
        'echo:kitchen-updates,KitchenOrderReady' => 'handleKitchenNotification',
        'echo:inventory-alerts,LowStockAlert' => 'handleInventoryAlert',
        'echo:order-updates,OrderStatusChanged' => 'handleOrderUpdate'
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', Auth::id())
                                         ->orWhereNull('user_id') // Notificaciones generales
                                         ->orderBy('created_at', 'desc')
                                         ->take(10)
                                         ->get()
                                         ->toArray();

        $this->unreadCount = Notification::where('user_id', Auth::id())
                                       ->whereNull('read_at')
                                       ->count();
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;

        if ($this->showNotifications) {
            $this->markAllAsRead();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && ($notification->user_id === Auth::id() || $notification->user_id === null)) {
            $notification->update(['read_at' => now()]);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                   ->whereNull('read_at')
                   ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function handleKitchenNotification($event)
    {
        $this->dispatch('notification.kitchen', $event);

        // Crear notificación local
        Notification::create([
            'user_id' => Auth::id(),
            'type' => 'kitchen_ready',
            'title' => 'Pedido Listo',
            'message' => "El pedido #{$event['order_code']} está listo para servir",
            'data' => json_encode($event),
            'order_id' => $event['order_id'] ?? null
        ]);

        $this->loadNotifications();

        // Mostrar notificación toast
        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'Pedido Listo',
            'message' => "Pedido #{$event['order_code']} listo en cocina"
        ]);
    }

    public function handleInventoryAlert($event)
    {
        $this->dispatch('notification.inventory', $event);

        // Solo crear notificación para administradores y encargados
        if (in_array(Auth::user()->role, ['admin', 'manager'])) {
            Notification::create([
                'user_id' => Auth::id(),
                'type' => 'low_stock',
                'title' => 'Stock Bajo',
                'message' => "El ingrediente '{$event['ingredient_name']}' tiene stock bajo",
                'data' => json_encode($event)
            ]);

            $this->loadNotifications();

            $this->dispatch('show-toast', [
                'type' => 'warning',
                'title' => 'Alerta de Stock',
                'message' => "Stock bajo: {$event['ingredient_name']}"
            ]);
        }
    }

    public function handleOrderUpdate($event)
    {
        $this->dispatch('notification.order', $event);

        // Solo notificar si es el mesero del pedido
        if ($event['waiter_id'] === Auth::id()) {
            Notification::create([
                'user_id' => Auth::id(),
                'type' => 'order_update',
                'title' => 'Actualización de Pedido',
                'message' => "El pedido #{$event['order_code']} cambió a: {$event['status']}",
                'data' => json_encode($event),
                'order_id' => $event['order_id'] ?? null
            ]);

            $this->loadNotifications();

            $this->dispatch('show-toast', [
                'type' => 'info',
                'title' => 'Pedido Actualizado',
                'message' => "#{$event['order_code']}: {$event['status']}"
            ]);
        }
    }

    public function clearNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && ($notification->user_id === Auth::id() || $notification->user_id === null)) {
            $notification->delete();
            $this->loadNotifications();
        }
    }

    public function clearAllNotifications()
    {
        Notification::where('user_id', Auth::id())->delete();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.real-time-notifications');
    }
}
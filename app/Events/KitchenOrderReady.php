<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitchenOrderReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderData;

    /**
     * Create a new event instance.
     */
    public function __construct(array $orderData)
    {
        $this->orderData = $orderData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen-updates'),
            // Canal específico para el mesero
            new Channel('user.' . $this->orderData['waiter_id'])
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderData['order_id'],
            'order_code' => $this->orderData['order_code'],
            'table_name' => $this->orderData['table_name'],
            'waiter_id' => $this->orderData['waiter_id'],
            'ready_at' => $this->orderData['ready_at'],
            'type' => 'kitchen_ready',
            'timestamp' => now()->timestamp
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'KitchenOrderReady';
    }
}
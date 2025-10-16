<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stockData;

    /**
     * Create a new event instance.
     */
    public function __construct(array $stockData)
    {
        $this->stockData = $stockData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('inventory-alerts'),
            new Channel('management-alerts') // Para administradores
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'ingredient_id' => $this->stockData['ingredient_id'],
            'ingredient_name' => $this->stockData['ingredient_name'],
            'current_stock' => $this->stockData['current_stock'],
            'minimum_stock' => $this->stockData['minimum_stock'],
            'unit' => $this->stockData['unit'],
            'type' => 'low_stock',
            'timestamp' => now()->timestamp
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'LowStockAlert';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'name',
        'capacity',
        'status',
        'location',
        'occupied_at',
        'cleaned_at',
        'is_available',
        'waiter_id',
        'assigned_at'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'occupied_at' => 'datetime',
        'cleaned_at' => 'datetime',
        'assigned_at' => 'datetime'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function currentOrder()
    {
        return $this->hasOne(Order::class)->where('status', '!=', 'completed')->latest();
    }

    public function currentOrders()
    {
        return $this->hasMany(Order::class)->whereIn('status', ['pending', 'preparing', 'ready']);
    }

    /**
     * Verificar si la mesa está ocupada
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Verificar si la mesa está disponible
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_available;
    }

    /**
     * Asignar un mesero a la mesa
     */
    public function assignWaiter(int $waiterId): void
    {
        $this->update([
            'waiter_id' => $waiterId,
            'assigned_at' => now()
        ]);
    }

    /**
     * Remover asignación de mesero
     */
    public function unassignWaiter(): void
    {
        $this->update([
            'waiter_id' => null,
            'assigned_at' => null
        ]);
    }

    /**
     * Verificar si la mesa está asignada a un mesero
     */
    public function isAssigned(): bool
    {
        return $this->waiter_id !== null;
    }

    /**
     * Verificar si la mesa está asignada a un mesero específico
     */
    public function isAssignedTo(int $waiterId): bool
    {
        return $this->waiter_id === $waiterId;
    }
}

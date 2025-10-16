<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     * Admin y meseros pueden ver todas las órdenes
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'waiter']);
    }

    /**
     * Determine whether the user can view the model.
     * Admin, meseros y el creador de la orden pueden verla
     */
    public function view(User $user, Order $order): bool
    {
        return $user->hasRole(['admin', 'waiter']) || $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     * Admin y meseros pueden crear órdenes
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'waiter']);
    }

    /**
     * Determine whether the user can update the model.
     * Admin y el mesero que creó la orden pueden actualizarla
     * Solo si la orden no está completada o cancelada
     */
    public function update(User $user, Order $order): bool
    {
        if ($order->status === 'completed' || $order->status === 'cancelled') {
            return false;
        }

        return $user->hasRole('admin') || $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo admin puede eliminar órdenes
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     * Solo admin puede restaurar órdenes eliminadas
     */
    public function restore(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Solo admin puede forzar eliminación permanente
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can mark order as delivered.
     * Solo meseros y admin
     */
    public function markDelivered(User $user, Order $order): bool
    {
        return $user->hasRole(['admin', 'waiter']);
    }

    /**
     * Determine whether the user can process payment.
     * Solo admin y cajeros
     */
    public function processPayment(User $user, Order $order): bool
    {
        return $user->hasRole(['admin', 'cashier']);
    }
}

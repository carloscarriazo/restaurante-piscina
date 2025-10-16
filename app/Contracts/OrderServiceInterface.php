<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\User;

interface OrderServiceInterface
{
    /**
     * Crear una nueva orden
     */
    public function createOrder(array $orderData): Order;

    /**
     * Editar una orden existente
     */
    public function editOrder(Order $order, User $user, array $orderData): Order;

    /**
     * Aplicar descuentos a una orden
     */
    public function applyDiscounts(Order $order): Order;

    /**
     * Calcular total de orden
     */
    public function calculateOrderTotal(Order $order): float;

    /**
     * Procesar pago de orden
     */
    public function processPayment(Order $order, array $paymentData): bool;

    /**
     * Combinar órdenes para facturación
     */
    public function combineBilling(array $orderIds): array;
}

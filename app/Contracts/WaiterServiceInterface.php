<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface WaiterServiceInterface
{
    /**
     * Obtener todos los pedidos asignados a un mesero
     */
    public function getWaiterOrders(int $waiterId): Collection;

    /**
     * Obtener pedidos listos para servir de un mesero
     */
    public function getReadyOrders(int $waiterId): Collection;

    /**
     * Asignar pedido a un mesero
     */
    public function assignOrderToWaiter(int $orderId, int $waiterId): bool;

    /**
     * Marcar pedido como servido por el mesero
     */
    public function markOrderAsServed(int $orderId): bool;

    /**
     * Crear un nuevo pedido
     */
    public function createOrder(array $orderData);

    /**
     * Actualizar un pedido existente
     */
    public function updateOrder(int $orderId, array $orderData);

    /**
     * Obtener detalles de un pedido
     */
    public function getOrderDetails(int $orderId);

    /**
     * Obtener estadísticas del mesero
     */
    public function getWaiterStats(int $waiterId): array;

    /**
     * Obtener mesas asignadas a un mesero
     */
    public function getWaiterTables(int $waiterId): Collection;
}

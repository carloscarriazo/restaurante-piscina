<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

interface OrderRepositoryInterface
{
    /**
     * Obtener todas las órdenes con filtros
     */
    public function getAllWithFilters(Request $request);

    /**
     * Obtener una orden por ID
     */
    public function findById(int $id);

    /**
     * Crear una nueva orden
     */
    public function create(array $data): Order;

    /**
     * Actualizar una orden
     */
    public function update(Order $order, array $data): Order;

    /**
     * Eliminar una orden
     */
    public function delete(Order $order): bool;

    /**
     * Obtener órdenes de cocina
     */
    public function getKitchenOrders();

    /**
     * Obtener órdenes por mesa
     */
    public function getOrdersByTable(int $tableId);

    /**
     * Actualizar estado de orden
     */
    public function updateStatus(Order $order, int $statusId): Order;
}

<?php

namespace App\Contracts;

interface NotificationServiceInterface
{
    /**
     * Enviar notificación a un mesero específico
     */
    public function notifyWaiter(int $waiterId, array $data): bool;

    /**
     * Enviar notificación a todos los meseros
     */
    public function notifyAllWaiters(array $data): bool;

    /**
     * Obtener notificaciones pendientes para un mesero
     */
    public function getPendingNotifications(int $waiterId): array;

    /**
     * Obtener todas las notificaciones
     */
    public function getAllNotifications(): array;

    /**
     * Marcar notificación como leída
     */
    public function markAsRead(int $notificationId): bool;

    /**
     * Notificar que un pedido está listo
     */
    public function notifyOrderReady(int $orderId): bool;

    /**
     * Notificar a cocina cuando se crea un nuevo pedido
     */
    public function notifyNewOrderToKitchen(int $orderId): bool;
}

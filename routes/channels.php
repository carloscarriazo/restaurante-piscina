<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal general para actualizaciones de cocina
Broadcast::channel('kitchen-updates', function ($user) {
    // Solo meseros y administradores pueden escuchar actualizaciones de cocina
    return in_array($user->role, ['waiter', 'admin', 'manager']);
});

// Canal para alertas de inventario
Broadcast::channel('inventory-alerts', function ($user) {
    // Solo administradores y encargados pueden ver alertas de inventario
    return in_array($user->role, ['admin', 'manager']);
});

// Canal para alertas de gestión
Broadcast::channel('management-alerts', function ($user) {
    // Solo administradores y encargados
    return in_array($user->role, ['admin', 'manager']);
});

// Canal para actualizaciones de pedidos
Broadcast::channel('order-updates', function ($user) {
    // Todos los usuarios autenticados pueden escuchar actualizaciones
    return $user !== null;
});

// Canal específico por usuario
Broadcast::channel('user.{userId}', function ($user, $userId) {
    // Solo el usuario específico puede escuchar su canal
    return (int) $user->id === (int) $userId;
});

// Canal específico por mesa
Broadcast::channel('table.{tableId}', function ($user, $tableId) {
    // Meseros pueden escuchar actualizaciones de sus mesas asignadas
    return $user->role === 'waiter' || in_array($user->role, ['admin', 'manager']);
});

// Canal para estadísticas en tiempo real
Broadcast::channel('dashboard-stats', function ($user) {
    // Solo administradores y encargados pueden ver estadísticas en tiempo real
    return in_array($user->role, ['admin', 'manager']);
});

// Canal para notificaciones generales del sistema
Broadcast::channel('system-notifications', function ($user) {
    // Todos los usuarios autenticados
    return $user !== null;
});
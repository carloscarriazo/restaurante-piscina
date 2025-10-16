<?php

namespace App\Constants;

class RouteConstants
{
    public const AUTH_SANCTUM = 'auth:sanctum';
    public const ORDERS_PATH = '/orders';
    public const STATS_PATH = '/stats';
    public const DASHBOARD_PATH = '/dashboard';
    public const ID_PARAM = '/{id}';

    // Paths de logs
    public const LARAVEL_LOG_PATH = 'logs/laravel.log';

    // Rutas compuestas comunes
    public const ORDERS_ID_DETAILS = '/orders/{id}/details';
    public const ORDERS_ID_START_PREPARING = '/orders/{id}/start-preparing';
    public const ORDERS_ID_MARK_READY = '/orders/{id}/mark-ready';
    public const ORDERS_ID_NOTIFY_WAITERS = '/orders/{id}/notify-waiters';
}

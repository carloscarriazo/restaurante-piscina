<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class ShareNotifications
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo ejecutar si el usuario está autenticado
        if (Auth::check()) {
            try {
                // Obtener estadísticas de notificaciones
                $notificationStats = $this->notificationService->getNotificationStats(Auth::id());

                // Compartir con todas las vistas
                View::share([
                    'userNotificationStats' => $notificationStats,
                    'hasUnreadNotifications' => $notificationStats['unread_total'] > 0,
                    'unreadNotificationsCount' => $notificationStats['unread_total']
                ]);

            } catch (\Exception $e) {
                // En caso de error, compartir valores por defecto
                View::share([
                    'userNotificationStats' => [
                        'unread_total' => 0,
                        'today_total' => 0,
                        'critical_unread' => 0,
                        'last_notification' => null
                    ],
                    'hasUnreadNotifications' => false,
                    'unreadNotificationsCount' => 0
                ]);
            }
        }

        return $next($request);
    }
}
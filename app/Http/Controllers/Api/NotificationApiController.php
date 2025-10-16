<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class NotificationApiController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Obtener notificaciones del usuario autenticado
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $notifications = Notification::where('user_id', $user->id)
                                       ->orWhereNull('user_id') // Notificaciones generales
                                       ->orderBy('created_at', 'desc')
                                       ->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications->items(),
                    'pagination' => [
                        'current_page' => $notifications->currentPage(),
                        'last_page' => $notifications->lastPage(),
                        'per_page' => $notifications->perPage(),
                        'total' => $notifications->total(),
                    ]
                ],
                'message' => 'Notificaciones obtenidas correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de notificaciones
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->notificationService->getNotificationStats(Auth::id());

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Estadísticas obtenidas correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function markAsRead(int $notificationId): JsonResponse
    {
        try {
            $success = $this->notificationService->markAsRead($notificationId);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notificación marcada como leída'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo marcar la notificación como leída'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $success = $this->notificationService->markAllAsReadForUser(Auth::id());

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Todas las notificaciones marcadas como leídas'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron marcar las notificaciones'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(int $notificationId): JsonResponse
    {
        try {
            $notification = Notification::findOrFail($notificationId);

            // Verificar que la notificación pertenezca al usuario o sea general
            if ($notification->user_id !== Auth::id() && $notification->user_id !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar esta notificación'
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notificación eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar notificación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener notificaciones en tiempo real (para long polling)
     */
    public function realtime(Request $request): JsonResponse
    {
        try {
            $lastCheck = $request->get('last_check');
            $timeout = min($request->get('timeout', 30), 60); // Máximo 60 segundos

            $startTime = time();

            do {
                $query = Notification::where('user_id', Auth::id())
                                   ->orWhereNull('user_id');

                if ($lastCheck) {
                    $query->where('created_at', '>', $lastCheck);
                }

                $newNotifications = $query->orderBy('created_at', 'desc')
                                         ->limit(10)
                                         ->get();

                if ($newNotifications->count() > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'notifications' => $newNotifications,
                            'has_new' => true,
                            'timestamp' => now()
                        ],
                        'message' => 'Nuevas notificaciones disponibles'
                    ]);
                }

                // Esperar un poco antes de verificar de nuevo
                sleep(2);

            } while (time() - $startTime < $timeout);

            // No hay nuevas notificaciones
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => [],
                    'has_new' => false,
                    'timestamp' => now()
                ],
                'message' => 'No hay nuevas notificaciones'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en consulta en tiempo real: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar notificación de prueba (solo para testing)
     */
    public function testNotification(Request $request): JsonResponse
    {
        try {
            // Solo permitir en desarrollo o para administradores
            if (app()->environment('production') && Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado'
                ], 403);
            }

            $success = $this->notificationService->notifyWaiter(Auth::id(), [
                'type' => 'test',
                'title' => 'Notificación de Prueba',
                'message' => 'Esta es una notificación de prueba enviada a las ' . now()->format('H:i:s'),
                'extra_data' => [
                    'test_data' => 'Datos de prueba',
                    'timestamp' => now()
                ]
            ]);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Notificación de prueba enviada' : 'Error al enviar notificación'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar notificación de prueba: ' . $e->getMessage()
            ], 500);
        }
    }
}
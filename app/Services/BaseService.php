<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * BaseService - Servicio base para funcionalidades compartidas
 * Contiene métodos genéricos que pueden ser utilizados por otros services
 */
class BaseService
{
    /**
     * Ejecuta una transacción de base de datos de forma segura
     */
    public function executeTransaction(callable $callback)
    {
        try {
            return DB::transaction($callback);
        } catch (Exception $e) {
            Log::error('Error en transacción: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Registra actividad del sistema
     */
    public function logActivity(string $action, string $module, array $data = [])
    {
        Log::info("Actividad: {$action} en {$module}", [
            'user_id' => Auth::id(),
            'data' => $data,
            'timestamp' => now()
        ]);
    }

    /**
     * Valida datos requeridos
     */
    public function validateRequiredFields(array $data, array $required): bool
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Campo requerido faltante: {$field}");
            }
        }
        return true;
    }

    /**
     * Formatea respuesta estándar para API
     */
    public function formatApiResponse(bool $success, $data = null, string $message = '', int $code = 200): array
    {
        return [
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'code' => $code,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Aplica filtros de búsqueda genéricos
     */
    public function applyFilters($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                if (is_string($value)) {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->where($field, $value);
                }
            }
        }
        return $query;
    }

    /**
     * Genera código único para entidades
     */
    public function generateUniqueCode(string $prefix, Model $model, string $field = 'code'): string
    {
        do {
            $code = $prefix . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while ($model::where($field, $code)->exists());

        return $code;
    }

    /**
     * Calcula porcentajes y estadísticas
     */
    public function calculatePercentage(float $part, float $total): float
    {
        if ($total == 0) return 0;
        return round(($part / $total) * 100, 2);
    }

    /**
     * Notifica a usuarios específicos
     */
    public function notifyUsers(array $userIds, string $title, string $message, array $data = [])
    {
        // Implementar sistema de notificaciones
        Log::info('Notificación enviada', [
            'users' => $userIds,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Verifica permisos de usuario
     */
    public function checkPermission(string $permission): bool
    {
        return Auth::check() && Auth::user() && method_exists(Auth::user(), 'hasPermission') && Auth::user()->hasPermission($permission);
    }

    /**
     * Retorna respuesta de éxito estándar
     */
    protected function success(string $message, $data = null): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()
        ];
    }

    /**
     * Retorna respuesta de error estándar
     */
    protected function error(string $message, $data = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()
        ];
    }

    /**
     * Registra error en logs
     */
    protected function logError(string $message, Exception $exception, array $context = []): void
    {
        Log::error($message, [
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'user_id' => auth()->check() ? auth()->id() : null,
            'timestamp' => now()
        ]);
    }

    /**
     * Registra transacción en logs
     */
    protected function logTransaction(string $action, array $data = []): void
    {
        Log::info("Transacción: {$action}", [
            'user_id' => auth()->check() ? auth()->id() : null,
            'data' => $data,
            'timestamp' => now()
        ]);
    }
}
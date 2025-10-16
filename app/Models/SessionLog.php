<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'details',
        'module',
        'permission',
        'success'
    ];

    protected $casts = [
        'details' => 'array',
        'success' => 'boolean'
    ];

    /**
     * Usuario que realizó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log de una acción del usuario
     */
    public static function logAction(
        ?int $userId,
        string $action,
        bool $success = true,
        ?string $module = null,
        ?string $permission = null,
        ?array $details = null
    ): void {
        static::create([
            'user_id' => $userId,
            'action' => $action,
            'success' => $success,
            'module' => $module,
            'permission' => $permission,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Obtener logs por usuario
     */
    public static function getByUser(int $userId, int $limit = 50)
    {
        return static::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Obtener logs recientes
     */
    public static function getRecent(int $limit = 100)
    {
        return static::with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }
}

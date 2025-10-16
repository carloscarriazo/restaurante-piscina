<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Los roles que tienen este permiso
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Obtener permisos por módulo
     */
    public static function getByModule(string $module)
    {
        return static::where('module', $module)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
    }

    /**
     * Verificar si el permiso está activo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}

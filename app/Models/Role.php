<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Get the users associated with the role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Los permisos asignados a este rol
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Asignar un permiso al rol
     */
    public function givePermission(Permission $permission): void
    {
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revocar un permiso del rol
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission->id);
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()
                   ->where('slug', $permissionSlug)
                   ->where('is_active', true)
                   ->exists();
    }
}

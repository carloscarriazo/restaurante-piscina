<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasAnyRole(array $roleNames)
 */
class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Mesas asignadas al usuario (si es mesero)
     */
    public function tables()
    {
        return $this->hasMany(Table::class, 'waiter_id');
    }

    /**
     * Logs de sesión del usuario
     */
    public function sessionLogs()
    {
        return $this->hasMany(SessionLog::class);
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereHas('permissions', function($query) use ($permission) {
            $query->where('permissions.name', $permission)->where('permissions.is_active', true);
        })->exists();
    }

    /**
     * Verificar si el usuario tiene algún rol específico
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('nombre', $roleName)->exists();
    }

    /**
     * Obtener todos los permisos del usuario
     */
    public function getAllPermissions()
    {
        return Permission::whereHas('roles', function($query) {
            $query->whereIn('roles.id', $this->roles()->pluck('id'));
        })->where('is_active', true)->get();
    }

    /**
     * Verificar si puede acceder a un módulo
     */
    public function canAccessModule(string $module): bool
    {
        return $this->getAllPermissions()->where('module', $module)->isNotEmpty();
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }
}

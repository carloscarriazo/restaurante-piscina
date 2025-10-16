<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class CleanRolesSeeder extends Seeder
{
    private const KEEP_ROLES = ['Administrador', 'Mesero', 'Cocina'];

    private const ROLE_MAPPING = [
        'Cocinero' => 'Cocina',
        'Gerente' => 'Administrador',
        'Contador' => 'Administrador',
        'AuxiliarGerente' => 'Administrador'
    ];

    public function run(): void
    {
        $this->migrateUsersFromMappedRoles();
        $this->cleanObsoleteRoles();
        $this->command->info('Limpieza de roles completada. Solo quedan: ' . implode(', ', self::KEEP_ROLES));
    }

    private function migrateUsersFromMappedRoles(): void
    {
        foreach (self::ROLE_MAPPING as $oldRoleName => $newRoleName) {
            $oldRole = Role::where('nombre', $oldRoleName)->first();
            $newRole = Role::where('nombre', $newRoleName)->first();

            if ($oldRole && $newRole) {
                $this->migrateUsersToNewRole($oldRole, $newRole);
                $oldRole->delete();
                $this->command->info("Rol '{$oldRoleName}' eliminado");
            }
        }
    }

    private function migrateUsersToNewRole(Role $oldRole, Role $newRole): void
    {
        foreach ($oldRole->users as $user) {
            $user->roles()->detach($oldRole->id);

            if (!$this->userHasRole($user, $newRole->id)) {
                $user->roles()->attach($newRole->id);
            }

            $this->command->info("Usuario {$user->name} migrado de '{$oldRole->nombre}' a '{$newRole->nombre}'");
        }
    }

    private function cleanObsoleteRoles(): void
    {
        $obsoleteRoles = Role::whereNotIn('nombre', self::KEEP_ROLES)->get();
        $adminRole = Role::where('nombre', 'Administrador')->first();

        foreach ($obsoleteRoles as $role) {
            if ($role->users()->exists()) {
                $this->migrateUsersToAdmin($role, $adminRole);
            }

            $role->delete();
            $this->command->info("Rol obsoleto '{$role->nombre}' eliminado");
        }
    }

    private function migrateUsersToAdmin(Role $oldRole, Role $adminRole): void
    {
        foreach ($oldRole->users as $user) {
            $user->roles()->detach($oldRole->id);

            if (!$this->userHasRole($user, $adminRole->id)) {
                $user->roles()->attach($adminRole->id);
            }

            $this->command->info("Usuario {$user->name} migrado a Administrador desde rol obsoleto");
        }
    }

    private function userHasRole(User $user, int $roleId): bool
    {
        return $user->roles()->where('role_id', $roleId)->exists();
    }
}

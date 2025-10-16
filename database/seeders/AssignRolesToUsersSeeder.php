<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::where('nombre', 'Administrador')->first();
        $kitchenRole = Role::where('nombre', 'Cocina')->first();
        $waiterRole = Role::where('nombre', 'Mesero')->first();

        if (!$adminRole || !$kitchenRole || !$waiterRole) {
            $this->command->error('Los roles no están creados. Ejecuta primero PermissionSeeder.');
            return;
        }

        // Asignar roles a usuarios específicos
        $users = [
            1 => $adminRole,    // Admin Principal -> Administrador
            2 => $kitchenRole,  // Chef Cocinero -> Cocina
            3 => $adminRole,    // Gerente General -> Administrador
            4 => $waiterRole,   // Mesero Uno -> Mesero
            7 => $waiterRole,   // Mesero Uno -> Mesero
            8 => $waiterRole,   // Mesero Dos -> Mesero
            9 => $waiterRole,   // Mesero Tres -> Mesero
            10 => $waiterRole,  // Mesero Cuatro -> Mesero
            11 => $waiterRole,  // Mesero Cinco -> Mesero
            12 => $waiterRole,  // Mesero Seis -> Mesero
        ];

        foreach ($users as $userId => $role) {
            $user = User::find($userId);
            if ($user) {
                // Limpiar roles existentes antes de asignar nuevo
                $user->roles()->detach();
                $user->roles()->attach($role->id);
                $this->command->info("Rol '{$role->nombre}' asignado a {$user->name}");
            }
        }

        $this->command->info('Roles asignados exitosamente a todos los usuarios');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permisos del módulo de Usuarios
        $userPermissions = [
            ['name' => 'Ver Usuarios', 'slug' => 'users.view', 'module' => 'users', 'description' => 'Ver lista de usuarios'],
            ['name' => 'Crear Usuarios', 'slug' => 'users.create', 'module' => 'users', 'description' => 'Crear nuevos usuarios'],
            ['name' => 'Editar Usuarios', 'slug' => 'users.edit', 'module' => 'users', 'description' => 'Editar información de usuarios'],
            ['name' => 'Eliminar Usuarios', 'slug' => 'users.delete', 'module' => 'users', 'description' => 'Eliminar usuarios del sistema'],
            ['name' => 'Gestionar Roles', 'slug' => 'users.roles', 'module' => 'users', 'description' => 'Asignar y modificar roles de usuarios'],
        ];

        // Permisos del módulo de Productos
        $productPermissions = [
            ['name' => 'Ver Productos', 'slug' => 'products.view', 'module' => 'products', 'description' => 'Ver lista de productos'],
            ['name' => 'Crear Productos', 'slug' => 'products.create', 'module' => 'products', 'description' => 'Crear nuevos productos'],
            ['name' => 'Editar Productos', 'slug' => 'products.edit', 'module' => 'products', 'description' => 'Editar información de productos'],
            ['name' => 'Eliminar Productos', 'slug' => 'products.delete', 'module' => 'products', 'description' => 'Eliminar productos'],
        ];

        // Permisos del módulo de Inventario
        $inventoryPermissions = [
            ['name' => 'Ver Inventario', 'slug' => 'inventory.view', 'module' => 'inventory', 'description' => 'Ver inventario y stock'],
            ['name' => 'Gestionar Stock', 'slug' => 'inventory.manage', 'module' => 'inventory', 'description' => 'Añadir y restar stock'],
            ['name' => 'Ver Reportes', 'slug' => 'inventory.reports', 'module' => 'inventory', 'description' => 'Ver reportes de inventario'],
        ];

        // Permisos del módulo de Órdenes
        $orderPermissions = [
            ['name' => 'Ver Órdenes', 'slug' => 'orders.view', 'module' => 'orders', 'description' => 'Ver lista de órdenes'],
            ['name' => 'Crear Órdenes', 'slug' => 'orders.create', 'module' => 'orders', 'description' => 'Crear nuevas órdenes'],
            ['name' => 'Editar Órdenes', 'slug' => 'orders.edit', 'module' => 'orders', 'description' => 'Editar órdenes existentes'],
            ['name' => 'Procesar Órdenes', 'slug' => 'orders.process', 'module' => 'orders', 'description' => 'Marcar órdenes como listas'],
        ];

        // Permisos del módulo de Cocina
        $kitchenPermissions = [
            ['name' => 'Ver Cocina', 'slug' => 'kitchen.view', 'module' => 'kitchen', 'description' => 'Acceder al panel de cocina'],
            ['name' => 'Marcar Listos', 'slug' => 'kitchen.ready', 'module' => 'kitchen', 'description' => 'Marcar platos como listos'],
        ];

        // Permisos del módulo de Mesas
        $tablePermissions = [
            ['name' => 'Ver Mesas', 'slug' => 'tables.view', 'module' => 'tables', 'description' => 'Ver estado de las mesas'],
            ['name' => 'Gestionar Mesas', 'slug' => 'tables.manage', 'module' => 'tables', 'description' => 'Crear, editar y eliminar mesas'],
        ];

        // Permisos de Configuración
        $configPermissions = [
            ['name' => 'Ver Configuración', 'slug' => 'config.view', 'module' => 'config', 'description' => 'Acceder a configuración del sistema'],
            ['name' => 'Gestionar Permisos', 'slug' => 'config.permissions', 'module' => 'config', 'description' => 'Gestionar permisos y roles'],
            ['name' => 'Ver Logs', 'slug' => 'config.logs', 'module' => 'config', 'description' => 'Ver logs de actividad del sistema'],
        ];

        $allPermissions = array_merge(
            $userPermissions,
            $productPermissions,
            $inventoryPermissions,
            $orderPermissions,
            $kitchenPermissions,
            $tablePermissions,
            $configPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::create($permission);
        }

        // Crear roles básicos si no existen
        $adminRole = Role::firstOrCreate(['nombre' => 'Administrador']);
        $waiterRole = Role::firstOrCreate(['nombre' => 'Mesero']);
        $kitchenRole = Role::firstOrCreate(['nombre' => 'Cocina']);

        // Asignar todos los permisos al administrador
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Asignar permisos específicos al mesero
        $waiterPermissions = Permission::whereIn('slug', [
            'orders.view', 'orders.create', 'orders.edit',
            'tables.view', 'tables.manage',
            'products.view'
        ])->pluck('id');
        $waiterRole->permissions()->sync($waiterPermissions);

        // Asignar permisos específicos a cocina
        $kitchenPermissions = Permission::whereIn('slug', [
            'kitchen.view', 'kitchen.ready',
            'orders.view', 'inventory.view'
        ])->pluck('id');
        $kitchenRole->permissions()->sync($kitchenPermissions);

        $this->command->info('Permisos y roles creados exitosamente');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class KitchenTestOrdersSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener datos necesarios
        $table1 = Table::where('name', 'Mesa 1')->first();
        $table2 = Table::where('name', 'Mesa 2')->first();
        $user = User::where('email', 'mesero@bluelagoon.com')->first();

        $products = Product::take(5)->get();

        if (!$table1 || !$table2 || !$user || $products->count() < 3) {
            $this->command->error('No se encontraron los datos necesarios. Asegúrate de que existan mesas "Mesa 1" y "Mesa 2", usuario mesero y productos.');
            return;
        }

        // Crear órdenes de prueba para cocina usando enum status
        $testOrders = [
            [
                'table_id' => $table1->id,
                'user_id' => $user->id,
                'status' => 'pending', // Enum pending
                'total' => 50.05,
                'notes' => 'Sin cebolla, por favor',
                'created_at' => Carbon::now()->subMinutes(25), // Hace 25 minutos (prioridad ALTA)
                'items' => [
                    ['product' => $products[0], 'cantidad' => 2, 'notas' => 'Término medio'],
                    ['product' => $products[1], 'cantidad' => 1, 'notas' => 'Extra picante'],
                ]
            ],
            [
                'table_id' => $table2->id,
                'user_id' => $user->id,
                'status' => 'in_process', // Enum in_process
                'total' => 35.20,
                'notes' => 'Cliente alérgico a mariscos',
                'created_at' => Carbon::now()->subMinutes(15), // Hace 15 minutos (prioridad MEDIA)
                'items' => [
                    ['product' => $products[2], 'cantidad' => 1, 'notas' => ''],
                    ['product' => $products[3], 'cantidad' => 2, 'notas' => 'Sin sal'],
                ]
            ],
            [
                'table_id' => $table1->id,
                'user_id' => $user->id,
                'status' => 'pending', // Enum pending
                'total' => 73.98,
                'notes' => 'Mesa de cumpleaños',
                'created_at' => Carbon::now()->subMinutes(35), // Hace 35 minutos (prioridad URGENTE)
                'items' => [
                    ['product' => $products[0], 'cantidad' => 1, 'notas' => 'Bien cocido'],
                    ['product' => $products[1], 'cantidad' => 2, 'notas' => ''],
                    ['product' => $products[4], 'cantidad' => 1, 'notas' => 'Con helado'],
                ]
            ],
            [
                'table_id' => $table2->id,
                'user_id' => $user->id,
                'status' => 'pending', // Enum pending
                'total' => 31.35,
                'notes' => '',
                'created_at' => Carbon::now()->subMinutes(8), // Hace 8 minutos (prioridad NORMAL)
                'items' => [
                    ['product' => $products[2], 'cantidad' => 2, 'notas' => 'Con limón'],
                ]
            ]
        ];

        foreach ($testOrders as $orderData) {
            // Crear la orden
            $order = Order::create([
                'table_id' => $orderData['table_id'],
                'user_id' => $orderData['user_id'],
                'status' => $orderData['status'],
                'total' => $orderData['total'],
                'notes' => $orderData['notes'],
                'created_at' => $orderData['created_at'],
                'updated_at' => $orderData['created_at'],
            ]);

            // Crear los items de la orden
            foreach ($orderData['items'] as $itemData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemData['product']->id,
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['product']->precio,
                    'precio_total' => $itemData['product']->precio * $itemData['cantidad'],
                    'notas' => $itemData['notas'],
                ]);
            }

            $this->command->info("Orden #{$order->id} creada para {$order->table->name} - Status: {$order->status}");
        }

        $this->command->info('✅ Órdenes de prueba para cocina creadas exitosamente');
    }
}

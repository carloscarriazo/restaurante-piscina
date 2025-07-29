<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí puedes definir los estados de pedido que deseas insertar en la tabla 'order_statuses'
        $orderStatuses = [
            ['nombre' => 'Pendiente', 'descripcion' => 'El pedido está pendiente de ser procesado.'],
            ['nombre' => 'En preparación', 'descripcion' => 'El pedido está siendo preparado.'],
            ['nombre' => 'Listo para recoger', 'descripcion' => 'El pedido está listo para ser recogido por el cliente.'],
            ['nombre' => 'Entregado', 'descripcion' => 'El pedido ha sido entregado al cliente.'],
            ['nombre' => 'Cancelado', 'descripcion' => 'El pedido ha sido cancelado.'],
        ];

        // Inserta los estados de pedido en la base de datos
        foreach ($orderStatuses as $status) {
            DB::table('order_statuses')->insert($status);
        }
    }
}

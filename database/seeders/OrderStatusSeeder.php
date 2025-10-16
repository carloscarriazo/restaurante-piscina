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
        // Estados de pedido para el sistema de cocina
        $orderStatuses = [
            ['nombre' => 'Pendiente', 'descripcion' => 'El pedido está pendiente de ser procesado.'],
            ['nombre' => 'Confirmada', 'descripcion' => 'El pedido ha sido confirmado y está listo para cocinar.'],
            ['nombre' => 'En Preparación', 'descripcion' => 'El pedido está siendo preparado en cocina.'],
            ['nombre' => 'Pagada', 'descripcion' => 'El pedido ha sido pagado completamente.'],
            ['nombre' => 'Lista', 'descripcion' => 'El pedido está listo para ser servido al cliente.'],
            ['nombre' => 'Entregada', 'descripcion' => 'El pedido ha sido entregado al cliente.'],
            ['nombre' => 'Cancelada', 'descripcion' => 'El pedido ha sido cancelado.'],
        ];

        // Inserta los estados de pedido en la base de datos
        foreach ($orderStatuses as $status) {
            DB::table('order_statuses')->updateOrInsert(
                ['nombre' => $status['nombre']],
                $status
            );
        }
    }
}

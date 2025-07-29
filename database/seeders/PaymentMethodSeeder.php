<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí puedes definir los métodos de pago que deseas insertar en la tabla 'payment_methods'
        $paymentMethods = [
            ['nombre' => 'Efectivo'],
            ['nombre' => 'Tarjeta de crédito'],
            ['nombre' => 'Tarjeta de débito'],
            ['nombre' => 'Transferencia bancaria'],
            ['nombre' => 'Pago móvil'],
        ];

        // Inserta los métodos de pago en la base de datos
        foreach ($paymentMethods as $method) {
            DB::table('payment_methods')->insert($method);
        }
    }
}

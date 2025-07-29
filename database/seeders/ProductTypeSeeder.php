<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí puedes definir los tipos de productos que deseas insertar en la tabla 'product_types'
        $productTypes = [
            ['name' => 'Normal', 'description' => 'Producto individual'],
            ['name' => 'Combo', 'description' => 'Incluye varios productos'],
            ['name' => 'Servicio', 'description' => 'Ej. entrada piscina, alquiler'],
            ['name' => 'Ingrediente', 'description' => 'Solo para recetas'],
            ['name' => 'Bebida', 'description' => 'Ej. gaseosa, cerveza'],
        ];

        // Inserta los tipos de productos en la base de datos
        foreach ($productTypes as $type) {
            DB::table('product_types')->insert($type);
        }
    }
}

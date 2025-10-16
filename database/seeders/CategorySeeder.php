<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Aquí puedes definir las categorías que deseas insertar en la tabla 'categories'
        \App\Models\Category::create([
            'nombre' => 'Entradas',
            'descripcion' => 'Aperitivos y entradas',
        ]);

        \App\Models\Category::create([
            'nombre' => 'Platos Principales',
            'descripcion' => 'Platos principales variados',
        ]);

        \App\Models\Category::create([
            'nombre' => 'Bebidas',
            'descripcion' => 'Bebidas alcohólicas y no alcohólicas',
        ]);

        \App\Models\Category::create([
            'nombre' => 'Postres',
            'descripcion' => 'Deliciosos postres para finalizar la comida',
        ]);
    }
}

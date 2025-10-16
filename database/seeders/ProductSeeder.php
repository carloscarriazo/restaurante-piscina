<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear categorías
        $categories = [
            ['nombre' => 'Entradas', 'descripcion' => 'Platos para empezar'],
            ['nombre' => 'Platos Principales', 'descripcion' => 'Platos fuertes'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes'],
            ['nombre' => 'Postres', 'descripcion' => 'Dulces y postres'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['nombre' => $categoryData['nombre']],
                $categoryData
            );

            // Productos por categoría
            if ($category->nombre === 'Entradas') {
                $products = [
                    ['name' => 'Empanadas de Carne', 'description' => 'Empanadas artesanales de carne', 'price' => 15000, 'stock' => 50],
                    ['name' => 'Arepa con Queso', 'description' => 'Arepa tradicional con queso', 'price' => 8000, 'stock' => 30],
                    ['name' => 'Patacones', 'description' => 'Plátano verde frito', 'price' => 12000, 'stock' => 25],
                ];
            } elseif ($category->nombre === 'Platos Principales') {
                $products = [
                    ['name' => 'Bandeja Paisa', 'description' => 'Plato típico antioqueño', 'price' => 35000, 'stock' => 20],
                    ['name' => 'Sancocho de Pollo', 'description' => 'Sopa tradicional colombiana', 'price' => 28000, 'stock' => 15],
                    ['name' => 'Pescado a la Plancha', 'description' => 'Pescado fresco con ensalada', 'price' => 32000, 'stock' => 12],
                ];
            } elseif ($category->nombre === 'Bebidas') {
                $products = [
                    ['name' => 'Limonada Natural', 'description' => 'Limonada fresca', 'price' => 8000, 'stock' => 100],
                    ['name' => 'Cerveza Nacional', 'description' => 'Cerveza fría', 'price' => 5000, 'stock' => 80],
                    ['name' => 'Agua Panela', 'description' => 'Bebida tradicional', 'price' => 6000, 'stock' => 60],
                ];
            } elseif ($category->nombre === 'Postres') {
                $products = [
                    ['name' => 'Tres Leches', 'description' => 'Torta tres leches casera', 'price' => 12000, 'stock' => 15],
                    ['name' => 'Flan de Coco', 'description' => 'Flan tradicional de coco', 'price' => 10000, 'stock' => 20],
                ];
            }

            foreach ($products as $productData) {
                Product::firstOrCreate(
                    ['name' => $productData['name']],
                    [
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'category_id' => $category->id,
                        'stock' => $productData['stock'],
                        'available' => true,
                        'active' => true
                    ]
                );
            }
        }
    }
}

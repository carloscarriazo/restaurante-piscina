<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyDiscount;
use App\Models\Product;

class DailyDiscountSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener o crear una categoría por defecto
        $defaultCategory = \App\Models\Category::firstOrCreate(
            ['nombre' => 'Promociones'],
            ['descripcion' => 'Productos en promoción']
        );

        // Obtener algunos productos para los descuentos
        $products = Product::where('price', '<=', 3500)->take(3)->get();

        if ($products->isEmpty()) {
            // Si no hay productos, crear algunos de ejemplo
            $exampleProducts = [
                ['name' => 'Bebida Refrescante', 'price' => 2500],
                ['name' => 'Postre del Día', 'price' => 3000],
                ['name' => 'Aperitivo Especial', 'price' => 3500],
            ];

            foreach ($exampleProducts as $productData) {
                Product::firstOrCreate(
                    ['name' => $productData['name']],
                    [
                        'price' => $productData['price'],
                        'description' => 'Producto de ejemplo para descuentos',
                        'category_id' => $defaultCategory->id,
                        'unit' => 'unidad',
                        'active' => true
                    ]
                );
            }

            $products = Product::where('price', '<=', 3500)->take(3)->get();
        }

        // Crear descuentos para los próximos días
        $dates = [
            now()->toDateString(),
            now()->addDay()->toDateString(),
            now()->addDays(2)->toDateString(),
        ];

        foreach ($dates as $index => $date) {
            if ($products->count() > $index) {
                DailyDiscount::firstOrCreate([
                    'discount_date' => $date,
                    'product_id' => $products[$index]->id,
                ], [
                    'minimum_purchase' => 5000,
                    'product_max_price' => 3500,
                    'discount_amount' => 1000, // $1000 de descuento
                    'discount_percentage' => 0,
                    'is_active' => true,
                    'description' => 'Descuento especial del día - Compra mínima $5.000'
                ]);
            }
        }

        // Crear un descuento por porcentaje
        if ($products->count() > 0) {
            DailyDiscount::firstOrCreate([
                'discount_date' => now()->addDays(3)->toDateString(),
                'product_id' => $products->first()->id,
            ], [
                'minimum_purchase' => 8000,
                'product_max_price' => 3500,
                'discount_amount' => 0,
                'discount_percentage' => 15, // 15% de descuento
                'is_active' => true,
                'description' => 'Descuento del 15% - Compra mínima $8.000'
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuExampleSeeder extends Seeder
{
    /**
     * Seed de ejemplo para el menú digital
     *
     * Ejecutar con: php artisan db:seed --class=MenuExampleSeeder
     */
    public function run(): void
    {
        // Crear categorías de ejemplo
        $categories = [
            [
                'name' => 'platos_principales',
                'display_name' => 'Platos Principales',
                'description' => 'Nuestros mejores platos tradicionales',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'bebidas',
                'display_name' => 'Bebidas',
                'description' => 'Refrescantes bebidas naturales y gaseosas',
                'color' => '#4ECDC4',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'postres',
                'display_name' => 'Postres',
                'description' => 'Deliciosos postres caseros',
                'color' => '#FFE66D',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            MenuCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }

        // Obtener categorías creadas
        $platosCategory = MenuCategory::where('name', 'platos_principales')->first();
        $bebidasCategory = MenuCategory::where('name', 'bebidas')->first();
        $postresCategory = MenuCategory::where('name', 'postres')->first();

        // Crear ítems de ejemplo
        $items = [
            // Platos Principales
            [
                'menu_category_id' => $platosCategory->id,
                'name' => 'Sancocho de Gallina',
                'description' => 'Tradicional sancocho con arroz, yuca, plátano y papa',
                'price' => 16000,
                'size' => 'Porción completa',
                'ingredients' => 'Gallina criolla, yuca, plátano, papa, mazorca, arroz',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'operating_days' => [5, 6, 0], // Viernes, Sábado, Domingo
            ],
            [
                'menu_category_id' => $platosCategory->id,
                'name' => 'Mojarra Frita',
                'description' => 'Mojarra fresca del día con patacones y ensalada',
                'price' => 18000,
                'size' => '450gr aprox',
                'ingredients' => 'Mojarra fresca, patacones, ensalada, arroz',
                'is_available' => true,
                'is_featured' => false,
                'sort_order' => 2,
                'operating_days' => [5, 6, 0],
            ],
            [
                'menu_category_id' => $platosCategory->id,
                'name' => 'Cazuela de Mariscos',
                'description' => 'Exquisita cazuela con mariscos frescos en salsa criolla',
                'price' => 22000,
                'size' => 'Para 2 personas',
                'ingredients' => 'Camarones, calamar, pescado, leche de coco, yuca, plátano',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'operating_days' => [6, 0], // Solo Sábado y Domingo
            ],

            // Bebidas
            [
                'menu_category_id' => $bebidasCategory->id,
                'name' => 'Limonada Natural',
                'description' => 'Refrescante limonada con limones frescos',
                'price' => 4000,
                'size' => '500ml',
                'ingredients' => 'Limón, agua, azúcar, hielo',
                'is_available' => true,
                'is_featured' => false,
                'sort_order' => 1,
                'operating_days' => [5, 6, 0],
            ],
            [
                'menu_category_id' => $bebidasCategory->id,
                'name' => 'Jugo de Corozo',
                'description' => 'Tradicional jugo de corozo bien frío',
                'price' => 5000,
                'size' => '500ml',
                'ingredients' => 'Corozo, leche, azúcar, hielo',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'operating_days' => [5, 6, 0],
            ],
            [
                'menu_category_id' => $bebidasCategory->id,
                'name' => 'Cerveza Club Colombia',
                'description' => 'Cerveza colombiana bien fría',
                'price' => 3500,
                'size' => '330ml',
                'is_available' => true,
                'is_featured' => false,
                'sort_order' => 3,
                'operating_days' => [5, 6, 0],
            ],

            // Postres
            [
                'menu_category_id' => $postresCategory->id,
                'name' => 'Enyucado Casero',
                'description' => 'Delicioso enyucado tradicional recién horneado',
                'price' => 3000,
                'size' => 'Porción',
                'ingredients' => 'Yuca, coco, queso, mantequilla',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'operating_days' => [6, 0], // Solo fin de semana
            ],
            [
                'menu_category_id' => $postresCategory->id,
                'name' => 'Cocadas de Leche',
                'description' => 'Tradicionales cocadas sucrenses',
                'price' => 2000,
                'size' => '3 unidades',
                'ingredients' => 'Coco, leche condensada, canela',
                'is_available' => true,
                'is_featured' => false,
                'sort_order' => 2,
                'operating_days' => [5, 6, 0],
            ],
        ];

        foreach ($items as $itemData) {
            MenuItem::updateOrCreate(
                [
                    'menu_category_id' => $itemData['menu_category_id'],
                    'name' => $itemData['name']
                ],
                $itemData
            );
        }

        $this->command->info('✅ Menú de ejemplo creado exitosamente!');
        $this->command->info('📝 Categorías creadas: ' . MenuCategory::count());
        $this->command->info('🍽️  Ítems creados: ' . MenuItem::count());
        $this->command->info('');
        $this->command->info('🌐 Ver menú en: /menu/digital');
        $this->command->info('⚙️  Gestionar en: /menu/manage');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Constants\MessageConstants;
use App\Constants\ValidationRules;

class MenuSeeder extends Seeder
{
    private array $categories = [];

    public function run(): void
    {
        $this->seedCategories();
        $this->seedAsados();
        $this->seedPicadas();
        $this->seedPescadosYSancocho();
        $this->seedJugosYBebidas();
        $this->seedMicheladas();
    }

    private function seedCategories(): void
    {
        $this->categories['asados'] = MenuCategory::create([
            'name' => 'asados',
            'display_name' => 'Asados',
            'description' => 'Carnes y pescados frescos a la parrilla',
            'color' => '#FF6B6B',
            'sort_order' => 1
        ]);

        $this->categories['picadas'] = MenuCategory::create([
            'name' => 'picadas',
            'display_name' => 'Picadas',
            'description' => 'Picadas especiales para compartir',
            'color' => '#4ECDC4',
            'sort_order' => 2
        ]);

        $this->categories['pescados'] = MenuCategory::create([
            'name' => 'pescados',
            'display_name' => 'Pescados',
            'description' => 'Pescados frescos del día',
            'color' => '#45B7D1',
            'sort_order' => 3
        ]);

        $this->categories['sancocho'] = MenuCategory::create([
            'name' => 'sancocho',
            'display_name' => 'Sancocho del Día',
            'description' => 'Nuestro sancocho tradicional',
            'color' => '#96CEB4',
            'sort_order' => 4
        ]);

        $this->categories['micheladas'] = MenuCategory::create([
            'name' => 'micheladas',
            'display_name' => 'Micheladas',
            'description' => 'Micheladas refrescantes',
            'color' => '#FFEAA7',
            'sort_order' => 5
        ]);

        $this->categories['gaseosas'] = MenuCategory::create([
            'name' => 'gaseosas',
            'display_name' => 'Gaseosas',
            'description' => 'Bebidas refrescantes',
            'color' => '#DDA0DD',
            'sort_order' => 6
        ]);

        $this->categories['bebidas'] = MenuCategory::create([
            'name' => 'bebidas_bar',
            'display_name' => 'Bebidas Bar',
            'description' => 'Licores y cocteles',
            'color' => '#F39C12',
            'sort_order' => 7
        ]);

        $this->categories['jugos'] = MenuCategory::create([
            'name' => 'jugos_naturales',
            'display_name' => 'Jugos Naturales',
            'description' => 'Jugos naturales y frappe',
            'color' => '#2ECC71',
            'sort_order' => 8
        ]);
    }

    private function seedAsados(): void
    {
        $asados = [
            ['name' => 'Pechuga', 'size' => ValidationRules::SIZE_250GR, 'price' => 22, 'sort_order' => 1],
            ['name' => 'Pechuga Jr', 'size' => ValidationRules::SIZE_160GR, 'price' => 16, 'sort_order' => 2],
            ['name' => 'Cerdo', 'size' => ValidationRules::SIZE_250GR, 'price' => 23, 'sort_order' => 3],
            ['name' => 'Cerdo Jr', 'size' => ValidationRules::SIZE_160GR, 'price' => 17, 'sort_order' => 4],
            ['name' => 'Carne', 'size' => ValidationRules::SIZE_250GR, 'price' => 24, 'sort_order' => 5],
            ['name' => 'Carne Jr', 'size' => ValidationRules::SIZE_160GR, 'price' => 18, 'sort_order' => 6],
            ['name' => 'Churrasco Res', 'price' => 26, 'sort_order' => 7],
            ['name' => 'Churrasco Cerdo', 'price' => 26, 'sort_order' => 8],
            ['name' => 'Costilla BBQ', 'price' => 24, 'sort_order' => 9],
            ['name' => 'Alitas BBQ', 'size' => '12 piezas', 'price' => 30, 'sort_order' => 10],
            ['name' => 'Alitas BBQ', 'size' => '8 piezas', 'price' => 22, 'sort_order' => 11],
        ];

        foreach ($asados as $item) {
            MenuItem::create(array_merge(['menu_category_id' => $this->categories['asados']->id], $item));
        }
    }

    private function seedPicadas(): void
    {
        MenuItem::create([
            'menu_category_id' => $this->categories['picadas']->id,
            'name' => 'Personal',
            'price' => 28,
            'sort_order' => 1
        ]);

        MenuItem::create([
            'menu_category_id' => $this->categories['picadas']->id,
            'name' => 'Blue Lagoon',
            'description' => 'Pechuga-Cerdo-Chorizo-Butifarra-Patacón-Ensalada-Papa Francesa',
            'price' => 55,
            'sort_order' => 2
        ]);

        $adiciones = [
            ['name' => 'Papa Francesa', 'price' => 5, 'sort_order' => 3],
            ['name' => 'Patacón', 'price' => 5, 'sort_order' => 4],
            ['name' => 'Sopa', 'price' => 8, 'sort_order' => 5],
            ['name' => 'Arroz', 'price' => 4, 'sort_order' => 6],
            ['name' => 'Ensalada', 'price' => 3, 'sort_order' => 7],
        ];

        foreach ($adiciones as $item) {
            MenuItem::create(array_merge(['menu_category_id' => $this->categories['picadas']->id], $item));
        }
    }

    private function seedPescadosYSancocho(): void
    {
        MenuItem::create([
            'menu_category_id' => $this->categories['pescados']->id,
            'name' => 'Mojarra',
            'description' => 'Acompañado con arroz, patacón y ensalada',
            'price' => 28,
            'sort_order' => 1
        ]);

        MenuItem::create([
            'menu_category_id' => $this->categories['sancocho']->id,
            'name' => 'Sancocho del Día',
            'description' => 'Acompañado de Arroz-Papa francesa-Ensalada',
            'price' => 16,
            'sort_order' => 1
        ]);
    }

    private function seedJugosYBebidas(): void
    {
        // Jugos Naturales
        $jugos = [
            ['name' => 'Maracuyá-Corozo', 'price' => 6, 'sort_order' => 1],
            ['name' => 'Limonada', 'price' => 6, 'sort_order' => 2],
            ['name' => 'Frappe', 'description' => 'Maracuya - Corozo - Limón', 'price' => 7, 'sort_order' => 3],
        ];

        foreach ($jugos as $item) {
            MenuItem::create(array_merge(['menu_category_id' => $this->categories['jugos']->id], $item));
        }

        // Gaseosas
        $gaseosas = [
            'Coca Cola' => 3.5, 'Kola Roman' => 3.5, 'Sprite' => 3.5, 'Quatro' => 3.5,
            'Soda' => 3.5, 'Jugo Hit' => 4, 'Mini Pony' => 2, 'Postobon' => 3.5,
            'Agua Grande' => 2.5, 'Agua Pequeña' => 1
        ];

        $order = 1;
        foreach ($gaseosas as $name => $price) {
            MenuItem::create([
                'menu_category_id' => $this->categories['gaseosas']->id,
                'name' => $name,
                'price' => $price,
                'sort_order' => $order++
            ]);
        }

        // Bebidas Bar
        $bebidasBar = [
            'Ron Medellín pipona 750ml' => 74,
            'Panchina Medellín 375ml' => 34,
            'Aguardiente Antioqueño' => 65,
            'Pipona T. Verde 750ml' => 65,
            'Pachina T. Verde 375ml' => 33,
            'Pipona T. Azul 750ml' => 67,
            'Pachita T. Azul 375ml' => 34,
            'Águila negra' => 4,
            'Águila light' => 3.5,
            'Budweiser' => 3.5,
            'Coronita' => 5,
            MessageConstants::CLUB_COLOMBIA => 5,
            'Old parr 750ml' => 172,
            'Buchanans 750ml' => 220
        ];

        $order = 1;
        foreach ($bebidasBar as $name => $price) {
            MenuItem::create([
                'menu_category_id' => $this->categories['bebidas']->id,
                'name' => $name,
                'price' => $price,
                'sort_order' => $order++
            ]);
        }
    }

    private function seedMicheladas(): void
    {
        $micheladaTypes = [
            'Limón Natural' => 8, 'Maracuya' => 10, 'Uva' => 10, 'Corozo' => 10,
            'Fresa' => 10, 'Cereza' => 10, 'Mango' => 10, 'Frutos Rojos' => 11
        ];

        $beverageTypes = ['Ginger-Soda', MessageConstants::CLUB_COLOMBIA, 'Águila N. Budweiser'];
        $prices = [
            'Ginger-Soda' => [11, 13, 10],
            MessageConstants::CLUB_COLOMBIA => [13, 14, 12],
            'Águila N. Budweiser' => [12, 13, 12]
        ];

        foreach ($micheladaTypes as $flavor => $basePrice) {
            foreach ($beverageTypes as $index => $beverage) {
                MenuItem::create([
                    'menu_category_id' => $this->categories['micheladas']->id,
                    'name' => $flavor,
                    'description' => "Con $beverage",
                    'price' => $prices[$beverage][$index % 3],
                    'sort_order' => array_search($flavor, array_keys($micheladaTypes)) * 10 + $index
                ]);
            }
        }
    }
}

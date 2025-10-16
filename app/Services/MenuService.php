<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class MenuService extends BaseService
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Obtener menú digital completo con disponibilidad
     */
    public function getDigitalMenu(array $filters = []): array
    {
        $query = Product::with(['category', 'recipe.ingredients', 'unit'])
                       ->where('is_active', true);

        // Aplicar filtros
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['available_only']) && $filters['available_only']) {
            $query->where('is_available', true);
        }

        if (isset($filters['price_range'])) {
            if (isset($filters['price_range']['min'])) {
                $query->where('price', '>=', $filters['price_range']['min']);
            }
            if (isset($filters['price_range']['max'])) {
                $query->where('price', '<=', $filters['price_range']['max']);
            }
        }

        $products = $query->orderBy('category_id')
                         ->orderBy('name')
                         ->get();

        // Verificar disponibilidad de ingredientes
        $inventoryService = new InventoryService();
        $availabilityChecks = $inventoryService->checkRecipeAvailability(
            $products->pluck('id')->toArray()
        );

        // Agrupar por categoría
        $categories = $products->groupBy('category.name');

        return [
            'categories' => $categories->map(function ($categoryProducts, $categoryName) use ($availabilityChecks) {
                return [
                    'name' => $categoryName,
                    'products' => $categoryProducts->map(function ($product) use ($availabilityChecks) {
                        return $this->formatMenuProduct($product, $availabilityChecks[$product->id] ?? null);
                    })
                ];
            }),
            'statistics' => [
                'total_products' => $products->count(),
                'available_products' => $products->where('is_available', true)->count(),
                'categories_count' => $categories->count(),
                'price_range' => [
                    'min' => $products->min('price'),
                    'max' => $products->max('price'),
                    'average' => round($products->avg('price'), 2)
                ]
            ]
        ];
    }

    /**
     * Formatear producto para el menú
     */
    private function formatMenuProduct(Product $product, ?array $availability): array
    {
        $isAvailable = $product->is_available && ($availability['available'] ?? true);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image_url,
            'category' => $product->category->name ?? 'Sin categoría',
            'preparation_time' => $product->preparation_time ?? 15,
            'is_available' => $isAvailable,
            'availability_reason' => $this->getAvailabilityReason($product, $availability),
            'ingredients' => $this->getProductIngredients($product),
            'nutritional_info' => [
                'calories' => $product->calories,
                'allergens' => $product->allergens ? json_decode($product->allergens, true) : []
            ],
            'tags' => $this->getProductTags($product),
            'popularity' => $this->getProductPopularity(),
            'estimated_wait_time' => $this->calculateWaitTime($product)
        ];
    }

    /**
     * Obtener razón de disponibilidad
     */
    private function getAvailabilityReason(Product $product, ?array $availability): ?string
    {
        if (!$product->is_available) {
            return 'Producto desactivado temporalmente';
        }

        if ($availability && !$availability['available']) {
            $unavailable = $availability['unavailable_ingredients'] ?? [];
            if (!empty($unavailable)) {
                $ingredients = collect($unavailable)->pluck('name')->take(2)->join(', ');
                return "Ingredientes no disponibles: {$ingredients}";
            }
        }

        return null;
    }

    /**
     * Obtener ingredientes del producto
     */
    private function getProductIngredients(Product $product): array
    {
        if (!$product->recipe) {
            return [];
        }

        return $product->recipe->ingredients->map(function ($recipeIngredient) {
            return [
                'name' => $recipeIngredient->ingredient->name,
                'quantity' => $recipeIngredient->quantity,
                'unit' => $recipeIngredient->ingredient->unit->name ?? 'unidad'
            ];
        })->toArray();
    }

    /**
     * Obtener etiquetas del producto
     */
    private function getProductTags(Product $product): array
    {
        $tags = [];

        // Tags basados en características
        if ($product->is_spicy) {
            $tags[] = 'Picante';
        }

        if ($product->is_vegetarian) {
            $tags[] = 'Vegetariano';
        }

        if ($product->is_vegan) {
            $tags[] = 'Vegano';
        }

        // Tags basados en tiempo de preparación
        if ($product->preparation_time <= 10) {
            $tags[] = 'Rápido';
        } elseif ($product->preparation_time >= 30) {
            $tags[] = 'Elaborado';
        }

        // Tags basados en popularidad
        $popularity = $this->getProductPopularity();
        if ($popularity['rank'] <= 10) {
            $tags[] = 'Popular';
        }

        return $tags;
    }

    /**
     * Obtener popularidad del producto
     */
    private function getProductPopularity(): array
    {
        // Simular lógica de popularidad basada en pedidos
        // En una implementación real, esto consultaría las estadísticas de OrderItems
        return [
            'rank' => rand(1, 50),
            'orders_last_week' => rand(5, 50),
            'rating' => round(rand(35, 50) / 10, 1)
        ];
    }

    /**
     * Calcular tiempo de espera estimado
     */
    private function calculateWaitTime(Product $product): array
    {
        $baseTime = $product->preparation_time ?? 15;

        // Factores que pueden afectar el tiempo
        $queueFactor = 1.2; // Factor por cola de pedidos
        $complexityFactor = 1.0;

        if ($product->recipe && $product->recipe->ingredients->count() > 5) {
            $complexityFactor = 1.3;
        }

        $estimatedMinutes = ceil($baseTime * $queueFactor * $complexityFactor);

        return [
            'base_time' => $baseTime,
            'estimated_time' => $estimatedMinutes,
            'ready_at' => now()->addMinutes($estimatedMinutes)->format('H:i')
        ];
    }

    /**
     * Actualizar disponibilidad automática del menú
     */
    public function updateMenuAvailability(): array
    {
        return $this->executeTransaction(function () {
            $products = Product::with('recipe.ingredients')->where('is_active', true)->get();
            $inventoryService = new InventoryService();

            $availabilityChecks = $inventoryService->checkRecipeAvailability(
                $products->pluck('id')->toArray()
            );

            $updatedCount = 0;
            $unavailableProducts = [];

            foreach ($products as $product) {
                $availability = $availabilityChecks[$product->id] ?? ['available' => true];
                $shouldBeAvailable = $availability['available'];

                if ($product->is_available !== $shouldBeAvailable) {
                    $product->update(['is_available' => $shouldBeAvailable]);
                    $updatedCount++;

                    if (!$shouldBeAvailable) {
                        $unavailableProducts[] = [
                            'name' => $product->name,
                            'reason' => $availability['message'] ?? 'Ingredientes no disponibles'
                        ];
                    }
                }
            }

            $this->logActivity('menu_availability_updated', 'menu', [
                'updated_products' => $updatedCount,
                'unavailable_products' => $unavailableProducts
            ]);

            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'unavailable_products' => $unavailableProducts,
                'message' => "Disponibilidad actualizada para {$updatedCount} productos"
            ];
        });
    }

    /**
     * Obtener productos destacados
     */
    public function getFeaturedProducts(int $limit = 6): array
    {
        $products = Product::with(['category', 'recipe.ingredients'])
                          ->where('is_active', true)
                          ->where('is_available', true)
                          ->where('is_featured', true)
                          ->orderBy('featured_order')
                          ->take($limit)
                          ->get();

        if ($products->count() < $limit) {
            // Complementar con productos populares si no hay suficientes destacados
            $additionalProducts = Product::with(['category', 'recipe.ingredients'])
                                        ->where('is_active', true)
                                        ->where('is_available', true)
                                        ->where('is_featured', false)
                                        ->orderBy('created_at', 'desc')
                                        ->take($limit - $products->count())
                                        ->get();

            $products = $products->concat($additionalProducts);
        }

        $inventoryService = new InventoryService();
        $availabilityChecks = $inventoryService->checkRecipeAvailability(
            $products->pluck('id')->toArray()
        );

        return [
            'featured_products' => $products->map(function ($product) use ($availabilityChecks) {
                return $this->formatMenuProduct($product, $availabilityChecks[$product->id] ?? null);
            })->toArray()
        ];
    }

    /**
     * Buscar productos en el menú
     */
    public function searchMenuProducts(string $query, array $filters = []): array
    {
        $productQuery = Product::with(['category', 'recipe.ingredients'])
                              ->where('is_active', true);

        // Búsqueda por texto
        $productQuery->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%")
              ->orWhereHas('category', function ($catQuery) use ($query) {
                  $catQuery->where('name', 'LIKE', "%{$query}%");
              });
        });

        // Aplicar filtros adicionales
        if (isset($filters['category_id'])) {
            $productQuery->where('category_id', $filters['category_id']);
        }

        if (isset($filters['max_price'])) {
            $productQuery->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['max_prep_time'])) {
            $productQuery->where('preparation_time', '<=', $filters['max_prep_time']);
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            foreach ($filters['tags'] as $tag) {
                switch ($tag) {
                    case 'vegetarian':
                        $productQuery->where('is_vegetarian', true);
                        break;
                    case 'vegan':
                        $productQuery->where('is_vegan', true);
                        break;
                    case 'spicy':
                        $productQuery->where('is_spicy', true);
                        break;
                    default:
                        // Tag no reconocido, ignorar
                        break;
                }
            }
        }

        $products = $productQuery->orderBy('name')->get();

        $inventoryService = new InventoryService();
        $availabilityChecks = $inventoryService->checkRecipeAvailability(
            $products->pluck('id')->toArray()
        );

        return [
            'results' => $products->map(function ($product) use ($availabilityChecks) {
                return $this->formatMenuProduct($product, $availabilityChecks[$product->id] ?? null);
            })->toArray(),
            'total_results' => $products->count(),
            'query' => $query,
            'filters_applied' => $filters
        ];
    }

    /**
     * Obtener menú por categorías para API móvil
     */
    public function getMobileMenuData(): array
    {
        $categories = Category::with(['products' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_available', true)
                  ->orderBy('name');
        }])->where('is_active', true)->orderBy('display_order')->get();

        $inventoryService = new InventoryService();
        $allProducts = $categories->flatMap->products;

        $availabilityChecks = $inventoryService->checkRecipeAvailability(
            $allProducts->pluck('id')->toArray()
        );

        return [
            'categories' => $categories->map(function ($category) use ($availabilityChecks) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->image_url,
                    'products' => $category->products->map(function ($product) use ($availabilityChecks) {
                        $availability = $availabilityChecks[$product->id] ?? ['available' => true];
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'description' => $product->description,
                            'price' => $product->price,
                            'image' => $product->image_url,
                            'preparation_time' => $product->preparation_time ?? 15,
                            'is_available' => $product->is_available && $availability['available'],
                            'tags' => $this->getProductTags($product)
                        ];
                    })
                ];
            }),
            'last_updated' => now()->format(self::DATE_FORMAT)
        ];
    }

    /**
     * Generar reporte de popularidad del menú
     */
    public function getMenuPopularityReport(array $filters = []): array
    {
        // En una implementación real, esto consultaría OrderItems
        // Por ahora, simularemos datos

        $products = Product::with('category')->where('is_active', true)->get();

        return [
            'top_products' => $products->take(10)->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Sin categoría',
                    'orders_count' => rand(20, 100),
                    'revenue' => rand(500, 5000),
                    'avg_rating' => round(rand(35, 50) / 10, 1)
                ];
            })->sortByDesc('orders_count')->values(),
            'category_performance' => $products->groupBy('category.name')->map(function ($categoryProducts, $categoryName) {
                return [
                    'name' => $categoryName,
                    'products_count' => $categoryProducts->count(),
                    'total_orders' => $categoryProducts->sum(function () { return rand(10, 50); }),
                    'avg_price' => round($categoryProducts->avg('price'), 2)
                ];
            })->values()
        ];
    }
}

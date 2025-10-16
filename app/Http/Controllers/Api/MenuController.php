<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Obtener todas las categorías con sus productos
     * Resultado cacheado por 1 hora
     */
    public function getCategories(): JsonResponse
    {
        try {
            // Cachear resultados por 1 hora (3600 segundos)
            $categories = Cache::remember('menu_categories', 3600, function () {
                return Category::with(['products' => function ($query) {
                    $query->where('active', true);
                }])->get();
            });

            $categoriesData = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->nombre,
                    'description' => $category->descripcion,
                    'products_count' => $category->products->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $categoriesData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos de una categoría específica
     * Resultado cacheado por 1 hora por categoría
     */
    public function getProductsByCategory(int $categoryId): JsonResponse
    {
        try {
            // Cachear productos por categoría (1 hora)
            $cacheKey = "category_{$categoryId}_products";
            $products = Cache::remember($cacheKey, 3600, function () use ($categoryId) {
                return Product::where('category_id', $categoryId)
                    ->where('active', true)
                    ->get();
            });

            $category = Cache::remember("category_{$categoryId}", 3600, function () use ($categoryId) {
                return Category::findOrFail($categoryId);
            });

            $productsData = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => floatval($product->price),
                    'price_formatted' => '$' . number_format($product->price, 2),
                    'unit' => $product->unit,
                    'category_id' => $product->category_id,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->nombre,
                        'description' => $category->descripcion,
                    ],
                    'products' => $productsData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los productos (menú completo)
     * Resultado cacheado por 1 hora
     */
    public function getAllProducts(): JsonResponse
    {
        try {
            // Cachear todos los productos (1 hora)
            $products = Cache::remember('all_active_products', 3600, function () {
                return Product::with('category')
                    ->where('active', true)
                    ->get();
            });

            $productsData = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => floatval($product->price),
                    'price_formatted' => '$' . number_format($product->price, 2),
                    'unit' => $product->unit,
                    'category' => [
                        'id' => $product->category->id,
                        'name' => $product->category->nombre,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $productsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener mesas disponibles
     */
    public function getTables(): JsonResponse
    {
        try {
            $tables = Table::orderBy('name')->get();

            $tablesData = $tables->map(function ($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name,
                    'capacity' => $table->capacity,
                    'status' => $table->status,
                    'is_available' => $table->status === 'available',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $tablesData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mesas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar productos por nombre
     */
    public function searchProducts(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'La búsqueda debe tener al menos 2 caracteres'
                ], 400);
            }

            $products = Product::with('category')
                ->where('active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->limit(20)
                ->get();

            $productsData = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => floatval($product->price),
                    'price_formatted' => '$' . number_format($product->price, 2),
                    'unit' => $product->unit,
                    'category' => [
                        'id' => $product->category->id,
                        'name' => $product->category->nombre,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $productsData,
                'meta' => [
                    'query' => $query,
                    'results_count' => $products->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
}

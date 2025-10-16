<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseApiController
{
    protected $productService;
    protected $menuService;

    public function __construct(ProductService $productService, MenuService $menuService)
    {
        $this->productService = $productService;
        $this->menuService = $menuService;
    }

    /**
     * Obtener todos los productos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'category_id', 'available', 'active']);
            $products = $this->productService->getFiltered($filters);

            return $this->successResponse($products, 'Productos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener productos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener producto específico
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);

            if (!$product) {
                return $this->notFoundResponse('Producto no encontrado');
            }

            return $this->successResponse($product, 'Producto obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener producto: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos por categoría
     */
    public function byCategory(int $categoryId): JsonResponse
    {
        try {
            $products = $this->productService->getByCategory($categoryId);
            return $this->successResponse($products, 'Productos por categoría obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener productos por categoría: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos disponibles
     */
    public function available(): JsonResponse
    {
        try {
            $products = $this->productService->getAvailableProducts();
            return $this->successResponse($products, 'Productos disponibles obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener productos disponibles: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function lowStock(): JsonResponse
    {
        try {
            $products = $this->productService->getLowStockProducts();
            return $this->successResponse($products, 'Productos con stock bajo obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener productos con stock bajo: ' . $e->getMessage());
        }
    }

    /**
     * Crear nuevo producto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'unit_id' => 'nullable|exists:units,id',
                'stock' => 'nullable|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'available' => 'nullable|boolean',
                'active' => 'nullable|boolean',
                'image' => 'nullable|image|max:2048'
            ]);

            $product = $this->productService->create($validated);
            return $this->successResponse($product, 'Producto creado exitosamente', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al crear producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);

            if (!$product) {
                return $this->notFoundResponse('Producto no encontrado');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'unit_id' => 'nullable|exists:units,id',
                'stock' => 'nullable|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'available' => 'nullable|boolean',
                'active' => 'nullable|boolean',
                'image' => 'nullable|image|max:2048'
            ]);

            $updatedProduct = $this->productService->update($product, $validated);
            return $this->successResponse($updatedProduct, 'Producto actualizado exitosamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al actualizar producto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar producto
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);

            if (!$product) {
                return $this->notFoundResponse('Producto no encontrado');
            }

            $this->productService->delete($product);
            return $this->successResponse(null, 'Producto eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al eliminar producto: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar disponibilidad del producto
     */
    public function toggleAvailability(int $id): JsonResponse
    {
        try {
            $product = $this->productService->find($id);

            if (!$product) {
                return $this->notFoundResponse('Producto no encontrado');
            }

            $updatedProduct = $this->productService->toggleAvailability($product);
            return $this->successResponse($updatedProduct, 'Disponibilidad del producto actualizada');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al cambiar disponibilidad: ' . $e->getMessage());
        }
    }

    /**
     * Obtener menú digital completo
     */
    public function menu(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['category_id', 'available_only', 'price_range']);
            $menu = $this->menuService->getDigitalMenu($filters);

            return $this->successResponse($menu, 'Menú digital obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener menú: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos destacados
     */
    public function featured(): JsonResponse
    {
        try {
            $featured = $this->menuService->getFeaturedProducts();
            return $this->successResponse($featured, 'Productos destacados obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener productos destacados: ' . $e->getMessage());
        }
    }

    /**
     * Buscar productos
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->input('q', '');
            $filters = $request->only(['category_id', 'max_price', 'max_prep_time', 'tags']);

            $results = $this->menuService->searchMenuProducts($query, $filters);
            return $this->successResponse($results, 'Búsqueda completada exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error en la búsqueda: ' . $e->getMessage());
        }
    }

    /**
     * Obtener datos del menú para móvil
     */
    public function mobileMenu(): JsonResponse
    {
        try {
            $menuData = $this->menuService->getMobileMenuData();
            return $this->successResponse($menuData, 'Menú móvil obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener menú móvil: ' . $e->getMessage());
        }
    }
}
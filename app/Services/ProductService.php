<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Exceptions\ProductException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;

class ProductService extends BaseService
{
    /**
     * Obtener todos los productos con sus relaciones
     */
    public function getAll()
    {
        return Product::with(['category', 'unit'])->orderBy('name')->get();
    }

    /**
     * Buscar producto por ID
     */
    public function find(int $id)
    {
        return Product::with(['category', 'unit', 'recipes.ingredients'])->find($id);
    }

    /**
     * Obtener productos con filtros
     */
    public function getFiltered($filters = [])
    {
        $query = Product::with(['category', 'unit']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['available'])) {
            $query->where('available', $filters['available']);
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('name')->get();
    }

        /**
     * Crear un nuevo producto
     */
    public function create(array $data)
    {
        // Crear el producto sin imagen
        $productData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'unit_id' => $data['unit_id'] ?? null,
            'stock' => $data['stock'] ?? 0,
            'stock_minimo' => $data['stock_minimo'] ?? 0,
            'available' => $data['available'] ?? true,
            'active' => $data['active'] ?? true,
        ];

        $product = Product::create($productData);

        // Manejar la imagen usando MediaLibrary
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $product->addMediaFromRequest('image')
                ->toMediaCollection('products');
        }

        return $product;
    }

    /**
     * Actualizar un producto existente
     */
    public function update(Product $product, array $data)
    {
        // Actualizar los datos del producto
        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $data['category_id'],
            'unit_id' => $data['unit_id'] ?? null,
            'stock' => $data['stock'] ?? $product->stock,
            'stock_minimo' => $data['stock_minimo'] ?? $product->stock_minimo,
            'available' => $data['available'] ?? $product->available,
            'active' => $data['active'] ?? $product->active,
        ];

        $product->update($updateData);

        // Manejar la imagen usando MediaLibrary
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Eliminar imagen anterior
            $product->clearMediaCollection('products');

            // Agregar nueva imagen
            $product->addMediaFromRequest('image')
                ->toMediaCollection('products');
        }

        return $product->fresh();
    }

    /**
     * Eliminar un producto
     */
    public function delete(Product $product)
    {
        // Verificar que no tenga órdenes activas
        $hasActiveOrders = $product->orderItems()
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['pending', 'in_process']);
            })
            ->exists();

        if ($hasActiveOrders) {
            throw ProductException::cannotDelete();
        }

        // Eliminar todas las imágenes asociadas usando MediaLibrary
        $product->clearMediaCollection('products');

        return $product->delete();
    }

    /**
     * Cambiar disponibilidad de un producto
     */
    public function toggleAvailability(Product $product)
    {
        $product->update(['available' => !$product->available]);
        return $product->fresh();
    }

    /**
     * Cambiar estado activo de un producto
     */
    public function toggleActive(Product $product)
    {
        $product->update(['active' => !$product->active]);
        return $product->fresh();
    }

    /**
     * Obtener productos por categoría
     */
    public function getByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)
            ->where('available', true)
            ->where('active', true)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts()
    {
        return Product::whereColumn('stock', '<=', 'stock_minimo')
            ->where('active', true)
            ->with(['category', 'unit'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener categorías para formularios
     */
    public function getCategories()
    {
        return Category::orderBy('nombre')->get();
    }

    /**
     * Obtener unidades para formularios
     */
    public function getUnits()
    {
        return Unit::orderBy('nombre')->get();
    }

    /**
     * Obtener productos disponibles para pedidos
     */
    public function getAvailableProducts()
    {
        return Product::with(['category'])
                     ->where('available', true)
                     ->where('stock', '>', 0)
                     ->orderBy('name')
                     ->get();
    }
}

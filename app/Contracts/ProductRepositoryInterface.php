<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    /**
     * Obtener todos los productos con filtros
     */
    public function getAllWithFilters(Request $request);

    /**
     * Obtener producto por ID
     */
    public function findById(int $id);

    /**
     * Crear un nuevo producto
     */
    public function create(array $data): Product;

    /**
     * Actualizar un producto
     */
    public function update(Product $product, array $data): Product;

    /**
     * Eliminar un producto
     */
    public function delete(Product $product): bool;

    /**
     * Obtener productos por categoría
     */
    public function getByCategory(int $categoryId);

    /**
     * Obtener categorías de productos
     */
    public function getCategories();

    /**
     * Cambiar disponibilidad de producto
     */
    public function toggleAvailability(Product $product): Product;
}

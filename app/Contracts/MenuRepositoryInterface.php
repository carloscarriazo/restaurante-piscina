<?php

namespace App\Contracts;

use App\Models\Category;
use App\Models\Product;

interface MenuRepositoryInterface
{
    /**
     * Obtener todas las categorías del menú con items
     */
    public function getAllCategoriesWithItems();

    /**
     * Obtener categoría por ID con items
     */
    public function getCategoryWithItems(int $categoryId);

    /**
     * Obtener items disponibles de una categoría
     */
    public function getAvailableItemsByCategory(int $categoryId);

    /**
     * Crear nueva categoría de menú
     */
    public function createCategory(array $data): Category;

    /**
     * Crear nuevo item de menú
     */
    public function createMenuItem(array $data): Product;

    /**
     * Actualizar categoría
     */
    public function updateCategory(Category $category, array $data): Category;

    /**
     * Actualizar item de menú
     */
    public function updateMenuItem(Product $item, array $data): Product;
}

<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Obtener todas las categorías con el conteo de productos
     */
    public function getAll()
    {
        return Category::withCount('products')->orderBy('nombre')->get();
    }

    /**
     * Obtener categorías activas para formularios
     */
    public function getActive()
    {
        return Category::where('is_active', true)->orderBy('nombre')->get();
    }

    /**
     * Crear una nueva categoría
     */
    public function create(array $data)
    {
        $categoryData = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];

        return Category::create($categoryData);
    }

    /**
     * Actualizar una categoría existente
     */
    public function update(Category $category, array $data)
    {
        $updateData = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'is_active' => $data['is_active'] ?? $category->is_active,
        ];

        $category->update($updateData);
        return $category->fresh();
    }

    /**
     * Eliminar una categoría
     */
    public function deleteCategory($id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            throw \App\Exceptions\CategoryException::notFound($id);
        }

        if ($category->products()->count() > 0) {
            throw \App\Exceptions\CategoryException::hasProducts($category->name);
        }

        return $category->delete();
    }

    /**
     * Cambiar estado activo de una categoría
     */
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }

    /**
     * Obtener categorías con productos
     */
    public function getWithProducts()
    {
        return Category::with(['products' => function($query) {
            $query->where('active', true)->where('available', true);
        }])->where('is_active', true)->orderBy('nombre')->get();
    }
}

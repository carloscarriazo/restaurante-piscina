<?php

namespace App\Repositories;

use App\Contracts\MenuRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use App\Models\MenuCategory;
use App\Models\MenuItem;

class MenuRepository implements MenuRepositoryInterface
{
    public function getAllCategoriesWithItems()
    {
        // Usar MenuCategory y MenuItem en lugar de Category y Product
        return MenuCategory::with(['menuItems' => function($query) {
                $query->where('is_available', true)
                      ->orderBy('sort_order')
                      ->orderBy('name');
            }])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'nombre' => $category->display_name,
                    'descripcion' => $category->description ?? '',
                    'color' => $category->color ?? '#4ECDC4',
                    'items' => $category->menuItems->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'description' => $item->description ?? '',
                            'price' => $item->effective_price,
                            'size' => $item->size,
                            'ingredients' => $item->ingredients,
                            'category_id' => $item->menu_category_id,
                            'is_available' => $item->is_available,
                            'is_featured' => $item->is_featured,
                            'operating_days' => $item->operating_days ?? [5, 6, 0],
                            'valid_from' => $item->valid_from,
                            'valid_until' => $item->valid_until,
                            'image_url' => $item->image_url,
                            'available' => $item->is_available, // Alias para compatibilidad
                        ];
                    })
                ];
            });
    }

    public function getCategoryWithItems(int $categoryId)
    {
        $category = MenuCategory::with(['menuItems' => function($query) {
                $query->where('is_available', true)
                      ->orderBy('sort_order')
                      ->orderBy('name');
            }])
            ->findOrFail($categoryId);

        return [
            'id' => $category->id,
            'nombre' => $category->display_name,
            'descripcion' => $category->description ?? '',
            'color' => $category->color ?? '#4ECDC4',
            'items' => $category->menuItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description ?? '',
                    'price' => $item->effective_price,
                    'size' => $item->size,
                    'ingredients' => $item->ingredients,
                    'category_id' => $item->menu_category_id,
                    'is_available' => $item->is_available,
                    'is_featured' => $item->is_featured,
                    'operating_days' => $item->operating_days ?? [5, 6, 0],
                    'valid_from' => $item->valid_from,
                    'valid_until' => $item->valid_until,
                    'image_url' => $item->image_url,
                    'available' => $item->is_available, // Alias para compatibilidad
                ];
            })
        ];
    }

    public function getAvailableItemsByCategory(int $categoryId)
    {
        return MenuItem::where('menu_category_id', $categoryId)
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function createCategory(array $data): Category
    {
        return MenuCategory::create($data);
    }

    public function createMenuItem(array $data): Product
    {
        return MenuItem::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    public function updateMenuItem(Product $item, array $data): Product
    {
        $item->update($data);
        return $item->fresh();
    }
}

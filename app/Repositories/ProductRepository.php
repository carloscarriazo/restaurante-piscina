<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllWithFilters(Request $request)
    {
        $query = Product::with(['category', 'type']);

        // Aplicar filtros
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('available')) {
            $query->where('is_available', $request->available);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return $query->latest()->paginate(20);
    }

    public function findById(int $id)
    {
        return Product::with(['category', 'type', 'ingredients'])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getByCategory(int $categoryId)
    {
        return Product::with(['category', 'type'])
            ->where('category_id', $categoryId)
            ->where('is_available', true)
            ->get();
    }

    public function getCategories()
    {
        return Category::where('active', true)->get();
    }

    public function toggleAvailability(Product $product): Product
    {
        $product->update(['is_available' => !$product->is_available]);
        return $product->fresh();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $products = $category->products()
            ->orderBy('name')
            ->paginate(10);

        return view('categories.show', compact('category', 'products'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Verificar que la categoría no tenga productos
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }

    /**
     * Toggle the active status of a category.
     */
    public function toggleActive(Category $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activada' : 'desactivada';

        return redirect()->route('categories.index')
            ->with('success', "Categoría {$status} exitosamente.");
    }
}

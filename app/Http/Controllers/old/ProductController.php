<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit']);

        // Filtrar por categoría si se especifica
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrar por estado si se especifica
        if ($request->has('available') && $request->available !== '') {
            $query->where('available', $request->available);
        }

        // Buscar por nombre si se especifica
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('nombre')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('nombre')->get();
        $units = Unit::orderBy('nombre')->get();

        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999.99',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'available' => 'boolean',
        ]);

        $validated['available'] = $request->has('available');

        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_url'] = $imagePath;
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'unit']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('nombre')->get();
        $units = Unit::orderBy('nombre')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:99999.99',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'available' => 'boolean',
        ]);

        $validated['available'] = $request->has('available');

        // Manejar subida de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image_url) {
                Storage::disk('public')->delete($product->image_url);
            }

            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_url'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Verificar que el producto no esté en órdenes pendientes
        $hasActiveOrders = $product->orderItems()
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['pending', 'in_process']);
            })
            ->exists();

        if ($hasActiveOrders) {
            return redirect()->route('products.index')
                ->with('error', 'No se puede eliminar el producto porque tiene órdenes activas.');
        }

        // Eliminar imagen si existe
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    /**
     * Toggle the availability status of a product.
     */
    public function toggleAvailability(Product $product): RedirectResponse
    {
        $product->update(['available' => !$product->available]);

        $status = $product->available ? 'disponible' : 'no disponible';

        return redirect()->route('products.index')
            ->with('success', "Producto marcado como {$status} exitosamente.");
    }

    /**
     * Get products by category (for AJAX requests).
     */
    public function getByCategory(Category $category)
    {
        $products = $category->products()
            ->where('available', true)
            ->select('id', 'name', 'price')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }
}

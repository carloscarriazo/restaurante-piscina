<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    // Obtener todos los productos
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'type']);

            // Filtros opcionales
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('type_id')) {
                $query->where('type_id', $request->type_id);
            }

            if ($request->has('is_available')) {
                $query->where('is_available', $request->boolean('is_available'));
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $products = $query->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener producto específico
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'type', 'ingredients'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Crear nuevo producto
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'type_id' => 'nullable|exists:product_types,id',
                'is_available' => 'boolean',
                'image_url' => 'nullable|url'
            ]);

            $product = Product::create($request->all());
            $product->load(['category', 'type']);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar producto
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'category_id' => 'sometimes|exists:categories,id',
                'type_id' => 'nullable|exists:product_types,id',
                'is_available' => 'boolean',
                'image_url' => 'nullable|url'
            ]);

            $product->update($request->all());
            $product->load(['category', 'type']);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cambiar disponibilidad del producto
    public function toggleAvailability($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update(['is_available' => !$product->is_available]);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad actualizada',
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar producto
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener todas las categorías
    public function categories()
    {
        try {
            $categories = Category::with('products')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener productos por categoría
    public function productsByCategory($categoryId)
    {
        try {
            $category = Category::with(['products' => function($query) {
                $query->where('is_available', true)->orderBy('name');
            }])->findOrFail($categoryId);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category->name,
                    'products' => $category->products
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

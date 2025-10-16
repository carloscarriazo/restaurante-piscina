<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Constants\MessageConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Categorías",
 *     description="Gestión de categorías de productos"
 * )
 */
class CategoryController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Listar todas las categorías",
     *     tags={"Categorías"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="products_count", type="integer")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Category::query()->withCount('products');

        // Filtro por nombre
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $categories = $request->has('per_page')
            ? $query->paginate($request->per_page)
            : $query->get();

        return $this->successResponse($categories, 'Categorías obtenidas exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Crear nueva categoría",
     *     tags={"Categorías"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Bebidas"),
     *             @OA\Property(property="description", type="string", example="Bebidas frías y calientes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada exitosamente"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500'
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.unique' => 'Ya existe una categoría con este nombre'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $category = Category::create($validator->validated());

        return $this->successResponse($category, 'Categoría creada exitosamente', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Obtener detalle de una categoría",
     *     tags={"Categorías"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle de la categoría"),
     *     @OA\Response(response=404, description="Categoría no encontrada")
     * )
     */
    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return $this->errorResponse(MessageConstants::CATEGORY_NOT_FOUND, 404);
        }

        return $this->successResponse($category);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Actualizar categoría",
     *     tags={"Categorías"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Categoría actualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->errorResponse(MessageConstants::CATEGORY_NOT_FOUND, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $category->update($validator->validated());

        return $this->successResponse($category, 'Categoría actualizada exitosamente');
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Eliminar categoría",
     *     tags={"Categorías"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Categoría eliminada"),
     *     @OA\Response(response=400, description="No se puede eliminar, tiene productos asociados")
     * )
     */
    public function destroy($id)
    {
        $category = Category::withCount('products')->find($id);

        if (!$category) {
            return $this->errorResponse(MessageConstants::CATEGORY_NOT_FOUND, 404);
        }

        if ($category->products_count > 0) {
            return $this->errorResponse(
                'No se puede eliminar la categoría porque tiene productos asociados',
                400
            );
        }

        $category->delete();

        return $this->successResponse(null, 'Categoría eliminada exitosamente');
    }
}

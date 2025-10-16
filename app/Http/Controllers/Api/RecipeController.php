<?php

namespace App\Http\Controllers\Api;

use App\Models\Recipe;
use App\Models\Product;
use App\Models\Ingredient;
use App\Constants\MessageConstants;
use App\Constants\ValidationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Recetas",
 *     description="Gestión de recetas (productos con sus ingredientes)"
 * )
 */
class RecipeController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/recipes",
     *     summary="Listar todas las recetas",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de recetas con ingredientes")
     * )
     */
    public function index(Request $request)
    {
        $query = Recipe::with(['product', 'ingredients']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $recipes = $request->has('per_page')
            ? $query->paginate($request->per_page)
            : $query->get();

        return $this->successResponse($recipes, 'Recetas obtenidas exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/recipes",
     *     summary="Crear nueva receta",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "ingredients"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Pizza Margherita - Receta Base"),
     *             @OA\Property(
     *                 property="ingredients",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="ingredient_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="number", example=0.2, description="Cantidad en la unidad del ingrediente")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Receta creada")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'name' => 'nullable|string|max:255',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.001'
        ], [
            'product_id.required' => 'El producto es obligatorio',
            'product_id.exists' => 'El producto no existe',
            'ingredients.required' => 'Debe incluir al menos un ingrediente',
            'ingredients.*.ingredient_id.required' => 'Cada ingrediente debe tener un ID válido',
            'ingredients.*.quantity.required' => 'Cada ingrediente debe tener una cantidad'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            DB::beginTransaction();

            $product = Product::find($request->product_id);

            // Crear la receta
            $recipe = Recipe::create([
                'product_id' => $request->product_id,
                'name' => $request->name ?? "Receta para {$product->name}"
            ]);

            // Asociar ingredientes con cantidades
            foreach ($request->ingredients as $ingredientData) {
                $recipe->ingredients()->attach($ingredientData['ingredient_id'], [
                    'quantity' => $ingredientData['quantity']
                ]);
            }

            DB::commit();

            $recipe->load(['product', 'ingredients']);

            return $this->successResponse($recipe, 'Receta creada exitosamente', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear la receta: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/recipes/{id}",
     *     summary="Obtener detalle de una receta",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle de la receta con ingredientes")
     * )
     */
    public function show($id)
    {
        $recipe = Recipe::with(['product', 'ingredients'])->find($id);

        if (!$recipe) {
            return $this->errorResponse(MessageConstants::RECIPE_NOT_FOUND, 404);
        }

        // Calcular costo estimado
        $totalCost = 0;
        foreach ($recipe->ingredients as $ingredient) {
            $totalCost += $ingredient->pivot->quantity * $ingredient->cost_per_unit;
        }

        $recipe->estimated_cost = round($totalCost, 2);

        return $this->successResponse($recipe);
    }

    /**
     * @OA\Put(
     *     path="/api/recipes/{id}",
     *     summary="Actualizar receta",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Receta actualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $recipe = $this->findRecipeOrFail($id);

            $validator = $this->validateRecipeUpdate($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            DB::beginTransaction();

            if ($request->has('name')) {
                $recipe->update(['name' => $request->name]);
            }

            // Actualizar ingredientes si se proporcionan
            if ($request->has('ingredients')) {
                // Eliminar ingredientes actuales
                $recipe->ingredients()->detach();

                // Agregar nuevos ingredientes
                foreach ($request->ingredients as $ingredientData) {
                    $recipe->ingredients()->attach($ingredientData['ingredient_id'], [
                        'quantity' => $ingredientData['quantity']
                    ]);
                }
            }

            DB::commit();

            $recipe->load(['product', 'ingredients']);

            return $this->successResponse($recipe, 'Receta actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar la receta: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/recipes/product/{productId}",
     *     summary="Obtener recetas de un producto específico",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Recetas del producto")
     * )
     */
    public function byProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return $this->errorResponse('Producto no encontrado', 404);
        }

        $recipes = Recipe::with('ingredients')
            ->where('product_id', $productId)
            ->get();

        return $this->successResponse([
            'product' => $product,
            'recipes' => $recipes
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/recipes/{id}",
     *     summary="Eliminar receta",
     *     tags={"Recetas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Receta eliminada")
     * )
     */
    public function destroy($id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return $this->errorResponse(MessageConstants::RECIPE_NOT_FOUND, 404);
        }

        $recipe->ingredients()->detach();
        $recipe->delete();

        return $this->successResponse(null, 'Receta eliminada exitosamente');
    }

    /**
     * Métodos privados para SRP
     */
    private function findRecipeOrFail($id): Recipe
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(MessageConstants::RECIPE_NOT_FOUND);
        }

        return $recipe;
    }

    private function validateRecipeUpdate(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'ingredients' => 'nullable|array|min:1',
            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.001'
        ]);
    }
}

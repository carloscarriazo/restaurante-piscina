<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Constants\MessageConstants;
use App\Constants\ValidationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Ingredientes",
 *     description="Gestión de ingredientes e inventario"
 * )
 */
class IngredientController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/ingredients",
     *     summary="Listar todos los ingredientes",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="low_stock",
     *         in="query",
     *         description="Filtrar solo ingredientes con stock bajo",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ingredientes con stock actual"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Ingredient::query();

        // Filtrar solo ingredientes con stock bajo
        if ($request->boolean('low_stock')) {
            $query->whereRaw('current_stock <= minimum_stock');
        }

        // Filtrar por búsqueda
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtrar por estado activo
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $ingredients = $request->has('per_page')
            ? $query->paginate($request->per_page)
            : $query->get();

        // Agregar flag de stock bajo a cada ingrediente
        if (!$request->has('per_page')) {
            $ingredients->transform(function ($ingredient) {
                $ingredient->is_low_stock = $ingredient->isLowStock();
                return $ingredient;
            });
        }

        return $this->successResponse($ingredients, 'Ingredientes obtenidos exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/ingredients",
     *     summary="Crear nuevo ingrediente",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "unit"},
     *             @OA\Property(property="name", type="string", example="Tomate"),
     *             @OA\Property(property="unit", type="string", example="kg"),
     *             @OA\Property(property="current_stock", type="number", example=50.5),
     *             @OA\Property(property="minimum_stock", type="number", example=10),
     *             @OA\Property(property="cost_per_unit", type="number", example=2.50),
     *             @OA\Property(property="supplier", type="string", example="Proveedor XYZ")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Ingrediente creado")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ingredients,name',
            'unit' => 'required|string|max:50',
            'current_stock' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'minimum_stock' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'cost_per_unit' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ], [
            'name.required' => 'El nombre del ingrediente es obligatorio',
            'name.unique' => 'Ya existe un ingrediente con este nombre',
            'unit.required' => 'La unidad de medida es obligatoria'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $ingredient = Ingredient::create($validator->validated());

        return $this->successResponse($ingredient, 'Ingrediente creado exitosamente', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients/{id}",
     *     summary="Obtener detalle de un ingrediente",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle del ingrediente con historial de movimientos")
     * )
     */
    public function show($id)
    {
        $ingredient = Ingredient::with(['stockMovements' => function ($query) {
            $query->latest()->limit(20);
        }])->find($id);

        if (!$ingredient) {
            return $this->errorResponse(MessageConstants::INGREDIENT_NOT_FOUND, 404);
        }

        $ingredient->is_low_stock = $ingredient->isLowStock();

        return $this->successResponse($ingredient);
    }

    /**
     * @OA\Put(
     *     path="/api/ingredients/{id}",
     *     summary="Actualizar ingrediente",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ingrediente actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return $this->errorResponse(MessageConstants::INGREDIENT_NOT_FOUND, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
            'unit' => 'required|string|max:50',
            'current_stock' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'minimum_stock' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'cost_per_unit' => ValidationRules::NULLABLE_NUMERIC_MIN_0,
            'supplier' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $ingredient->update($validator->validated());

        return $this->successResponse($ingredient, 'Ingrediente actualizado exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/ingredients/{id}/adjust-stock",
     *     summary="Ajustar stock de ingrediente (aumentar o disminuir)",
     *     tags={"Ingredientes"},
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
     *             required={"type", "quantity", "reason"},
     *             @OA\Property(property="type", type="string", enum={"increase", "decrease"}, example="increase"),
     *             @OA\Property(property="quantity", type="number", example=25.5),
     *             @OA\Property(property="reason", type="string", example="Compra de proveedor")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Stock ajustado correctamente")
     * )
     */
    public function adjustStock(Request $request, $id)
    {
        try {
            $ingredient = $this->findIngredientOrFail($id);

            $validator = $this->validateStockAdjustment($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $userId = optional($request->user())->id;

            $request->type === 'increase'
                ? $ingredient->increaseStock($request->quantity, $request->reason, $userId)
                : $ingredient->decreaseStock($request->quantity, $request->reason, $userId);

            $ingredient->refresh();

            return $this->successResponse([
                'ingredient' => $ingredient,
                'new_stock' => $ingredient->current_stock,
                'is_low_stock' => $ingredient->isLowStock()
            ], 'Stock ajustado exitosamente');

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients/{id}/movements",
     *     summary="Obtener historial de movimientos de un ingrediente",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Historial de movimientos")
     * )
     */
    public function movements($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return $this->errorResponse(MessageConstants::INGREDIENT_NOT_FOUND, 404);
        }

        $movements = $ingredient->stockMovements()
            ->with('user:id,name')
            ->latest()
            ->paginate(50);

        return $this->successResponse($movements, 'Movimientos obtenidos exitosamente');
    }

    /**
     * @OA\Delete(
     *     path="/api/ingredients/{id}",
     *     summary="Eliminar ingrediente",
     *     tags={"Ingredientes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Ingrediente eliminado")
     * )
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return $this->errorResponse(MessageConstants::INGREDIENT_NOT_FOUND, 404);
        }

        // Verificar si está en uso en alguna receta
        if ($ingredient->recipes()->count() > 0) {
            return $this->errorResponse(
                'No se puede eliminar el ingrediente porque está en uso en recetas',
                400
            );
        }

        $ingredient->delete();

        return $this->successResponse(null, 'Ingrediente eliminado exitosamente');
    }
    /**
     * Métodos privados para SRP
     */
    private function findIngredientOrFail($id): Ingredient
    {
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(MessageConstants::INGREDIENT_NOT_FOUND);
        }

        return $ingredient;
    }

    private function validateStockAdjustment(Request $request)
    {
        return Validator::make($request->all(), [
            'type' => 'required|in:increase,decrease',
            'quantity' => 'required|numeric|min:0.001',
            'reason' => 'required|string|max:255'
        ], [
            'type.required' => 'El tipo de ajuste es obligatorio',
            'type.in' => 'El tipo debe ser "increase" o "decrease"',
            'quantity.required' => 'La cantidad es obligatoria',
            'quantity.min' => 'La cantidad debe ser mayor a 0',
            'reason.required' => 'La razón del ajuste es obligatoria'
        ]);
    }
}

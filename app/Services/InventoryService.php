<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class InventoryService extends BaseService
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    /**
     * Obtener inventario completo con estadísticas
     */
    public function getInventoryOverview(array $filters = []): array
    {
        $query = Ingredient::with(['unit', 'stockMovements' => function($q) {
            $q->orderBy('created_at', 'desc')->take(10);
        }]);

        // Aplicar filtros
        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['low_stock']) && $filters['low_stock']) {
            $query->whereRaw('current_quantity <= min_stock_level');
        }

        if (isset($filters['out_of_stock']) && $filters['out_of_stock']) {
            $query->where('current_quantity', 0);
        }

        $ingredients = $query->orderBy('name')->get();

        return [
            'ingredients' => $ingredients->map(function ($ingredient) {
                return $this->formatIngredientData($ingredient);
            }),
            'statistics' => [
                'total_ingredients' => $ingredients->count(),
                'low_stock_count' => $ingredients->filter(function ($ingredient) {
                    return $ingredient->current_quantity <= $ingredient->min_stock_level;
                })->count(),
                'out_of_stock_count' => $ingredients->where('current_quantity', 0)->count(),
                'categories' => $ingredients->groupBy('category')->map->count(),
                'total_value' => $ingredients->sum(function ($ingredient) {
                    return $ingredient->current_quantity * $ingredient->cost_per_unit;
                })
            ]
        ];
    }

    /**
     * Formatear datos de ingrediente
     */
    private function formatIngredientData(Ingredient $ingredient): array
    {
        $stockLevel = $this->calculateStockLevel($ingredient);

        return [
            'id' => $ingredient->id,
            'name' => $ingredient->name,
            'category' => $ingredient->category,
            'current_quantity' => $ingredient->current_quantity,
            'unit' => $ingredient->unit->name ?? 'Unidad',
            'min_stock_level' => $ingredient->min_stock_level,
            'cost_per_unit' => $ingredient->cost_per_unit,
            'total_value' => $ingredient->current_quantity * $ingredient->cost_per_unit,
            'stock_level' => $stockLevel,
            'last_updated' => $ingredient->updated_at->format(self::DATE_FORMAT),
            'recent_movements' => $ingredient->stockMovements->take(5)->map(function ($movement) {
                return [
                    'type' => $movement->type,
                    'quantity' => $movement->quantity,
                    'reason' => $movement->reason,
                    'created_at' => $movement->created_at->format(self::DATE_FORMAT)
                ];
            }),
            'usage_prediction' => $this->predictIngredientUsage($ingredient)
        ];
    }

    /**
     * Calcular nivel de stock
     */
    private function calculateStockLevel(Ingredient $ingredient): array
    {
        $current = $ingredient->current_quantity;
        $minimum = $ingredient->min_stock_level;
        $percentage = $minimum > 0 ? ($current / $minimum) * 100 : 100;

        if ($current <= 0) {
            $status = 'out_of_stock';
            $color = 'danger';
        } elseif ($current <= $minimum) {
            $status = 'low_stock';
            $color = 'warning';
        } elseif ($percentage <= 150) {
            $status = 'normal';
            $color = 'success';
        } else {
            $status = 'high_stock';
            $color = 'info';
        }

        return [
            'status' => $status,
            'color' => $color,
            'percentage' => round($percentage, 2)
        ];
    }

    /**
     * Predecir uso de ingrediente
     */
    private function predictIngredientUsage(Ingredient $ingredient): array
    {
        // Obtener movimientos de los últimos 30 días
        $movements = StockMovement::where('ingredient_id', $ingredient->id)
                                ->where('type', 'consumed')
                                ->where('created_at', '>=', now()->subDays(30))
                                ->get();

        if ($movements->isEmpty()) {
            return [
                'daily_average' => 0,
                'weekly_average' => 0,
                'days_until_empty' => 'N/A'
            ];
        }

        $totalConsumed = $movements->sum('quantity');
        $daysWithMovements = $movements->groupBy(function ($movement) {
            return $movement->created_at->format('Y-m-d');
        })->count();

        $dailyAverage = $daysWithMovements > 0 ? $totalConsumed / $daysWithMovements : 0;
        $weeklyAverage = $dailyAverage * 7;

        $daysUntilEmpty = $dailyAverage > 0 ?
                         ceil($ingredient->current_quantity / $dailyAverage) :
                         'Indefinido';

        return [
            'daily_average' => round($dailyAverage, 2),
            'weekly_average' => round($weeklyAverage, 2),
            'days_until_empty' => $daysUntilEmpty
        ];
    }

    /**
     * Añadir stock de ingrediente
     */
    public function addStock(int $ingredientId, float $quantity, string $reason = 'manual_addition'): array
    {
        $this->validateRequiredFields([
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity
        ], ['ingredient_id', 'quantity']);

        if ($quantity <= 0) {
            return [
                'success' => false,
                'message' => 'La cantidad debe ser mayor que cero'
            ];
        }

        return $this->executeTransaction(function () use ($ingredientId, $quantity, $reason) {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $oldQuantity = $ingredient->current_quantity;
            $newQuantity = $oldQuantity + $quantity;

            // Actualizar cantidad
            $ingredient->update(['current_quantity' => $newQuantity]);

            // Registrar movimiento
            StockMovement::create([
                'ingredient_id' => $ingredientId,
                'type' => 'added',
                'quantity' => $quantity,
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason,
                'user_id' => Auth::id(),
                'notes' => "Stock añadido: +{$quantity}"
            ]);

            $this->logActivity('stock_added', 'inventory', [
                'ingredient_id' => $ingredientId,
                'ingredient_name' => $ingredient->name,
                'quantity_added' => $quantity,
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason
            ]);

            return [
                'success' => true,
                'message' => "Stock añadido: {$quantity} " . ($ingredient->unit->name ?? 'unidades'),
                'ingredient' => $this->formatIngredientData($ingredient->fresh())
            ];
        });
    }

    /**
     * Reducir stock de ingrediente
     */
    public function reduceStock(int $ingredientId, float $quantity, string $reason = 'manual_reduction'): array
    {
        $this->validateRequiredFields([
            'ingredient_id' => $ingredientId,
            'quantity' => $quantity
        ], ['ingredient_id', 'quantity']);

        if ($quantity <= 0) {
            return [
                'success' => false,
                'message' => 'La cantidad debe ser mayor que cero'
            ];
        }

        return $this->executeTransaction(function () use ($ingredientId, $quantity, $reason) {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $oldQuantity = $ingredient->current_quantity;

            if ($oldQuantity < $quantity) {
                return [
                    'success' => false,
                    'message' => "Stock insuficiente. Disponible: {$oldQuantity}, Requerido: {$quantity}"
                ];
            }

            $newQuantity = $oldQuantity - $quantity;

            // Actualizar cantidad
            $ingredient->update(['current_quantity' => $newQuantity]);

            // Registrar movimiento
            StockMovement::create([
                'ingredient_id' => $ingredientId,
                'type' => 'consumed',
                'quantity' => $quantity,
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason,
                'user_id' => Auth::id(),
                'notes' => "Stock reducido: -{$quantity}"
            ]);

            $this->logActivity('stock_reduced', 'inventory', [
                'ingredient_id' => $ingredientId,
                'ingredient_name' => $ingredient->name,
                'quantity_reduced' => $quantity,
                'previous_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $reason
            ]);

            // Verificar nivel de stock bajo
            if ($newQuantity <= $ingredient->min_stock_level) {
                $this->notifyLowStock($ingredient);
            }

            return [
                'success' => true,
                'message' => "Stock reducido: {$quantity} " . ($ingredient->unit->name ?? 'unidades'),
                'ingredient' => $this->formatIngredientData($ingredient->fresh()),
                'low_stock_warning' => $newQuantity <= $ingredient->min_stock_level
            ];
        });
    }

    /**
     * Notificar stock bajo
     */
    private function notifyLowStock(Ingredient $ingredient): void
    {
        $this->notifyUsers(
            ['kitchen', 'admin'],
            'Stock Bajo',
            "El ingrediente '{$ingredient->name}' tiene stock bajo: {$ingredient->current_quantity} " . ($ingredient->unit->name ?? 'unidades'),
            [
                'ingredient_id' => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'current_quantity' => $ingredient->current_quantity,
                'min_stock_level' => $ingredient->min_stock_level
            ]
        );
    }

    /**
     * Verificar disponibilidad para recetas
     */
    public function checkRecipeAvailability(array $productIds): array
    {
        $results = [];

        foreach ($productIds as $productId) {
            $product = Product::with('recipe.ingredients')->find($productId);

            if (!$product || !$product->recipe) {
                $results[$productId] = [
                    'available' => true,
                    'message' => 'Producto sin receta definida'
                ];
                continue;
            }

            $availability = $this->checkSingleRecipeAvailability($product->recipe);
            $results[$productId] = $availability;
        }

        return $results;
    }

    /**
     * Verificar disponibilidad de una receta
     */
    private function checkSingleRecipeAvailability(Recipe $recipe): array
    {
        $unavailableIngredients = [];
        $lowStockIngredients = [];

        foreach ($recipe->ingredients as $recipeIngredient) {
            $ingredient = $recipeIngredient->ingredient;
            $requiredQuantity = $recipeIngredient->quantity;
            $availableQuantity = $ingredient->current_quantity;

            if ($availableQuantity < $requiredQuantity) {
                $unavailableIngredients[] = [
                    'name' => $ingredient->name,
                    'required' => $requiredQuantity,
                    'available' => $availableQuantity,
                    'deficit' => $requiredQuantity - $availableQuantity
                ];
            } elseif ($availableQuantity <= $ingredient->min_stock_level) {
                $lowStockIngredients[] = [
                    'name' => $ingredient->name,
                    'current_stock' => $availableQuantity,
                    'min_level' => $ingredient->min_stock_level
                ];
            }
        }

        return [
            'available' => empty($unavailableIngredients),
            'unavailable_ingredients' => $unavailableIngredients,
            'low_stock_ingredients' => $lowStockIngredients,
            'message' => empty($unavailableIngredients) ?
                        'Ingredientes disponibles' :
                        'Ingredientes insuficientes para la preparación'
        ];
    }

    /**
     * Generar reporte de movimientos de inventario
     */
    public function getInventoryReport(array $filters = []): array
    {
        $query = StockMovement::with(['ingredient', 'user']);

        // Aplicar filtros de fecha
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Filtrar por tipo de movimiento
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Filtrar por ingrediente
        if (isset($filters['ingredient_id'])) {
            $query->where('ingredient_id', $filters['ingredient_id']);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        return [
            'movements' => $movements->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'ingredient_name' => $movement->ingredient->name,
                    'type' => $movement->type,
                    'quantity' => $movement->quantity,
                    'previous_quantity' => $movement->previous_quantity,
                    'new_quantity' => $movement->new_quantity,
                    'reason' => $movement->reason,
                    'user_name' => $movement->user->name ?? 'Sistema',
                    'notes' => $movement->notes,
                    'created_at' => $movement->created_at->format('Y-m-d H:i:s')
                ];
            }),
            'summary' => [
                'total_movements' => $movements->count(),
                'additions' => $movements->where('type', 'added')->sum('quantity'),
                'consumptions' => $movements->where('type', 'consumed')->sum('quantity'),
                'adjustments' => $movements->where('type', 'adjusted')->sum('quantity'),
                'by_ingredient' => $movements->groupBy('ingredient.name')
                                          ->map(function ($group) {
                                              return [
                                                  'movements' => $group->count(),
                                                  'total_added' => $group->where('type', 'added')->sum('quantity'),
                                                  'total_consumed' => $group->where('type', 'consumed')->sum('quantity')
                                              ];
                                          })
            ]
        ];
    }

    /**
     * Ajustar stock por inventario físico
     */
    public function adjustStock(int $ingredientId, float $actualQuantity, string $notes = ''): array
    {
        $this->validateRequiredFields([
            'ingredient_id' => $ingredientId,
            'actual_quantity' => $actualQuantity
        ], ['ingredient_id', 'actual_quantity']);

        if ($actualQuantity < 0) {
            return [
                'success' => false,
                'message' => 'La cantidad no puede ser negativa'
            ];
        }

        return $this->executeTransaction(function () use ($ingredientId, $actualQuantity, $notes) {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $systemQuantity = $ingredient->current_quantity;
            $difference = $actualQuantity - $systemQuantity;

            if (abs($difference) < 0.01) { // Tolerancia para diferencias menores
                return [
                    'success' => true,
                    'message' => 'No hay diferencia significativa en el inventario',
                    'ingredient' => $this->formatIngredientData($ingredient)
                ];
            }

            // Actualizar cantidad
            $ingredient->update(['current_quantity' => $actualQuantity]);

            // Registrar movimiento de ajuste
            StockMovement::create([
                'ingredient_id' => $ingredientId,
                'type' => 'adjusted',
                'quantity' => abs($difference),
                'previous_quantity' => $systemQuantity,
                'new_quantity' => $actualQuantity,
                'reason' => 'inventory_adjustment',
                'user_id' => Auth::id(),
                'notes' => $notes ?: "Ajuste de inventario físico. Diferencia: {$difference}"
            ]);

            $this->logActivity('stock_adjusted', 'inventory', [
                'ingredient_id' => $ingredientId,
                'ingredient_name' => $ingredient->name,
                'system_quantity' => $systemQuantity,
                'actual_quantity' => $actualQuantity,
                'difference' => $difference,
                'notes' => $notes
            ]);

            return [
                'success' => true,
                'message' => "Inventario ajustado. Diferencia: {$difference}",
                'ingredient' => $this->formatIngredientData($ingredient->fresh()),
                'adjustment' => [
                    'system_quantity' => $systemQuantity,
                    'actual_quantity' => $actualQuantity,
                    'difference' => $difference
                ]
            ];
        });
    }
}

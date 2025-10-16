<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'minimum_stock',
        'cost_per_unit',
        'supplier',
        'is_active'
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class)->withPivot('quantity');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Verificar si hay suficiente stock
    public function hasStock(float $quantity): bool
    {
        return $this->current_stock >= $quantity;
    }

    // Descontar del inventario
    public function decreaseStock(float $quantity, string $reason = 'Uso en producción', $userId = null, string $referenceType = null, $referenceId = null): void
    {
        if (!$this->hasStock($quantity)) {
            throw \App\Exceptions\IngredientException::insufficientStock(
                $this->name,
                $this->current_stock,
                $quantity
            );
        }

        $previousStock = $this->current_stock;
        $this->current_stock -= $quantity;
        $this->save();

        // Registrar movimiento de inventario
        $this->stockMovements()->create([
            'type' => StockMovement::TYPE_DECREASE,
            'quantity' => $quantity,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'moved_at' => now(),
            'user_id' => $userId
        ]);
    }

    // Aumentar inventario
    public function increaseStock(float $quantity, string $reason = 'Compra/Abastecimiento', $userId = null, string $referenceType = null, $referenceId = null): void
    {
        $previousStock = $this->current_stock;
        $this->current_stock += $quantity;
        $this->save();

        // Registrar movimiento de inventario
        $this->stockMovements()->create([
            'type' => StockMovement::TYPE_INCREASE,
            'quantity' => $quantity,
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'previous_stock' => $previousStock,
            'new_stock' => $this->current_stock,
            'moved_at' => now(),
            'user_id' => $userId
        ]);
    }

    // Verificar si está por debajo del stock mínimo
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}

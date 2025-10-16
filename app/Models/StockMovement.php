<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Constants\ValidationRules;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'type',
        'quantity',
        'reason',
        'reference_type',
        'reference_id',
        'previous_stock',
        'new_stock',
        'moved_at',
        'user_id',
    ];

    protected $casts = [
        'moved_at' => 'datetime',
        'quantity' => ValidationRules::DECIMAL_THREE,
        'previous_stock' => ValidationRules::DECIMAL_THREE,
        'new_stock' => ValidationRules::DECIMAL_THREE,
    ];

    /**
     * Tipos de movimientos de stock
     */
    const TYPE_INCREASE = 'increase';
    const TYPE_DECREASE = 'decrease';
    const TYPE_ADJUSTMENT = 'adjustment';

    /**
     * Razones comunes para movimientos
     */
    const REASON_PURCHASE = 'purchase'; // Compra de ingredientes
    const REASON_RECIPE_USE = 'recipe_use'; // Uso en recetas
    const REASON_WASTE = 'waste'; // Desperdicio
    const REASON_ADJUSTMENT = 'adjustment'; // Ajuste de inventario
    const REASON_RETURN = 'return'; // Devolución

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica para referencias (Order, Product, etc.)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Scope para movimientos de aumento
     */
    public function scopeIncrease($query)
    {
        return $query->where('type', self::TYPE_INCREASE);
    }

    /**
     * Scope para movimientos de disminución
     */
    public function scopeDecrease($query)
    {
        return $query->where('type', self::TYPE_DECREASE);
    }

    /**
     * Scope para una razón específica
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }
}

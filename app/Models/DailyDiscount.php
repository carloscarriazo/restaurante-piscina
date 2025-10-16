<?php

namespace App\Models;

use App\Constants\ValidationRules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyDiscount extends Model
{
    protected $fillable = [
        'discount_date',
        'minimum_purchase',
        'product_max_price',
        'product_id',
        'discount_amount',
        'discount_percentage',
        'is_active',
        'description'
    ];

    protected $casts = [
        'discount_date' => 'date',
        'minimum_purchase' => ValidationRules::DECIMAL_TWO,
        'product_max_price' => ValidationRules::DECIMAL_TWO,
        'discount_amount' => ValidationRules::DECIMAL_TWO,
        'discount_percentage' => ValidationRules::DECIMAL_TWO,
        'is_active' => 'boolean'
    ];

    /**
     * Relación con el producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Verificar si un pedido califica para descuento
     */
    public static function checkDiscountEligibility($orderTotal, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return static::where('discount_date', $date)
            ->where('is_active', true)
            ->where('minimum_purchase', '<=', $orderTotal)
            ->first();
    }

    /**
     * Obtener productos elegibles para descuento del día
     */
    public static function getEligibleProducts($date = null)
    {
        $date = $date ?? now()->toDateString();

        return static::with('product')
            ->where('discount_date', $date)
            ->where('is_active', true)
            ->whereHas('product', function ($query) {
                $query->where('price', '<=', 3500);
            })
            ->get();
    }
}

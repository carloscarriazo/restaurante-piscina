<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\ValidationRules;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'order_id',
        'user_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_method',
        'customer_name',
        'customer_id',
        'customer_phone',
        'notes',
        'status'
    ];

    protected $casts = [
        'subtotal' => ValidationRules::DECIMAL_TWO,
        'tax' => ValidationRules::DECIMAL_TWO,
        'discount' => ValidationRules::DECIMAL_TWO,
        'total' => ValidationRules::DECIMAL_TWO,
    ];

    /**
     * Relación con Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con User (cajero que generó la factura)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para facturas pagadas
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope para facturas anuladas
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope por método de pago
     */
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }
}

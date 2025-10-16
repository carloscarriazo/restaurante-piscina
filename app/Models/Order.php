<?php

namespace App\Models;

use App\Constants\ValidationRules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'table_id',
        'user_id',
        'status',
        'customer_name',
        'notes',
        'total',
        'subtotal',
        'tax',
        'discount',
        'combined_tables',
        'discount_amount',
        'discount_reason',
        'is_editable',
        'last_edited_at',
        'last_edited_by',
        'kitchen_notified',
        'kitchen_notified_at',
        'completed_at'
    ];

    protected $casts = [
        'combined_tables' => 'array',
        'total' => ValidationRules::DECIMAL_TWO,
        'subtotal' => ValidationRules::DECIMAL_TWO,
        'tax' => ValidationRules::DECIMAL_TWO,
        'discount' => ValidationRules::DECIMAL_TWO,
        'discount_amount' => ValidationRules::DECIMAL_TWO,
        'is_editable' => 'boolean',
        'kitchen_notified' => 'boolean',
        'last_edited_at' => 'datetime',
        'kitchen_notified_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lastEditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Calcular el total del pedido
     */
    public function getTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Calcular el total con descuento
     */
    public function getTotalWithDiscount()
    {
        return $this->getTotal() - $this->discount_amount;
    }

    /**
     * Verificar si el mesero puede editar este pedido
     * Optimizado para máximo 3 returns
     */
    public function canBeEditedBy(User $user): bool
    {
        if (!$this->is_editable || in_array($this->status, ['in_process', 'ready', 'served', 'delivered', 'cancelled'])) {
            return false;
        }

        if ($user->hasAnyRole(['Administrador', 'Gerente'])) {
            return true;
        }

        return $user->hasRole('Mesero') && $this->user_id === $user->id;
    }
    /**
     * Notificar a la cocina
     */
    public function notifyKitchen()
    {
        $this->update([
            'kitchen_notified' => true,
            'kitchen_notified_at' => now()
        ]);

        // Aquí puedes agregar lógica de notificación en tiempo real
        // Por ejemplo, usando WebSockets, notificaciones push, etc.
    }

    /**
     * Combinar con otras mesas para facturación
     */
    public function combineWithTables(array $tableIds)
    {
        $this->update([
            'combined_tables' => $tableIds
        ]);
    }

    /**
     * Aplicar descuento del día
     */
    public function applyDailyDiscount()
    {
        $discount = DailyDiscount::checkDiscountEligibility($this->getTotal());

        if ($discount) {
            $discountAmount = $discount->discount_amount ?:
                ($this->getTotal() * $discount->discount_percentage / 100);

            $this->update([
                'discount_amount' => $discountAmount,
                'discount_reason' => $discount->description ?: 'Descuento del día'
            ]);
        }
    }
}

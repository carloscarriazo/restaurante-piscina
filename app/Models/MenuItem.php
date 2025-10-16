<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_category_id',
        'product_id',
        'name',
        'description',
        'price',
        'size',
        'ingredients',
        'image_url',
        'is_available',
        'is_featured',
        'sort_order',
        'operating_days',
        'valid_from',
        'valid_until'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'operating_days' => 'array',
        'valid_from' => 'date',
        'valid_until' => 'date'
    ];

    // Días de operación por defecto: viernes (5), sábado (6), domingo (0)
    const OPERATING_DAYS = [5, 6, 0];

    const DAY_NAMES = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    ];

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtiene el precio: del producto vinculado o del precio propio
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->product_id && $this->product) {
            return $this->product->price;
        }
        return $this->price;
    }

    /**
     * Obtiene el precio formateado
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->effective_price, 0);
    }

    /**
     * Verifica si el menú está disponible hoy
     */
    public function isAvailableToday(): bool
    {
        $today = Carbon::now()->dayOfWeek;
        $operatingDays = $this->operating_days ?? self::OPERATING_DAYS;

        return in_array($today, $operatingDays);
    }

    /**
     * Verifica si está dentro del período de validez
     */
    public function isValidPeriod(): bool
    {
        $now = Carbon::now()->startOfDay();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Scope para items disponibles solo en días de operación
     */
    public function scopeAvailableToday($query)
    {
        $today = Carbon::now()->dayOfWeek;

        return $query->where('is_available', true)
            ->where(function ($q) use ($today) {
                $q->whereJsonContains('operating_days', $today)
                    ->orWhereNull('operating_days');
            });
    }

    /**
     * Scope para items válidos en el período actual
     */
    public function scopeValidPeriod($query)
    {
        $now = Carbon::now()->startOfDay();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $now);
        });
    }

    /**
     * Obtiene los nombres de los días de operación
     */
    public function getOperatingDaysNamesAttribute()
    {
        $days = $this->operating_days ?? self::OPERATING_DAYS;
        return collect($days)->map(fn($day) => self::DAY_NAMES[$day])->implode(', ');
    }
}

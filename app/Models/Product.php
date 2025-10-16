<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'unit_id',
        'stock',
        'stock_minimo',
        'available',
        'active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'available' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products')->singleFile();
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();
    }

    /**
     * Obtener la URL de la imagen principal del producto
     */
    public function getImageUrl(): ?string
    {
        $media = $this->getFirstMedia('products');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Obtener la URL de la imagen thumbnail
     */
    public function getImageThumbUrl(): ?string
    {
        $media = $this->getFirstMedia('products');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Accessor para mantener compatibilidad con image_url
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->getImageUrl();
    }

    /**
     * Verifica si el producto puede ser preparado (hay suficientes ingredientes)
     */
    public function canBePrepared(int $quantity = 1): bool
    {
        foreach ($this->recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                $requiredQuantity = $ingredient->pivot->quantity * $quantity;
                if ($ingredient->current_stock < $requiredQuantity) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Obtiene los ingredientes faltantes para preparar el producto
     */
    public function getMissingIngredients(int $quantity = 1): array
    {
        $missingIngredients = [];

        foreach ($this->recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                $requiredQuantity = $ingredient->pivot->quantity * $quantity;
                if ($ingredient->current_stock < $requiredQuantity) {
                    $missingIngredients[] = [
                        'ingredient' => $ingredient,
                        'required' => $requiredQuantity,
                        'available' => $ingredient->current_stock,
                        'missing' => $requiredQuantity - $ingredient->current_stock
                    ];
                }
            }
        }

        return $missingIngredients;
    }

    /**
     * Consume los ingredientes necesarios para preparar el producto
     */
    public function consumeIngredients(int $quantity = 1, $orderId = null, $userId = null): bool
    {
        // Verificar si hay suficientes ingredientes
        if (!$this->canBePrepared($quantity)) {
            throw new \InvalidArgumentException('No hay suficientes ingredientes para preparar ' . $quantity . ' unidad(es) de ' . $this->name);
        }

        // Consumir cada ingrediente
        foreach ($this->recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                $requiredQuantity = $ingredient->pivot->quantity * $quantity;

                // Crear movimiento de stock
                \App\Models\StockMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'type' => 'consumed',
                    'quantity' => $requiredQuantity,
                    'previous_quantity' => $ingredient->current_stock,
                    'new_quantity' => $ingredient->current_stock - $requiredQuantity,
                    'reason' => 'recipe_consumption',
                    'user_id' => $userId,
                    'notes' => "Consumo por receta - Orden #{$orderId}"
                ]);

                // Actualizar cantidad del ingrediente
                $ingredient->update(['current_stock' => $ingredient->current_stock - $requiredQuantity]);
            }
        }

        return true;
    }

    /**
     * Calcula el costo estimado de ingredientes para el producto
     */
    public function getIngredientsCost(): float
    {
        $totalCost = 0;

        foreach ($this->recipes as $recipe) {
            foreach ($recipe->ingredients as $ingredient) {
                if ($ingredient->cost_per_unit) {
                    $totalCost += $ingredient->pivot->quantity * $ingredient->cost_per_unit;
                }
            }
        }

        return $totalCost;
    }

    /**
     * Obtiene el margen de ganancia del producto
     */
    public function getProfitMargin(): ?float
    {
        $ingredientsCost = $this->getIngredientsCost();

        if ($ingredientsCost > 0) {
            return (($this->price - $ingredientsCost) / $this->price) * 100;
        }

        return null;
    }
}

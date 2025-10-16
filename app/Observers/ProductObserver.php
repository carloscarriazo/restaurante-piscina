<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     * Invalida el caché de menú al crear un producto
     */
    public function created(Product $product): void
    {
        $this->clearMenuCache($product);
    }

    /**
     * Handle the Product "updated" event.
     * Invalida el caché de menú al actualizar un producto
     */
    public function updated(Product $product): void
    {
        $this->clearMenuCache($product);
    }

    /**
     * Handle the Product "deleted" event.
     * Invalida el caché de menú al eliminar un producto
     */
    public function deleted(Product $product): void
    {
        $this->clearMenuCache($product);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->clearMenuCache($product);
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->clearMenuCache($product);
    }

    /**
     * Limpia el caché relacionado con el menú
     */
    private function clearMenuCache(Product $product): void
    {
        // Invalidar caché de categorías
        Cache::forget('menu_categories');

        // Invalidar caché de todos los productos
        Cache::forget('all_active_products');

        // Invalidar caché de productos por categoría
        if ($product->category_id) {
            Cache::forget("category_{$product->category_id}_products");
            Cache::forget("category_{$product->category_id}");
        }
    }
}

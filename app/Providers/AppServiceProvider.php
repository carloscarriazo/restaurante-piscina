<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\NotificationServiceInterface;
use App\Contracts\WaiterServiceInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\MenuRepositoryInterface;
use App\Services\NotificationService;
use App\Services\WaiterService;
use App\Repositories\OrderRepository;
use App\Repositories\MenuRepository;
use App\Models\Product;
use App\Observers\ProductObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar servicios siguiendo principios SOLID
        $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
        $this->app->bind(WaiterServiceInterface::class, WaiterService::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers para invalidación de caché
        Product::observe(ProductObserver::class);
    }
}

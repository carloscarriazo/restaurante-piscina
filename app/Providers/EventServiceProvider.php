<?php

namespace App\Providers;

use App\Listeners\LogLogin;
use App\Listeners\LogLogout;
use App\Events\KitchenOrderReady;
use App\Events\LowStockAlert;
use App\Events\OrderStatusChanged;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            LogLogin::class,
        ],
        Logout::class => [
            LogLogout::class,
        ],
        // Eventos de broadcasting para notificaciones en tiempo real
        KitchenOrderReady::class => [],
        LowStockAlert::class => [],
        OrderStatusChanged::class => [],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

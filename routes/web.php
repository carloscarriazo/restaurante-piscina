<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Constants\RouteConstants;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta GET para logout (redirige al dashboard con logout automático)
Route::get('/logout', function () {
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect('/');
})->name('logout.get');

Route::middleware([
    RouteConstants::AUTH_SANCTUM,
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get(RouteConstants::DASHBOARD_PATH, [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/productos', function () {
    return view('products');
});

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/pedidos', function () {
    return view('orders');
});

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/inventario', function () {
    return view('inventory');
})->name('inventario');

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/recetas', function () {
    return view('recipes');
})->name('recetas');

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/usuarios', function () {
    return view('users');
})->name('usuarios');

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/reportes', \App\Livewire\ReportDashboard::class)->name('reportes');

Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->get('/test-livewire', function () {
    return view('test-livewire');
});

// Rutas del menú digital (públicas)
Route::prefix('menu')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/digital', [MenuController::class, 'digital'])->name('menu.digital');
    Route::get('/digital-preview', [MenuController::class, 'digitalPreview'])->name('menu.digital.preview');
    Route::get('/qr', [MenuController::class, 'qr'])->name('menu.qr');
});

// Ruta para gestión de menú (requiere autenticación)
Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->group(function () {
    Route::get('/menu/manage', [MenuController::class, 'manage'])->name('menu.manage');
});

// Rutas de cocina (requieren autenticación)
Route::prefix('kitchen')->middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->group(function () {
    Route::get(RouteConstants::DASHBOARD_PATH, [KitchenController::class, 'dashboard'])->name('kitchen.dashboard');
    Route::get('/login', [KitchenController::class, 'login'])->name('kitchen.login');
});

// Rutas de meseros (acceso público para pantallas dedicadas)
Route::prefix('waiter')->group(function () {
    Route::get(RouteConstants::DASHBOARD_PATH, function () {
        return view('waiter.dashboard');
    })->name('waiter.dashboard');
    Route::get('/menu', function () {
        return view('waiter.menu');
    })->name('waiter.menu');
});

// API del menú (para la app móvil)
Route::prefix('api/menu')->group(function () {
    Route::get('/', [MenuController::class, 'apiIndex']);
    Route::get('/category/{id}', [MenuController::class, 'apiCategory']);
});

// Rutas de administración (requieren autenticación) - Usando Livewire Components
Route::middleware([RouteConstants::AUTH_SANCTUM, 'verified'])->group(function () {
    // Gestión de categorías usando Livewire
    Route::get('categories', function () {
        return view('categories');
    })->name('categories.index');

    // Gestión de productos - CRUD completo
    Route::resource('products', ProductController::class);

    // Ruta adicional para toggle de disponibilidad de producto (PATCH custom)
    Route::patch('products/{product}/toggle-availability', [ProductController::class, 'toggleAvailability'])
        ->name('products.toggle-availability');

    // Gestión de mesas - CRUD completo
    Route::resource('tables', TableController::class);

    // Gestión de inventario
    Route::get('inventory', function () {
        return view('inventory');
    })->name('inventory.index');

    // Gestión de recetas
    Route::get('recipes', function () {
        return view('recipes');
    })->name('recipes.index');

    // Gestión de usuarios y configuración
    Route::get('users', function () {
        return view('users');
    })->name('users.index');
});

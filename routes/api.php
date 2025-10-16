<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Constants\RouteConstants;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TableApiController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\WaiterController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\TableAssignmentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\UserController;

// Rutas de autenticación (públicas con rate limiting)
Route::prefix('auth')->group(function () {
    // Rate limiting estricto para login: 5 intentos por minuto
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Rate limiting para registro: 3 intentos por minuto
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1');

    // Rutas protegidas de autenticación
    Route::middleware(RouteConstants::AUTH_SANCTUM)->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Rutas de cocina (acceso público para pantallas de cocina con rate limiting)
Route::prefix('kitchen')->middleware('throttle:120,1')->group(function () {
    Route::get('/test', [KitchenController::class, 'test']);
    Route::get(RouteConstants::ORDERS_PATH, [KitchenController::class, 'orders']);
    Route::get(RouteConstants::ORDERS_ID_DETAILS, [KitchenController::class, 'orderDetails']);
    Route::post(RouteConstants::ORDERS_ID_START_PREPARING, [KitchenController::class, 'startPreparing']);
    Route::post(RouteConstants::ORDERS_ID_MARK_READY, [KitchenController::class, 'markAsReady']);
    Route::post(RouteConstants::ORDERS_ID_NOTIFY_WAITERS, [KitchenController::class, 'notifyWaiters']);
    Route::get(RouteConstants::STATS_PATH, [KitchenController::class, 'kitchenStats']);
});

// Rutas de meseros (acceso público para pantallas de meseros con rate limiting)
Route::prefix('waiter')->middleware('throttle:120,1')->group(function () {
    Route::get('/orders', [WaiterController::class, 'getOrders']);
    Route::get('/orders/ready', [WaiterController::class, 'getReadyOrders']);
    Route::get('/orders/{id}/details', [WaiterController::class, 'getOrderDetails']);
    Route::post('/orders', [WaiterController::class, 'createOrder']);
    Route::put('/orders/{id}', [WaiterController::class, 'updateOrder']);
    Route::post('/orders/{id}/mark-delivered', [WaiterController::class, 'markAsDelivered']);
    Route::get('/notifications', [WaiterController::class, 'getNotifications']);
    Route::post('/notifications/{id}/mark-read', [WaiterController::class, 'markNotificationAsRead']);
    Route::get('/stats', [WaiterController::class, 'getStats']);

    // Rutas del menú para meseros
    Route::get('/menu/categories', [\App\Http\Controllers\Api\MenuController::class, 'getCategories']);
    Route::get('/menu/categories/{id}/products', [\App\Http\Controllers\Api\MenuController::class, 'getProductsByCategory']);
    Route::get('/menu/products', [\App\Http\Controllers\Api\MenuController::class, 'getAllProducts']);
    Route::get('/menu/search', [\App\Http\Controllers\Api\MenuController::class, 'searchProducts']);
    Route::get('/tables', [\App\Http\Controllers\Api\MenuController::class, 'getTables']);
});

// Rutas protegidas por autenticación con rate limiting (60 req/minuto)
Route::middleware(['auth:sanctum', 'api.logging', 'throttle:60,1'])->group(function () {

    // Información del usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    // Rutas de órdenes (refactorizadas)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get(RouteConstants::ID_PARAM, [OrderController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [OrderController::class, 'update']);
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus']);
        Route::delete(RouteConstants::ID_PARAM, [OrderController::class, 'destroy']);
    });

    // Rutas de facturación (separadas)
    Route::prefix('billing')->group(function () {
        Route::post('/combine', [BillingController::class, 'combineBilling']);
        Route::post('/payment/{orderId}', [BillingController::class, 'processPayment']);
    });

    // Rutas de productos
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductApiController::class, 'index']);
        Route::post('/', [ProductApiController::class, 'store']);
        Route::get('/categories', [ProductApiController::class, 'categories']);
        Route::get('/category/{categoryId}', [ProductApiController::class, 'productsByCategory']);

        Route::get(RouteConstants::ID_PARAM, [ProductApiController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [ProductApiController::class, 'update']);
        Route::patch('/{id}/toggle-availability', [ProductApiController::class, 'toggleAvailability']);
        Route::delete(RouteConstants::ID_PARAM, [ProductApiController::class, 'destroy']);
    });

    // Rutas de mesas
    Route::prefix('tables')->group(function () {
        Route::get('/', [TableApiController::class, 'index']);
        Route::post('/', [TableApiController::class, 'store']);
        Route::get('/status', [TableApiController::class, 'status']);

        Route::get(RouteConstants::ID_PARAM, [TableApiController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [TableApiController::class, 'update']);
        Route::patch('/{id}/toggle-availability', [TableApiController::class, 'toggleAvailability']);
        Route::patch('/{id}/free', [TableApiController::class, 'free']);
        Route::get('/{id}/orders', [TableApiController::class, 'orders']);
        Route::delete(RouteConstants::ID_PARAM, [TableApiController::class, 'destroy']);
    });

    // Rutas de asignación de mesas a meseros
    Route::prefix('table-assignments')->group(function () {
        Route::get('/', [TableAssignmentController::class, 'index']);
        Route::post('/assign', [TableAssignmentController::class, 'assign']);
        Route::post('/unassign', [TableAssignmentController::class, 'unassign']);
        Route::post('/bulk-assign', [TableAssignmentController::class, 'bulkAssign']);
        Route::get('/waiter/{waiterId}', [TableAssignmentController::class, 'waiterTables']);
        Route::get('/available-waiters', [TableAssignmentController::class, 'availableWaiters']);
    });

    // Rutas de notificaciones
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationApiController::class, 'index']);
        Route::get('/stats', [NotificationApiController::class, 'stats']);
        Route::get('/realtime', [NotificationApiController::class, 'realtime']);
        Route::post('/test', [NotificationApiController::class, 'testNotification']);
        Route::post('/{notificationId}/read', [NotificationApiController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationApiController::class, 'markAllAsRead']);
        Route::delete('/{notificationId}', [NotificationApiController::class, 'destroy']);
    });

    // Rutas de reportes y analytics
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'dashboard']);
        Route::get('/daily-sales', [ReportController::class, 'dailySales']);
        Route::get('/weekly-sales', [ReportController::class, 'weeklySales']);
        Route::get('/inventory', [ReportController::class, 'inventory']);
        Route::get('/table-usage', [ReportController::class, 'tableUsage']);
    });

    // Rutas de categorías
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get(RouteConstants::ID_PARAM, [CategoryController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [CategoryController::class, 'update']);
        Route::delete(RouteConstants::ID_PARAM, [CategoryController::class, 'destroy']);
    });

    // Rutas de ingredientes
    Route::prefix('ingredients')->group(function () {
        Route::get('/', [IngredientController::class, 'index']);
        Route::post('/', [IngredientController::class, 'store']);
        Route::get(RouteConstants::ID_PARAM, [IngredientController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [IngredientController::class, 'update']);
        Route::delete(RouteConstants::ID_PARAM, [IngredientController::class, 'destroy']);
        Route::post('/{id}/adjust-stock', [IngredientController::class, 'adjustStock']);
        Route::get('/{id}/movements', [IngredientController::class, 'movements']);
    });

    // Rutas de recetas
    Route::prefix('recipes')->group(function () {
        Route::get('/', [RecipeController::class, 'index']);
        Route::post('/', [RecipeController::class, 'store']);
        Route::get(RouteConstants::ID_PARAM, [RecipeController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [RecipeController::class, 'update']);
        Route::delete(RouteConstants::ID_PARAM, [RecipeController::class, 'destroy']);
        Route::get('/product/{productId}', [RecipeController::class, 'byProduct']);
    });

    // Rutas de facturas
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::get('/daily-summary', [InvoiceController::class, 'dailySummary']);
        Route::get(RouteConstants::ID_PARAM, [InvoiceController::class, 'show']);
        Route::post('/{id}/cancel', [InvoiceController::class, 'cancel']);
    });

    // Rutas de usuarios
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/roles/available', [UserController::class, 'availableRoles']);
        Route::get(RouteConstants::ID_PARAM, [UserController::class, 'show']);
        Route::put(RouteConstants::ID_PARAM, [UserController::class, 'update']);
        Route::delete(RouteConstants::ID_PARAM, [UserController::class, 'destroy']);
    });

});

// Ruta de prueba de API
Route::get('/ping', function () {
    return response()->json([
        'message' => 'Blue Lagoon API funcionando correctamente',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

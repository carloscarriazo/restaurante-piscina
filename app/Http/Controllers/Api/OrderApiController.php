<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\KitchenService;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Requests\Api\UpdateOrderRequest;
use App\Http\Requests\Api\UpdateOrderStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderApiController extends BaseApiController
{
    protected $orderService;
    protected $kitchenService;

    public function __construct(OrderService $orderService, KitchenService $kitchenService)
    {
        $this->orderService = $orderService;
        $this->kitchenService = $kitchenService;
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Obtener todos los pedidos",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="table_id",
     *         in="query",
     *         description="Filtrar por mesa",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pedidos"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'table_id', 'date_from', 'date_to']);
            $orders = $this->orderService->getAllOrders($filters);

            return $this->successResponse($orders, 'Pedidos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener pedidos: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Crear nuevo pedido",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"table_id","items"},
     *             @OA\Property(property="table_id", type="integer", example=1),
     *             @OA\Property(property="customer_name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="notes", type="string", example="Sin cebolla"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="notes", type="string", example="Término medio")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pedido creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Errores de validación"
     *     )
     * )
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $result = $this->orderService->create($request->validated());

            if (!$result['success']) {
                return $this->errorResponse($result['message']);
            }

            return $this->successResponse($result['order'] ?? $result['data'], 'Pedido creado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al crear pedido: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Obtener un pedido específico",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del pedido"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pedido no encontrado"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['table', 'items.product', 'user'])
                          ->findOrFail($id);

            return $this->successResponse($order, 'Pedido obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->notFoundResponse('Orden no encontrada');
        }
    }

        /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Actualizar una orden existente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = $this->findOrderOrFail($id);
            $this->authorizeOrderEdit($user, $order);

            $this->orderService->editOrder($order, $user, $request->validated());
            $order->load(['table', 'items.product', 'user']);

            return $this->successResponse($order, 'Orden actualizada exitosamente');

        } catch (\Exception $e) {
            return $this->handleOrderException($e, 'actualizar');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     tags={"Orders"},
     *     summary="Cancelar un pedido",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pedido cancelado"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        try {
            $order = $this->findOrderOrFail($id);
            $this->validateCancellableStatus($order);

            $order->update(['status' => 'cancelled']);
            return $this->successResponse($order, 'Pedido cancelado exitosamente');

        } catch (\Exception $e) {
            return $this->handleOrderException($e, 'cancelar');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders/{id}/status",
     *     tags={"Orders"},
     *     summary="Actualizar estado del pedido",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending","confirmed","preparing","ready","served","cancelled"},
     *                 example="preparing"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado actualizado"
     *     )
     * )
     */
    public function updateStatus(UpdateOrderStatusRequest $request, $id): JsonResponse
    {
        try {
            $order = $this->orderService->changeStatus($id, $request->input('status'));

            return $this->successResponse($order, 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al actualizar estado: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders/combine",
     *     tags={"Orders"},
     *     summary="Combinar facturación de múltiples pedidos",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_ids"},
     *             @OA\Property(
     *                 property="order_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1,2,3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facturación combinada"
     *     )
     * )
     */
    public function combineBilling(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_ids' => 'required|array|min:2',
                'order_ids.*' => 'exists:orders,id'
            ]);

            $result = $this->orderService->combineBilling($request->order_ids, Auth::user());

            return $this->successResponse($result, 'Facturación combinada exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al combinar facturación: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/kitchen",
     *     tags={"Orders"},
     *     summary="Obtener órdenes pendientes para cocina",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de órdenes para cocina"
     *     )
     * )
     */
    public function kitchenOrders(): JsonResponse
    {
        try {
            $orders = Order::with(['table', 'items.product'])
                          ->whereIn('status', ['pending', 'confirmed', 'preparing'])
                          ->orderBy('created_at', 'asc')
                          ->get();

            return $this->successResponse($orders, 'Órdenes de cocina obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener órdenes de cocina: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/daily-discounts",
     *     tags={"Orders"},
     *     summary="Obtener descuentos del día",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de descuentos activos"
     *     )
     * )
     */
    public function dailyDiscounts(): JsonResponse
    {
        try {
            $today = Carbon::today();
            $discounts = \App\Models\DailyDiscount::where('date', $today)
                                                  ->where('is_active', true)
                                                  ->get();

            return $this->successResponse($discounts, 'Descuentos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener descuentos: ' . $e->getMessage());
        }
    }

    /**
     * Métodos privados para SRP
     */
    private function findOrderOrFail($id): Order
    {
        return Order::findOrFail($id);
    }

    private function authorizeOrderEdit($user, Order $order): void
    {
        if (!$user->hasAnyRole(['Mesero', 'Administrador'])) {
            throw new \Illuminate\Auth\Access\AuthorizationException('No tienes permisos para editar órdenes');
        }

        if (!$order->canBeEditedBy($user)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('Esta orden no puede ser editada');
        }
    }

    private function validateCancellableStatus(Order $order): void
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            throw new \InvalidArgumentException('Solo se pueden cancelar pedidos pendientes o confirmados');
        }
    }

    /**
     * Método centralizado para manejar excepciones (SRP + OCP)
     * Usa patrón Strategy para eliminar múltiples returns
     */
    private function handleOrderException(\Exception $e, string $action): JsonResponse
    {
        $handlers = [
            \Illuminate\Database\Eloquent\ModelNotFoundException::class =>
                fn() => $this->notFoundResponse('Pedido no encontrado'),
            \Illuminate\Auth\Access\AuthorizationException::class =>
                fn() => $this->forbiddenResponse($e->getMessage()),
            \InvalidArgumentException::class =>
                fn() => $this->errorResponse($e->getMessage(), 400),
        ];

        $handler = $handlers[get_class($e)] ??
            fn() => $this->serverErrorResponse("Error al {$action} pedido: " . $e->getMessage());

        return $handler();
    }
}

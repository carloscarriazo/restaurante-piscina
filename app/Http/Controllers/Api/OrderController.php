<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderServiceInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderRepository;
    protected $orderService;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderServiceInterface $orderService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    /**
     * Obtener todas las órdenes
     */
    public function index(Request $request)
    {
        try {
            $orders = $this->orderRepository->getAllWithFilters($request);

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las órdenes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva orden
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $orderData = $request->all();
            $orderData['waiter_id'] = Auth::id();

            $order = $this->orderService->createOrder($orderData);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada exitosamente',
                'data' => $order->load(['table', 'items.product', 'status'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una orden específica
     */
    public function show($id)
    {
        try {
            $order = $this->orderRepository->findById($id);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada'
            ], 404);
        }
    }

    /**
     * Actualizar una orden
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'items' => 'sometimes|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $order = $this->orderRepository->findById($id);
            $user = Auth::user();

            $updatedOrder = $this->orderService->editOrder($order, $user, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizada exitosamente',
                'data' => $updatedOrder->load(['table', 'items.product', 'status'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una orden
     */
    public function destroy($id)
    {
        try {
            $order = $this->orderRepository->findById($id);

            $this->orderRepository->delete($order);

            return response()->json([
                'success' => true,
                'message' => 'Orden eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de orden
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|exists:order_statuses,id'
        ]);

        try {
            $order = $this->orderRepository->findById($id);

            $updatedOrder = $this->orderRepository->updateStatus($order, $request->status_id);

            return response()->json([
                'success' => true,
                'message' => 'Estado de orden actualizado exitosamente',
                'data' => $updatedOrder->load(['status'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}

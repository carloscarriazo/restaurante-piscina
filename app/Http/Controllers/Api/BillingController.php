<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\OrderServiceInterface;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Combinar órdenes para facturación
     */
    public function combineBilling(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:2',
            'order_ids.*' => 'exists:orders,id'
        ]);

        try {
            $result = $this->orderService->combineBilling($request->order_ids);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['order']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al combinar facturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar pago de orden
     */
    public function processPayment(Request $request, $orderId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'reference' => 'nullable|string|max:100'
        ]);

        try {
            $order = \App\Models\Order::findOrFail($orderId);

            $success = $this->orderService->processPayment($order, $request->all());

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pago procesado exitosamente',
                    'data' => $order->fresh()->load(['payment', 'status'])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el pago'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }
}

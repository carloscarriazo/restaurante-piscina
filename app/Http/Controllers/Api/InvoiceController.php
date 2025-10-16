<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Facturas",
 *     description="Gestión completa de facturas para caja"
 * )
 */
class InvoiceController extends BaseApiController
{
    // Constantes para mensajes (DRY Principle)
    private const MSG_INVOICE_NOT_FOUND = 'Factura no encontrada';
    private const MSG_INVOICE_CREATED = 'Factura creada exitosamente';
    private const MSG_INVOICE_CANCELLED = 'Factura anulada exitosamente';
    private const MSG_INVOICE_ALREADY_CANCELLED = 'La factura ya está anulada';

    /**
     * @OA\Get(
     *     path="/api/invoices",
     *     summary="Listar todas las facturas",
     *     tags={"Facturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filtrar por fecha (YYYY-MM-DD)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="payment_method",
     *         in="query",
     *         description="Filtrar por método de pago",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de facturas")
     * )
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['order.items.product', 'order.table', 'user']);

        // Filtrar por fecha
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtrar por método de pago
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtrar por rango de fechas
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Ordenar por más reciente
        $query->latest();

        $invoices = $request->has('per_page')
            ? $query->paginate($request->per_page)
            : $query->paginate(50);

        return $this->successResponse($invoices, 'Facturas obtenidas exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/invoices",
     *     summary="Crear nueva factura",
     *     tags={"Facturas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "payment_method"},
     *             @OA\Property(property="order_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", enum={"cash", "card", "transfer"}, example="cash"),
     *             @OA\Property(property="customer_name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="customer_id", type="string", example="V-12345678"),
     *             @OA\Property(property="customer_phone", type="string", example="+58424-1234567"),
     *             @OA\Property(property="notes", type="string", example="Cliente frecuente")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Factura creada")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = $this->validateInvoiceCreation($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            $order = Order::with('items.product')->find($request->order_id);
            $this->validateOrderForInvoicing($order);

            DB::beginTransaction();

            // Calcular totales
            $subtotal = $order->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $tax = $subtotal * 0.16; // IVA 16%
            $total = $subtotal + $tax - $order->discount_amount;

            // Generar número de factura único
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad(
                Invoice::whereDate('created_at', now()->toDateString())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $order->discount_amount,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'customer_name' => $request->customer_name,
                'customer_id' => $request->customer_id,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
                'status' => 'paid'
            ]);

            // Actualizar estado del pedido
            $order->update(['status' => 'billed']);

            // Liberar mesa si está asignada
            if ($order->table_id) {
                $order->table->update(['status' => 'available']);
            }

            DB::commit();

            $invoice->load(['order.items.product', 'order.table', 'user']);

            return $this->successResponse($invoice, self::MSG_INVOICE_CREATED, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleInvoiceException($e, 'crear factura');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/invoices/{id}",
     *     summary="Obtener detalle de una factura",
     *     tags={"Facturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle completo de la factura")
     * )
     */
    public function show($id)
    {
        $invoice = Invoice::with([
            'order.items.product',
            'order.table',
            'order.user',
            'user'
        ])->find($id);

        if (!$invoice) {
            return $this->errorResponse(self::MSG_INVOICE_NOT_FOUND, 404);
        }

        return $this->successResponse($invoice);
    }

    /**
     * @OA\Get(
     *     path="/api/invoices/daily-summary",
     *     summary="Resumen de ventas del día para cierre de caja",
     *     tags={"Facturas"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Fecha (YYYY-MM-DD), por defecto hoy",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Resumen financiero del día")
     * )
     */
    public function dailySummary(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $invoices = Invoice::whereDate('created_at', $date)->get();

        $summary = [
            'date' => $date,
            'total_invoices' => $invoices->count(),
            'total_sales' => $invoices->sum('total'),
            'total_tax' => $invoices->sum('tax'),
            'total_discounts' => $invoices->sum('discount'),
            'payment_methods' => [
                'cash' => $invoices->where('payment_method', 'cash')->sum('total'),
                'card' => $invoices->where('payment_method', 'card')->sum('total'),
                'transfer' => $invoices->where('payment_method', 'transfer')->sum('total'),
            ],
            'invoices' => $invoices
        ];

        return $this->successResponse($summary, 'Resumen diario obtenido exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/invoices/{id}/cancel",
     *     summary="Anular una factura",
     *     tags={"Facturas"},
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
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", example="Error en facturación")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Factura anulada")
     * )
     */
    public function cancel(Request $request, $id)
    {
        try {
            $invoice = $this->findInvoiceOrFail($id);
            $this->validateCancellableInvoice($invoice);

            $validator = $this->validateCancellationReason($request);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            DB::beginTransaction();

            $invoice->update([
                'status' => 'cancelled',
                'notes' => ($invoice->notes ?? '') . "\n\nANULADA: " . $request->reason . ' - ' . now()
            ]);

            // Revertir estado del pedido
            $invoice->order->update(['status' => 'delivered']);

            DB::commit();

            return $this->successResponse($invoice, self::MSG_INVOICE_CANCELLED);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->handleInvoiceException($e, 'anular factura');
        }
    }

    /**
     * Métodos privados para SRP
     */
    private function validateInvoiceCreation(Request $request)
    {
        return Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:cash,card,transfer',
            'customer_name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|string|max:50',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500'
        ], [
            'order_id.required' => 'El pedido es obligatorio',
            'order_id.exists' => 'El pedido no existe',
            'payment_method.required' => 'El método de pago es obligatorio',
            'payment_method.in' => 'Método de pago inválido'
        ]);
    }

    private function validateOrderForInvoicing(Order $order): void
    {
        if (Invoice::where('order_id', $order->id)->exists()) {
            throw new \InvalidArgumentException('El pedido ya tiene una factura generada');
        }

        if (!in_array($order->status, ['served', 'delivered'])) {
            throw new \InvalidArgumentException('Solo se pueden facturar pedidos servidos o entregados');
        }
    }

    private function findInvoiceOrFail($id): Invoice
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(self::MSG_INVOICE_NOT_FOUND);
        }

        return $invoice;
    }

    private function validateCancellableInvoice(Invoice $invoice): void
    {
        if ($invoice->status === 'cancelled') {
            throw new \InvalidArgumentException(self::MSG_INVOICE_ALREADY_CANCELLED);
        }
    }

    private function validateCancellationReason(Request $request)
    {
        return Validator::make($request->all(), [
            'reason' => 'required|string|max:255'
        ], [
            'reason.required' => 'Debe especificar la razón de anulación'
        ]);
    }

    /**
     * Manejador centralizado de excepciones (SRP + OCP + Strategy Pattern)
     */
    private function handleInvoiceException(\Exception $e, string $action): JsonResponse
    {
        $handlers = [
            \Illuminate\Database\Eloquent\ModelNotFoundException::class =>
                fn($ex) => $this->errorResponse(self::MSG_INVOICE_NOT_FOUND, 404),
            \InvalidArgumentException::class =>
                fn($ex) => $this->errorResponse($ex->getMessage(), 400),
        ];

        $handler = $handlers[get_class($e)] ??
            fn($ex) => $this->errorResponse("Error al {$action}: " . $ex->getMessage(), 500);

        return $handler($e);
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Exception;

class BillingService extends BaseService
{
    /**
     * Procesar pago y generar factura
     */
    public function processPayment(array $paymentData): array
    {
        $this->validateRequiredFields($paymentData, ['order_ids', 'payment_method_id', 'amount_paid']);

        return $this->executeTransaction(function () use ($paymentData) {
            $orders = Order::whereIn('id', $paymentData['order_ids'])
                          ->where('status', 'ready')
                          ->get();

            if ($orders->isEmpty()) {
                throw new Exception('No hay pedidos válidos para procesar el pago');
            }

            $totalAmount = $orders->sum('total');
            $amountPaid = $paymentData['amount_paid'];

            if ($amountPaid < $totalAmount) {
                throw new Exception('El monto pagado es insuficiente');
            }

            // Crear factura
            $invoice = $this->createInvoice($orders, $paymentData);

            // Procesar pago
            $payment = $this->createPayment($invoice, $paymentData);

            // Marcar pedidos como pagados
            $orders->each(function ($order) {
                $order->update(['status' => 'completed']);
            });

            // Liberar mesas
            $this->freeTables($orders);

            $this->logActivity('payment_processed', 'billing', [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid
            ]);

            return [
                'success' => true,
                'invoice' => $invoice->load(['orders.items.product', 'payment']),
                'change' => $amountPaid - $totalAmount,
                'payment' => $payment
            ];
        });
    }

    /**
     * Crear factura
     */
    private function createInvoice(Collection $orders, array $paymentData): Invoice
    {
        $subtotal = $orders->sum('subtotal');
        $tax = $orders->sum('tax');
        $total = $orders->sum('total');

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'customer_name' => $paymentData['customer_name'] ?? 'Cliente General',
            'customer_document' => $paymentData['customer_document'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
            'cashier_id' => Auth::id(),
            'table_numbers' => $orders->pluck('table.number')->join(', ')
        ]);
    }

    /**
     * Crear registro de pago
     */
    private function createPayment(Invoice $invoice, array $paymentData): Payment
    {
        return Payment::create([
            'invoice_id' => $invoice->id,
            'payment_method_id' => $paymentData['payment_method_id'],
            'amount' => $paymentData['amount_paid'],
            'reference' => $paymentData['reference'] ?? null,
            'processed_by' => Auth::id(),
            'processed_at' => now()
        ]);
    }

    /**
     * Liberar mesas después del pago
     */
    private function freeTables(Collection $orders): void
    {
        $tableIds = $orders->pluck('table_id')->unique();

        Table::whereIn('id', $tableIds)->update([
            'status' => 'available',
            'cleaned_at' => null
        ]);
    }

    /**
     * Generar número de factura único
     */
    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = Invoice::whereDate('created_at', now()->toDateString())->count() + 1;

        return sprintf('%s-%04d', $date, $sequence);
    }

    /**
     * Obtener resumen de ventas diario
     */
    public function getDailySalesReport(string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        $invoices = Invoice::whereDate('created_at', $date)
                          ->with(['orders.items.product', 'payment.paymentMethod'])
                          ->get();

        $paymentMethods = PaymentMethod::all();
        $paymentBreakdown = [];

        foreach ($paymentMethods as $method) {
            $total = $invoices->filter(function ($invoice) use ($method) {
                return $invoice->payment && $invoice->payment->payment_method_id === $method->id;
            })->sum('total');

            $paymentBreakdown[] = [
                'method' => $method->name,
                'total' => $total,
                'count' => $invoices->where('payment.payment_method_id', $method->id)->count()
            ];
        }

        return [
            'date' => $date,
            'total_sales' => $invoices->sum('total'),
            'total_invoices' => $invoices->count(),
            'average_ticket' => $invoices->avg('total'),
            'payment_breakdown' => $paymentBreakdown,
            'hourly_sales' => $this->getHourlySales($invoices),
            'top_products' => $this->getTopProducts($invoices),
            'tax_collected' => $invoices->sum('tax')
        ];
    }

    /**
     * Obtener ventas por hora
     */
    private function getHourlySales(Collection $invoices): array
    {
        return $invoices->groupBy(function ($invoice) {
            return $invoice->created_at->format('H:00');
        })->map(function ($group) {
            return [
                'sales' => $group->sum('total'),
                'invoices' => $group->count()
            ];
        })->toArray();
    }

    /**
     * Obtener productos más vendidos del día
     */
    private function getTopProducts(Collection $invoices): array
    {
        $products = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->orders as $order) {
                foreach ($order->items as $item) {
                    $key = $item->product_id;

                    if (!isset($products[$key])) {
                        $products[$key] = [
                            'name' => $item->product->name,
                            'quantity' => 0,
                            'revenue' => 0
                        ];
                    }

                    $products[$key]['quantity'] += $item->quantity;
                    $products[$key]['revenue'] += $item->total;
                }
            }
        }

        // Ordenar por cantidad vendida
        uasort($products, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });

        return array_slice($products, 0, 10, true);
    }

    /**
     * Obtener resumen mensual
     */
    public function getMonthlySalesReport(int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])
                          ->with(['orders.items.product', 'payment.paymentMethod'])
                          ->get();

        $dailySales = [];
        for ($day = 1; $day <= date('t', strtotime($startDate)); $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $daySales = $invoices->filter(function ($invoice) use ($date) {
                return $invoice->created_at->toDateString() === $date;
            });

            $dailySales[$day] = [
                'date' => $date,
                'sales' => $daySales->sum('total'),
                'invoices' => $daySales->count()
            ];
        }

        return [
            'year' => $year,
            'month' => $month,
            'total_sales' => $invoices->sum('total'),
            'total_invoices' => $invoices->count(),
            'daily_sales' => $dailySales,
            'top_products' => $this->getTopProducts($invoices),
            'average_daily_sales' => $invoices->sum('total') / date('t', strtotime($startDate)),
            'tax_collected' => $invoices->sum('tax')
        ];
    }

    /**
     * Obtener estadísticas de empleados (cajeros)
     */
    public function getCashierStatistics(array $filters = []): array
    {
        $query = Invoice::with(['cashier']);

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $invoices = $query->get();

        $cashierStats = $invoices->groupBy('cashier_id')->map(function ($invoices, $cashierId) {
            $cashier = $invoices->first()->cashier;

            return [
                'cashier_name' => $cashier ? $cashier->name : 'Desconocido',
                'total_sales' => $invoices->sum('total'),
                'total_invoices' => $invoices->count(),
                'average_ticket' => $invoices->avg('total'),
                'performance_score' => $this->calculateCashierScore($invoices)
            ];
        })->sortByDesc('total_sales');

        return [
            'cashier_statistics' => $cashierStats->values()->toArray(),
            'period_total' => $invoices->sum('total'),
            'top_performer' => $cashierStats->first()
        ];
    }

    /**
     * Calcular puntuación de rendimiento del cajero
     */
    private function calculateCashierScore(Collection $invoices): float
    {
        $totalSales = $invoices->sum('total');
        $totalInvoices = $invoices->count();
        $averageTicket = $totalInvoices > 0 ? $totalSales / $totalInvoices : 0;

        // Puntuación basada en ventas totales (70%) y ticket promedio (30%)
        $salesScore = min(($totalSales / 1000000) * 70, 70); // Máximo 70 puntos
        $ticketScore = min(($averageTicket / 50000) * 30, 30); // Máximo 30 puntos

        return round($salesScore + $ticketScore, 2);
    }

    /**
     * Anular factura
     */
    public function voidInvoice(int $invoiceId, string $reason): array
    {
        return $this->executeTransaction(function () use ($invoiceId, $reason) {
            $invoice = Invoice::findOrFail($invoiceId);

            if ($invoice->status === 'voided') {
                throw new Exception('La factura ya está anulada');
            }

            // Verificar si se puede anular (mismo día)
            if (!$invoice->created_at->isToday()) {
                throw new Exception('Solo se pueden anular facturas del mismo día');
            }

            $invoice->update([
                'status' => 'voided',
                'void_reason' => $reason,
                'voided_by' => Auth::id(),
                'voided_at' => now()
            ]);

            $this->logActivity('invoice_voided', 'billing', [
                'invoice_id' => $invoiceId,
                'reason' => $reason,
                'voided_by' => Auth::id()
            ]);

            return [
                'success' => true,
                'message' => 'Factura anulada correctamente',
                'invoice' => $invoice->fresh()
            ];
        });
    }
}
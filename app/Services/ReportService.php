<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Obtener resumen de ventas del día
     */
    public function getDailySalesReport(string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();

        $orders = Order::whereDate('created_at', $targetDate)
            ->where('status_id', 4) // Solo órdenes pagadas
            ->with(['items.product', 'payment', 'table'])
            ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Productos más vendidos
        $topProducts = $this->getTopSellingProducts($orders);

        // Ventas por método de pago
        $paymentMethods = $this->getSalesByPaymentMethod($orders);

        // Ventas por hora
        $hourlySales = $this->getHourlySales($orders);

        return [
            'date' => $targetDate->toDateString(),
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'top_products' => $topProducts,
            'payment_methods' => $paymentMethods,
            'hourly_sales' => $hourlySales,
            'orders_summary' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'table' => $order->table->name ?? 'N/A',
                    'total' => $order->total,
                    'time' => $order->created_at->format('H:i'),
                    'items_count' => $order->items->count()
                ];
            })
        ];
    }

    /**
     * Obtener resumen de ventas semanal
     */
    public function getWeeklySalesReport(string $startDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfWeek();
        $end = $start->copy()->endOfWeek();

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status_id', 4)
            ->with(['items.product', 'payment'])
            ->get();

        $dailySales = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $dayOrders = $orders->filter(function ($order) use ($date) {
                return $order->created_at->toDateString() === $date->toDateString();
            });

            $dailySales[] = [
                'date' => $date->toDateString(),
                'day_name' => $date->locale('es')->dayName,
                'total_sales' => $dayOrders->sum('total'),
                'orders_count' => $dayOrders->count()
            ];
        }

        return [
            'week_start' => $start->toDateString(),
            'week_end' => $end->toDateString(),
            'total_sales' => $orders->sum('total'),
            'total_orders' => $orders->count(),
            'daily_breakdown' => $dailySales,
            'top_products_week' => $this->getTopSellingProducts($orders, 10)
        ];
    }

    /**
     * Obtener productos más vendidos
     */
    private function getTopSellingProducts(Collection $orders, int $limit = 5): Collection
    {
        return $orders->flatMap(function ($order) {
            return $order->items;
        })->groupBy('product_id')->map(function ($items) {
            $product = $items->first()->product;
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $product->category->name ?? 'Sin categoría',
                'quantity_sold' => $items->sum('quantity'),
                'total_revenue' => $items->sum('total_price'),
                'average_price' => $items->avg('unit_price')
            ];
        })->sortByDesc('quantity_sold')->take($limit)->values();
    }

    /**
     * Obtener ventas por método de pago
     */
    private function getSalesByPaymentMethod(Collection $orders): Collection
    {
        return $orders->groupBy(function ($order) {
            return $order->payment->payment_method->name ?? 'Sin especificar';
        })->map(function ($orders, $method) {
            return [
                'method' => $method,
                'total_sales' => $orders->sum('total'),
                'orders_count' => $orders->count(),
                'percentage' => 0 // Se calculará después
            ];
        })->values();
    }

    /**
     * Obtener ventas por hora
     */
    private function getHourlySales(Collection $orders): Collection
    {
        return $orders->groupBy(function ($order) {
            return $order->created_at->format('H:00');
        })->map(function ($orders, $hour) {
            return [
                'hour' => $hour,
                'total_sales' => $orders->sum('total'),
                'orders_count' => $orders->count()
            ];
        })->sortBy('hour')->values();
    }

    /**
     * Obtener inventario de productos
     */
    public function getProductInventoryReport(): array
    {
        $products = Product::with(['category'])
            ->where('active', true)
            ->get();

        $lowStock = $products->filter(function ($product) {
            return isset($product->stock) && $product->stock < 10;
        });

        $outOfStock = $products->filter(function ($product) {
            return !$product->is_available;
        });

        return [
            'total_products' => $products->count(),
            'active_products' => $products->where('is_available', true)->count(),
            'inactive_products' => $products->where('is_available', false)->count(),
            'low_stock_products' => $lowStock->count(),
            'out_of_stock_products' => $outOfStock->count(),
            'products_by_category' => $products->groupBy('category.name')->map(function ($products) {
                return $products->count();
            }),
            'low_stock_details' => $lowStock->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Sin categoría',
                    'stock' => $product->stock ?? 0,
                    'price' => $product->price
                ];
            })->values()
        ];
    }

    /**
     * Obtener reporte de mesas más utilizadas
     */
    public function getTableUsageReport(string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();

        $orders = Order::whereDate('created_at', $targetDate)
            ->with(['table'])
            ->get();

        $tableUsage = $orders->groupBy('table_id')->map(function ($orders, $tableId) {
            $table = $orders->first()->table;
            return [
                'table_id' => $tableId,
                'table_name' => $table->name ?? "Mesa {$tableId}",
                'orders_count' => $orders->count(),
                'total_revenue' => $orders->where('status_id', 4)->sum('total'),
                'average_order_value' => $orders->where('status_id', 4)->avg('total') ?? 0
            ];
        })->sortByDesc('orders_count')->values();

        return [
            'date' => $targetDate->toDateString(),
            'table_usage' => $tableUsage,
            'most_used_table' => $tableUsage->first(),
            'total_table_orders' => $orders->count()
        ];
    }
}

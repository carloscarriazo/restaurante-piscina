<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Table;
use App\Services\ReportCacheService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app')]
class ReportDashboard extends Component
{
    // Servicio de caché
    protected $cacheService;

    // Filtros de fecha
    public $dateFilter = 'today'; // today, week, month, custom
    public $startDate;
    public $endDate;
    public $selectedMonth;
    public $selectedYear;

    // Pestañas
    public $activeTab = 'overview'; // overview, products, invoices, services

    public function mount()
    {
        $this->cacheService = new ReportCacheService();
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function updatedDateFilter()
    {
        switch ($this->dateFilter) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            default:
                // Para 'custom' no hacemos nada, las fechas se mantienen
                break;
        }
    }

    public function getOverviewDataProperty()
    {
        $cacheKey = $this->cacheService->generateKey('overview', $this->startDate, $this->endDate);

        return $this->cacheService->remember($cacheKey, function() {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();

            // Total de ventas
            $totalSales = Order::whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['completed', 'paid'])
                ->sum('total');

            // Total de órdenes
            $totalOrders = Order::whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['completed', 'paid'])
                ->count();

            // Promedio por orden
            $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

            // Total de facturas
            $totalInvoices = Invoice::whereBetween('created_at', [$start, $end])
                ->count();

            // Ingresos por facturas
            $invoiceRevenue = Invoice::whereBetween('created_at', [$start, $end])
                ->where('status', 'paid')
                ->sum('total');

            // Órdenes pendientes
            $pendingOrders = Order::where('status', 'pending')->count();

            // Mesas ocupadas
            $occupiedTables = Table::where('status', 'occupied')->count();

            // Ventas por día (últimos 7 días)
            $salesByDay = Order::whereBetween('created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()])
                ->whereIn('status', ['completed', 'paid'])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return [
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_order' => $averageOrder,
                'total_invoices' => $totalInvoices,
                'invoice_revenue' => $invoiceRevenue,
                'pending_orders' => $pendingOrders,
                'occupied_tables' => $occupiedTables,
                'sales_by_day' => $salesByDay,
            ];
        }, $this->dateFilter);
    }

    public function getTopProductsProperty()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereIn('orders.status', ['completed', 'paid'])
            ->select(
                'products.id',
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    public function getDailyInvoicesProperty()
    {
        $today = Carbon::today();

        return Invoice::whereDate('created_at', $today)
            ->with(['order.table', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMonthlyInvoicesProperty()
    {
        return Invoice::whereMonth('created_at', $this->selectedMonth)
            ->whereYear('created_at', $this->selectedYear)
            ->with(['order.table', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMonthlyRevenueProperty()
    {
        return Invoice::whereMonth('created_at', $this->selectedMonth)
            ->whereYear('created_at', $this->selectedYear)
            ->where('status', 'paid')
            ->sum('total');
    }

    public function getDailyRevenueProperty()
    {
        return Invoice::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->sum('total');
    }

    public function getServicesDataProperty()
    {
        // Total de productos activos
        $activeProducts = Product::where('active', true)->count();

        // Total de categorías
        $totalCategories = DB::table('categories')->count();

        // Total de mesas
        $totalTables = Table::count();

        // Mesas disponibles
        $availableTables = Table::where('status', 'available')->count();

        // Productos más populares (todos los tiempos)
        $popularProducts = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'active_products' => $activeProducts,
            'total_categories' => $totalCategories,
            'total_tables' => $totalTables,
            'available_tables' => $availableTables,
            'popular_products' => $popularProducts,
        ];
    }

    public function exportReport()
    {
        $this->dispatch('show-notification', [
            'type' => 'info',
            'message' => 'Exportación de reportes próximamente disponible'
        ]);
    }

    public function printReport()
    {
        $this->dispatch('print-report');
    }

    public function render()
    {
        return view('livewire.report-dashboard', [
            'overviewData' => $this->overviewData,
            'topProducts' => $this->topProducts,
            'dailyInvoices' => $this->dailyInvoices,
            'monthlyInvoices' => $this->monthlyInvoices,
            'monthlyRevenue' => $this->monthlyRevenue,
            'dailyRevenue' => $this->dailyRevenue,
            'servicesData' => $this->servicesData,
        ]);
    }
}

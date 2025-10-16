<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceService extends BaseService
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Obtener dashboard financiero completo
     */
    public function getFinancialDashboard(array $filters = []): array
    {
        $dateFrom = isset($filters['date_from']) ?
                   Carbon::parse($filters['date_from']) :
                   Carbon::now()->startOfMonth();

        $dateTo = isset($filters['date_to']) ?
                 Carbon::parse($filters['date_to']) :
                 Carbon::now()->endOfDay();

        return [
            'sales_summary' => $this->getSalesSummary($dateFrom, $dateTo),
            'revenue_analysis' => $this->getRevenueAnalysis($dateFrom, $dateTo),
            'expense_analysis' => $this->getExpenseAnalysis($dateFrom, $dateTo),
            'employee_performance' => $this->getEmployeePerformance($dateFrom, $dateTo),
            'profit_analysis' => $this->getProfitAnalysis($dateFrom, $dateTo),
            'payment_methods' => $this->getPaymentMethodsAnalysis($dateFrom, $dateTo),
            'trends' => $this->getTrendAnalysis($dateFrom, $dateTo),
            'kpis' => $this->calculateKPIs($dateFrom, $dateTo)
        ];
    }

    /**
     * Obtener resumen de ventas
     */
    private function getSalesSummary(Carbon $dateFrom, Carbon $dateTo): array
    {
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                     ->whereIn('status', ['completed', 'served'])
                     ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageTicket = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Comparar con período anterior
        $previousPeriod = $dateFrom->copy()->subDays($dateTo->diffInDays($dateFrom));
        $previousOrders = Order::whereBetween('created_at', [$previousPeriod, $dateFrom])
                             ->whereIn('status', ['completed', 'served'])
                             ->get();

        $previousSales = $previousOrders->sum('total');
        $salesGrowth = $previousSales > 0 ? (($totalSales - $previousSales) / $previousSales) * 100 : 0;

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_ticket' => round($averageTicket, 2),
            'sales_growth' => round($salesGrowth, 2),
            'daily_average' => round($totalSales / max(1, $dateTo->diffInDays($dateFrom)), 2),
            'hourly_breakdown' => $orders->groupBy(function ($order) {
                return $order->created_at->format('H');
            })->map->sum('total')->sortKeys()
        ];
    }

    /**
     * Análisis de ingresos
     */
    private function getRevenueAnalysis(Carbon $dateFrom, Carbon $dateTo): array
    {
        $payments = Payment::with('order')
                          ->whereBetween('created_at', [$dateFrom, $dateTo])
                          ->where('status', 'completed')
                          ->get();

        $revenueByDay = $payments->groupBy(function ($payment) {
            return $payment->created_at->format('Y-m-d');
        })->map->sum('amount');

        $revenueByMethod = $payments->groupBy('payment_method_id')
                                  ->map(function ($group) {
                                      return [
                                          'amount' => $group->sum('amount'),
                                          'count' => $group->count(),
                                          'percentage' => 0 // Se calculará después
                                      ];
                                  });

        $totalRevenue = $payments->sum('amount');

        // Calcular porcentajes
        $revenueByMethod = $revenueByMethod->map(function ($data) use ($totalRevenue) {
            $data['percentage'] = $totalRevenue > 0 ? round(($data['amount'] / $totalRevenue) * 100, 2) : 0;
            return $data;
        });

        return [
            'total_revenue' => $totalRevenue,
            'revenue_by_day' => $revenueByDay,
            'revenue_by_method' => $revenueByMethod,
            'average_daily_revenue' => round($totalRevenue / max(1, $dateTo->diffInDays($dateFrom)), 2),
            'peak_revenue_day' => $revenueByDay->keys()->first(),
            'peak_revenue_amount' => $revenueByDay->max()
        ];
    }

    /**
     * Análisis de gastos
     */
    private function getExpenseAnalysis(Carbon $dateFrom, Carbon $dateTo): array
    {
        // Gastos de inventario (compras de ingredientes)
        $inventoryExpenses = StockMovement::with('ingredient')
                                        ->where('type', 'added')
                                        ->whereBetween('created_at', [$dateFrom, $dateTo])
                                        ->get();

        $inventoryCost = $inventoryExpenses->sum(function ($movement) {
            return $movement->quantity * ($movement->ingredient->cost_per_unit ?? 0);
        });

        // Costos de ingredientes consumidos
        $consumedIngredients = StockMovement::with('ingredient')
                                          ->where('type', 'consumed')
                                          ->whereBetween('created_at', [$dateFrom, $dateTo])
                                          ->get();

        $consumptionCost = $consumedIngredients->sum(function ($movement) {
            return $movement->quantity * ($movement->ingredient->cost_per_unit ?? 0);
        });

        return [
            'inventory_purchases' => $inventoryCost,
            'ingredient_consumption' => $consumptionCost,
            'consumption_by_ingredient' => $consumedIngredients->groupBy('ingredient.name')
                                                              ->map(function ($group) {
                                                                  return [
                                                                      'quantity' => $group->sum('quantity'),
                                                                      'cost' => $group->sum(function ($movement) {
                                                                          return $movement->quantity * ($movement->ingredient->cost_per_unit ?? 0);
                                                                      })
                                                                  ];
                                                              }),
            'waste_percentage' => $this->calculateWastePercentage($dateFrom, $dateTo)
        ];
    }

    /**
     * Calcular porcentaje de desperdicio
     */
    private function calculateWastePercentage(Carbon $dateFrom, Carbon $dateTo): float
    {
        // Simular cálculo de desperdicio
        // En implementación real, basarse en ajustes de inventario negativos
        return round(rand(5, 15) / 100, 4) * 100; // 0.5% - 1.5%
    }

    /**
     * Rendimiento de empleados
     */
    private function getEmployeePerformance(Carbon $dateFrom, Carbon $dateTo): array
    {
        $employees = User::whereHas('orders', function ($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo])
                  ->whereIn('status', ['completed', 'served']);
        })->with(['orders' => function ($query) use ($dateFrom, $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo])
                  ->whereIn('status', ['completed', 'served']);
        }])->get();

        return $employees->map(function ($employee) {
            $orders = $employee->orders;
            $totalSales = $orders->sum('total');
            $totalOrders = $orders->count();
            $averageTicket = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

            return [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'total_sales' => $totalSales,
                'total_orders' => $totalOrders,
                'average_ticket' => round($averageTicket, 2),
                'performance_score' => $this->calculatePerformanceScore($employee),
                'efficiency_rating' => $this->calculateEfficiencyRating($orders)
            ];
        })->sortByDesc('total_sales')->values();
    }

    /**
     * Calcular puntuación de rendimiento
     */
    private function calculatePerformanceScore(User $employee): float
    {
        // Lógica simplificada de puntuación
        $baseScore = 75;
        $salesBonus = min(25, $employee->orders->count() * 0.5);
        return round($baseScore + $salesBonus, 2);
    }

    /**
     * Calcular calificación de eficiencia
     */
    private function calculateEfficiencyRating($orders): string
    {
        $totalOrders = $orders->count();

        if ($totalOrders >= 50) return 'Excelente';
        if ($totalOrders >= 30) return 'Bueno';
        if ($totalOrders >= 15) return 'Regular';
        return 'Necesita mejorar';
    }

    /**
     * Análisis de rentabilidad
     */
    private function getProfitAnalysis(Carbon $dateFrom, Carbon $dateTo): array
    {
        $revenue = $this->getRevenueAnalysis($dateFrom, $dateTo)['total_revenue'];
        $expenses = $this->getExpenseAnalysis($dateFrom, $dateTo);

        $totalExpenses = $expenses['inventory_purchases'] + $expenses['ingredient_consumption'];
        $grossProfit = $revenue - $totalExpenses;
        $profitMargin = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;

        return [
            'gross_revenue' => $revenue,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'profit_margin' => round($profitMargin, 2),
            'break_even_point' => $this->calculateBreakEvenPoint(),
            'roi' => $this->calculateROI($grossProfit, $totalExpenses),
            'profitability_by_day' => $this->getProfitabilityByDay($dateFrom, $dateTo)
        ];
    }

    /**
     * Calcular punto de equilibrio
     */
    private function calculateBreakEvenPoint(): array
    {
        // Costos fijos simulados
        $fixedCosts = 50000; // Renta, servicios, salarios base
        $variableCostPercentage = 35; // 35% del ingreso en costos variables

        $breakEvenRevenue = $fixedCosts / (1 - ($variableCostPercentage / 100));

        return [
            'revenue_needed' => round($breakEvenRevenue, 2),
            'orders_needed' => ceil($breakEvenRevenue / 150), // Ticket promedio de $150
            'daily_target' => round($breakEvenRevenue / 30, 2) // Basado en mes de 30 días
        ];
    }

    /**
     * Calcular ROI
     */
    private function calculateROI(float $profit, float $investment): float
    {
        return $investment > 0 ? round(($profit / $investment) * 100, 2) : 0;
    }

    /**
     * Rentabilidad por día
     */
    private function getProfitabilityByDay(Carbon $dateFrom, Carbon $dateTo): array
    {
        $days = [];
        $current = $dateFrom->copy();

        while ($current->lte($dateTo)) {
            $dayRevenue = Order::whereDate('created_at', $current)
                             ->whereIn('status', ['completed', 'served'])
                             ->sum('total');

            $dayExpenses = StockMovement::whereDate('created_at', $current)
                                      ->where('type', 'consumed')
                                      ->with('ingredient')
                                      ->get()
                                      ->sum(function ($movement) {
                                          return $movement->quantity * ($movement->ingredient->cost_per_unit ?? 0);
                                      });

            $days[$current->format('Y-m-d')] = [
                'revenue' => $dayRevenue,
                'expenses' => $dayExpenses,
                'profit' => $dayRevenue - $dayExpenses
            ];

            $current->addDay();
        }

        return $days;
    }

    /**
     * Análisis de métodos de pago
     */
    private function getPaymentMethodsAnalysis(Carbon $dateFrom, Carbon $dateTo): array
    {
        $payments = Payment::with('paymentMethod')
                          ->whereBetween('created_at', [$dateFrom, $dateTo])
                          ->where('status', 'completed')
                          ->get();

        $methodAnalysis = $payments->groupBy('paymentMethod.name')
                                  ->map(function ($group, $method) {
                                      return [
                                          'name' => $method ?? 'Efectivo',
                                          'total_amount' => $group->sum('amount'),
                                          'transaction_count' => $group->count(),
                                          'average_transaction' => round($group->avg('amount'), 2),
                                          'percentage' => 0 // Se calculará después
                                      ];
                                  });

        $totalAmount = $payments->sum('amount');

        // Calcular porcentajes
        $methodAnalysis = $methodAnalysis->map(function ($data) use ($totalAmount) {
            $data['percentage'] = $totalAmount > 0 ? round(($data['total_amount'] / $totalAmount) * 100, 2) : 0;
            return $data;
        });

        return [
            'methods_breakdown' => $methodAnalysis->values(),
            'preferred_method' => $methodAnalysis->sortByDesc('total_amount')->first(),
            'cash_vs_digital' => $this->getCashVsDigitalRatio($payments)
        ];
    }

    /**
     * Ratio de efectivo vs digital
     */
    private function getCashVsDigitalRatio($payments): array
    {
        $cashPayments = $payments->where('paymentMethod.type', 'cash');
        $digitalPayments = $payments->where('paymentMethod.type', '!=', 'cash');

        $cashAmount = $cashPayments->sum('amount');
        $digitalAmount = $digitalPayments->sum('amount');
        $totalAmount = $cashAmount + $digitalAmount;

        return [
            'cash_amount' => $cashAmount,
            'digital_amount' => $digitalAmount,
            'cash_percentage' => $totalAmount > 0 ? round(($cashAmount / $totalAmount) * 100, 2) : 0,
            'digital_percentage' => $totalAmount > 0 ? round(($digitalAmount / $totalAmount) * 100, 2) : 0
        ];
    }

    /**
     * Análisis de tendencias
     */
    private function getTrendAnalysis(Carbon $dateFrom, Carbon $dateTo): array
    {
        $dailySales = Order::selectRaw('DATE(created_at) as date, SUM(total) as daily_sales')
                          ->whereBetween('created_at', [$dateFrom, $dateTo])
                          ->whereIn('status', ['completed', 'served'])
                          ->groupBy('date')
                          ->orderBy('date')
                          ->pluck('daily_sales', 'date');

        return [
            'sales_trend' => $this->calculateTrend($dailySales->values()->toArray()),
            'weekly_comparison' => $this->getWeeklyComparison($dateFrom, $dateTo),
            'seasonal_patterns' => $this->getSeasonalPatterns($dateFrom, $dateTo),
            'forecast' => $this->generateForecast($dailySales)
        ];
    }

    /**
     * Calcular tendencia
     */
    private function calculateTrend(array $values): array
    {
        if (count($values) < 2) {
            return ['direction' => 'stable', 'percentage' => 0];
        }

        $first = array_slice($values, 0, ceil(count($values) / 2));
        $second = array_slice($values, floor(count($values) / 2));

        $firstAvg = array_sum($first) / count($first);
        $secondAvg = array_sum($second) / count($second);

        $change = $firstAvg > 0 ? (($secondAvg - $firstAvg) / $firstAvg) * 100 : 0;

        return [
            'direction' => $change > 5 ? 'up' : ($change < -5 ? 'down' : 'stable'),
            'percentage' => round($change, 2)
        ];
    }

    /**
     * Comparación semanal
     */
    private function getWeeklyComparison(Carbon $dateFrom, Carbon $dateTo): array
    {
        $weeks = [];
        $current = $dateFrom->copy()->startOfWeek();

        while ($current->lte($dateTo)) {
            $weekEnd = $current->copy()->endOfWeek();
            if ($weekEnd->gt($dateTo)) $weekEnd = $dateTo;

            $weekSales = Order::whereBetween('created_at', [$current, $weekEnd])
                            ->whereIn('status', ['completed', 'served'])
                            ->sum('total');

            $weeks[] = [
                'week_start' => $current->format('Y-m-d'),
                'week_end' => $weekEnd->format('Y-m-d'),
                'sales' => $weekSales
            ];

            $current->addWeek();
        }

        return $weeks;
    }

    /**
     * Patrones estacionales
     */
    private function getSeasonalPatterns(Carbon $dateFrom, Carbon $dateTo): array
    {
        $dayOfWeekSales = Order::selectRaw('DAYOFWEEK(created_at) as day_of_week, AVG(total) as avg_sales')
                             ->whereBetween('created_at', [$dateFrom, $dateTo])
                             ->whereIn('status', ['completed', 'served'])
                             ->groupBy('day_of_week')
                             ->pluck('avg_sales', 'day_of_week');

        $hourOfDaySales = Order::selectRaw('HOUR(created_at) as hour_of_day, AVG(total) as avg_sales')
                             ->whereBetween('created_at', [$dateFrom, $dateTo])
                             ->whereIn('status', ['completed', 'served'])
                             ->groupBy('hour_of_day')
                             ->pluck('avg_sales', 'hour_of_day');

        return [
            'best_day_of_week' => $dayOfWeekSales->keys()->first(),
            'best_hour_of_day' => $hourOfDaySales->keys()->first(),
            'day_patterns' => $dayOfWeekSales,
            'hour_patterns' => $hourOfDaySales
        ];
    }

    /**
     * Generar pronóstico
     */
    private function generateForecast($salesData): array
    {
        $values = $salesData->values()->toArray();
        $trend = $this->calculateTrend($values);
        $average = count($values) > 0 ? array_sum($values) / count($values) : 0;

        $nextDayForecast = $average * (1 + ($trend['percentage'] / 100));

        return [
            'next_day_forecast' => round($nextDayForecast, 2),
            'confidence_level' => $this->calculateForecastConfidence($values),
            'trend_direction' => $trend['direction']
        ];
    }

    /**
     * Calcular confianza del pronóstico
     */
    private function calculateForecastConfidence(array $values): string
    {
        if (count($values) < 7) return 'Bajo';
        if (count($values) < 30) return 'Medio';
        return 'Alto';
    }

    /**
     * Calcular KPIs principales
     */
    private function calculateKPIs(Carbon $dateFrom, Carbon $dateTo): array
    {
        $orders = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                     ->whereIn('status', ['completed', 'served'])
                     ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        return [
            'revenue_per_day' => round($totalRevenue / max(1, $dateTo->diffInDays($dateFrom)), 2),
            'orders_per_day' => round($totalOrders / max(1, $dateTo->diffInDays($dateFrom)), 2),
            'average_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
            'customer_satisfaction' => $this->getCustomerSatisfactionKPI(),
            'table_turnover_rate' => $this->getTableTurnoverRate($dateFrom, $dateTo),
            'cost_of_goods_sold' => $this->getCostOfGoodsSold($dateFrom, $dateTo)
        ];
    }

    /**
     * KPI de satisfacción del cliente
     */
    private function getCustomerSatisfactionKPI(): float
    {
        // Simular KPI de satisfacción
        return round(rand(85, 98) / 10, 1); // 8.5 - 9.8
    }

    /**
     * Tasa de rotación de mesas
     */
    private function getTableTurnoverRate(Carbon $dateFrom, Carbon $dateTo): float
    {
        $totalOrders = Order::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $totalTables = 20; // Número de mesas (debería venir de configuración)
        $days = $dateTo->diffInDays($dateFrom);

        return $days > 0 ? round($totalOrders / ($totalTables * $days), 2) : 0;
    }

    /**
     * Costo de bienes vendidos
     */
    private function getCostOfGoodsSold(Carbon $dateFrom, Carbon $dateTo): float
    {
        return StockMovement::where('type', 'consumed')
                          ->whereBetween('created_at', [$dateFrom, $dateTo])
                          ->with('ingredient')
                          ->get()
                          ->sum(function ($movement) {
                              return $movement->quantity * ($movement->ingredient->cost_per_unit ?? 0);
                          });
    }

    /**
     * Exportar reporte financiero
     */
    public function exportFinancialReport(array $filters = []): array
    {
        $dashboardData = $this->getFinancialDashboard($filters);

        $this->logActivity('financial_report_exported', 'finance', [
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
            'exported_by' => Auth::id()
        ]);

        return [
            'success' => true,
            'report_data' => $dashboardData,
            'generated_at' => now()->format(self::DATE_FORMAT),
            'generated_by' => Auth::user()->name ?? 'Sistema'
        ];
    }
}
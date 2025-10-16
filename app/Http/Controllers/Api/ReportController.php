<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Constants\ValidationRules;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Obtener reporte de ventas diarias
     */
    public function dailySales(Request $request)
    {
        $request->validate([
            'date' => ValidationRules::DATE_FORMAT_YMD
        ]);

        try {
            $report = $this->reportService->getDailySalesReport($request->date);

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte diario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reporte de ventas semanales
     */
    public function weeklySales(Request $request)
    {
        $request->validate([
            'start_date' => ValidationRules::DATE_FORMAT_YMD
        ]);

        try {
            $report = $this->reportService->getWeeklySalesReport($request->start_date);

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte semanal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reporte de inventario
     */
    public function inventory()
    {
        try {
            $report = $this->reportService->getProductInventoryReport();

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte de inventario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reporte de uso de mesas
     */
    public function tableUsage(Request $request)
    {
        $request->validate([
            'date' => ValidationRules::DATE_FORMAT_YMD
        ]);

        try {
            $report = $this->reportService->getTableUsageReport($request->date);

            return response()->json([
                'success' => true,
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar reporte de mesas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard con métricas generales
     */
    public function dashboard()
    {
        try {
            $todayReport = $this->reportService->getDailySalesReport();
            $weekReport = $this->reportService->getWeeklySalesReport();
            $inventoryReport = $this->reportService->getProductInventoryReport();

            return response()->json([
                'success' => true,
                'data' => [
                    'today' => [
                        'total_sales' => $todayReport['total_sales'],
                        'total_orders' => $todayReport['total_orders'],
                        'average_order' => $todayReport['average_order_value']
                    ],
                    'week' => [
                        'total_sales' => $weekReport['total_sales'],
                        'total_orders' => $weekReport['total_orders']
                    ],
                    'inventory' => [
                        'total_products' => $inventoryReport['total_products'],
                        'active_products' => $inventoryReport['active_products'],
                        'low_stock' => $inventoryReport['low_stock_products']
                    ],
                    'top_products_today' => $todayReport['top_products']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}

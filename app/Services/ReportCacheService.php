<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * ReportCacheService
 *
 * Servicio para cachear queries pesadas de reportes y mejorar performance.
 * Implementa patrón Repository con caché estratégico.
 *
 * TTL (Time To Live):
 * - Reportes de hoy: 5 minutos (datos cambian frecuentemente)
 * - Reportes de semana: 15 minutos
 * - Reportes de mes: 30 minutos
 * - Reportes históricos: 1 hora
 */
class ReportCacheService
{
    /**
     * Caché con TTL dinámico basado en el tipo de reporte
     *
     * @param string $key Clave única del caché
     * @param callable $callback Función que retorna los datos
     * @param string $dateFilter Filtro de fecha (today, week, month, custom)
     * @return mixed
     */
    public function remember(string $key, callable $callback, string $dateFilter = 'today')
    {
        $ttl = $this->getTTL($dateFilter);

        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Obtiene el TTL apropiado según el tipo de reporte
     *
     * @param string $dateFilter
     * @return int Segundos de TTL
     */
    private function getTTL(string $dateFilter): int
    {
        return match($dateFilter) {
            'today' => 300,      // 5 minutos
            'week' => 900,       // 15 minutos
            'month' => 1800,     // 30 minutos
            'custom' => 3600,    // 1 hora
            default => 600       // 10 minutos por defecto
        };
    }

    /**
     * Genera una clave de caché única basada en parámetros
     *
     * @param string $prefix Prefijo del caché (overview, products, etc.)
     * @param string $startDate Fecha de inicio
     * @param string $endDate Fecha de fin
     * @return string
     */
    public function generateKey(string $prefix, string $startDate, string $endDate): string
    {
        return sprintf(
            'report_%s_%s_%s',
            $prefix,
            Carbon::parse($startDate)->format('Ymd'),
            Carbon::parse($endDate)->format('Ymd')
        );
    }

    /**
     * Limpia toda la caché de reportes
     * Útil cuando hay cambios importantes en los datos
     *
     * @return void
     */
    public function flush(): void
    {
        Cache::tags(['reports'])->flush();
    }

    /**
     * Limpia caché de un tipo específico de reporte
     *
     * @param string $type Tipo de reporte (overview, products, invoices, services)
     * @return void
     */
    public function flushByType(string $type): void
    {
        $patterns = [
            'overview' => 'report_overview_*',
            'products' => 'report_products_*',
            'invoices' => 'report_invoices_*',
            'services' => 'report_services_*',
        ];

        if (isset($patterns[$type])) {
            // En producción usar Redis pattern matching
            // Por ahora limpiamos toda la caché de reportes
            $this->flush();
        }
    }

    /**
     * Invalida caché de reportes del día actual
     * Se ejecuta cuando se crea/modifica una orden
     *
     * @return void
     */
    public function invalidateToday(): void
    {
        $today = Carbon::today()->format('Ymd');
        Cache::forget("report_overview_{$today}_{$today}");
        Cache::forget("report_products_{$today}_{$today}");
        Cache::forget("report_invoices_{$today}_{$today}");
        Cache::forget("report_services_{$today}_{$today}");
    }
}

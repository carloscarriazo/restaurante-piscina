<?php

namespace App\Http\Controllers;

use App\Contracts\MenuRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MenuController extends Controller
{
    protected $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function index()
    {
        return view('menu.index');
    }

    public function manage()
    {
        return view('menu.manage');
    }

    public function qr()
    {
        return view('menu.qr');
    }

    public function digital()
    {
        // Obtener todas las categorías con sus ítems filtrados por día actual
        $categories = $this->menuRepository->getAllCategoriesWithItems();

        // Filtrar ítems que están disponibles hoy (viernes, sábado, domingo)
        $today = Carbon::now()->dayOfWeek;

        $categories = $categories->map(function ($category) use ($today) {
            $category['items'] = collect($category['items'])->filter(function ($item) use ($today) {
                // Verificar que el ítem esté disponible
                if (!$item['is_available']) {
                    return false;
                }

                // Verificar los días de operación
                $operatingDays = $item['operating_days'] ?? [5, 6, 0]; // Viernes, Sábado, Domingo
                if (!in_array($today, $operatingDays)) {
                    return false;
                }

                // Verificar el período de validez
                $now = Carbon::now()->startOfDay();
                if (isset($item['valid_from']) && $item['valid_from'] && Carbon::parse($item['valid_from'])->gt($now)) {
                    return false;
                }
                if (isset($item['valid_until']) && $item['valid_until'] && Carbon::parse($item['valid_until'])->lt($now)) {
                    return false;
                }

                return true;
            })->values();

            return $category;
        })->filter(function ($category) {
            // Eliminar categorías sin ítems disponibles
            return $category['items']->isNotEmpty();
        })->values();

        // Verificar si estamos en día de operación
        $isOperatingDay = in_array($today, [5, 6, 0]); // Viernes, Sábado, Domingo
        $dayName = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][$today];

        return view('menu.digital', compact('categories', 'isOperatingDay', 'dayName'));
    }

    /**
     * Vista previa del menú digital sin filtros de día (para desarrollo/pruebas)
     */
    public function digitalPreview()
    {
        // Obtener todas las categorías con sus ítems SIN filtrar por día
        $categories = $this->menuRepository->getAllCategoriesWithItems();

        // Solo filtrar por disponibilidad y período de validez
        $now = Carbon::now()->startOfDay();

        $categories = $categories->map(function ($category) use ($now) {
            $category['items'] = collect($category['items'])->filter(function ($item) use ($now) {
                // Solo verificar que el ítem esté disponible
                if (!$item['is_available']) {
                    return false;
                }

                // Verificar el período de validez
                if (isset($item['valid_from']) && $item['valid_from'] && Carbon::parse($item['valid_from'])->gt($now)) {
                    return false;
                }
                if (isset($item['valid_until']) && $item['valid_until'] && Carbon::parse($item['valid_until'])->lt($now)) {
                    return false;
                }

                return true;
            })->values();

            return $category;
        })->filter(function ($category) {
            // Eliminar categorías sin ítems disponibles
            return $category['items']->isNotEmpty();
        })->values();

        // Para preview, siempre mostrar como día operativo
        $isOperatingDay = true;
        $today = Carbon::now()->dayOfWeek;
        $dayName = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][$today];

        return view('menu.digital', compact('categories', 'isOperatingDay', 'dayName'));
    }

    // API endpoints para la carta digital
    public function apiIndex()
    {
        $categories = $this->menuRepository->getAllCategoriesWithItems();

        // Filtrar ítems por día actual
        $today = Carbon::now()->dayOfWeek;

        $categories = $categories->map(function ($category) use ($today) {
            $category['items'] = collect($category['items'])->filter(function ($item) use ($today) {
                if (!$item['is_available']) {
                    return false;
                }

                $operatingDays = $item['operating_days'] ?? [5, 6, 0];
                if (!in_array($today, $operatingDays)) {
                    return false;
                }

                $now = Carbon::now()->startOfDay();
                if (isset($item['valid_from']) && $item['valid_from'] && Carbon::parse($item['valid_from'])->gt($now)) {
                    return false;
                }
                if (isset($item['valid_until']) && $item['valid_until'] && Carbon::parse($item['valid_until'])->lt($now)) {
                    return false;
                }

                return true;
            })->values();

            return $category;
        })->filter(function ($category) {
            return $category['items']->isNotEmpty();
        })->values();

        $isOperatingDay = in_array($today, [5, 6, 0]);

        return response()->json([
            'success' => true,
            'data' => $categories,
            'meta' => [
                'is_operating_day' => $isOperatingDay,
                'current_day' => $today,
                'day_name' => ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][$today]
            ]
        ]);
    }

    public function apiCategory($id)
    {
        $category = $this->menuRepository->getCategoryWithItems($id);

        // Filtrar ítems por día actual
        $today = Carbon::now()->dayOfWeek;

        if ($category && isset($category['items'])) {
            $category['items'] = collect($category['items'])->filter(function ($item) use ($today) {
                if (!$item['is_available']) {
                    return false;
                }

                $operatingDays = $item['operating_days'] ?? [5, 6, 0];
                if (!in_array($today, $operatingDays)) {
                    return false;
                }

                $now = Carbon::now()->startOfDay();
                if (isset($item['valid_from']) && $item['valid_from'] && Carbon::parse($item['valid_from'])->gt($now)) {
                    return false;
                }
                if (isset($item['valid_until']) && $item['valid_until'] && Carbon::parse($item['valid_until'])->lt($now)) {
                    return false;
                }

                return true;
            })->values();
        }

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
}

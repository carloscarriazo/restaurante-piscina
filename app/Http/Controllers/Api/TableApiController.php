<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Http\Request;

class TableApiController extends Controller
{
    // Obtener todas las mesas
    public function index()
    {
        try {
            $tables = Table::with(['currentOrder' => function($query) {
                $query->where('status', '!=', 'completed');
            }])->orderBy('number')->get();

            return response()->json([
                'success' => true,
                'data' => $tables
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las mesas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener mesa específica con su orden actual
    public function show($id)
    {
        try {
            $table = Table::with([
                'currentOrder.items.product',
                'currentOrder.user'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $table
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mesa no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Obtener estado de todas las mesas (ocupada/libre)
    public function status()
    {
        try {
            $tables = Table::select('id', 'number', 'capacity', 'is_available')
                ->with(['currentOrder' => function($query) {
                    $query->select('id', 'table_id', 'status', 'created_at')
                          ->where('status', '!=', 'completed');
                }])
                ->orderBy('number')
                ->get()
                ->map(function($table) {
                    return [
                        'id' => $table->id,
                        'number' => $table->number,
                        'capacity' => $table->capacity,
                        'is_available' => $table->is_available,
                        'status' => $table->currentOrder ? 'occupied' : 'free',
                        'order_id' => $table->currentOrder ? $table->currentOrder->id : null,
                        'order_time' => $table->currentOrder ? $table->currentOrder->created_at->diffForHumans() : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $tables
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado de las mesas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Crear nueva mesa
    public function store(Request $request)
    {
        try {
            $request->validate([
                'number' => 'required|integer|unique:tables',
                'capacity' => 'required|integer|min:1',
                'location' => 'nullable|string|max:255',
                'is_available' => 'boolean'
            ]);

            $table = Table::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mesa creada exitosamente',
                'data' => $table
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la mesa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Actualizar mesa
    public function update(Request $request, $id)
    {
        try {
            $table = Table::findOrFail($id);

            $request->validate([
                'number' => 'sometimes|integer|unique:tables,number,' . $id,
                'capacity' => 'sometimes|integer|min:1',
                'location' => 'nullable|string|max:255',
                'is_available' => 'boolean'
            ]);

            $table->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Mesa actualizada exitosamente',
                'data' => $table
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la mesa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Cambiar disponibilidad de mesa
    public function toggleAvailability($id)
    {
        try {
            $table = Table::findOrFail($id);

            // Verificar si la mesa tiene una orden activa
            if (!$table->is_available && $table->currentOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cambiar la disponibilidad de una mesa ocupada'
                ], 403);
            }

            $table->update(['is_available' => !$table->is_available]);

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad actualizada',
                'data' => $table
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Liberar mesa (completar orden)
    public function free($id)
    {
        try {
            $table = Table::findOrFail($id);

            if ($table->currentOrder) {
                $table->currentOrder->update(['status' => 'completed']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mesa liberada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al liberar la mesa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Obtener órdenes de una mesa
    public function orders($id)
    {
        try {
            $table = Table::findOrFail($id);
            $orders = $table->orders()
                           ->with(['items.product', 'user'])
                           ->latest()
                           ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'table' => $table,
                    'orders' => $orders
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las órdenes de la mesa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminar mesa
    public function destroy($id)
    {
        try {
            $table = Table::findOrFail($id);

            // Verificar si la mesa tiene órdenes activas
            if ($table->orders()->where('status', '!=', 'completed')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar una mesa con órdenes activas'
                ], 403);
            }

            $table->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mesa eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la mesa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

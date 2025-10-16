<?php

namespace App\Http\Controllers\Api;

use App\Models\Table;
use App\Models\User;
use App\Constants\ValidationRules;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Asignación de Mesas",
 *     description="Gestión de asignación de mesas a meseros"
 * )
 */
class TableAssignmentController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/table-assignments",
     *     tags={"Asignación de Mesas"},
     *     summary="Obtener todas las asignaciones de mesas",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de mesas con sus asignaciones"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $tables = Table::with(['waiter', 'currentOrder'])
                ->orderBy('number')
                ->get()
                ->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'number' => $table->number,
                        'name' => $table->name,
                        'capacity' => $table->capacity,
                        'status' => $table->status,
                        'waiter' => $table->waiter ? [
                            'id' => $table->waiter->id,
                            'name' => $table->waiter->name,
                            'email' => $table->waiter->email
                        ] : null,
                        'assigned_at' => $table->assigned_at?->format(ValidationRules::DATE_FORMAT_YMD_HIS),
                        'has_active_order' => $table->currentOrder !== null
                    ];
                });

            return $this->successResponse($tables, 'Asignaciones obtenidas exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener asignaciones: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table-assignments/assign",
     *     tags={"Asignación de Mesas"},
     *     summary="Asignar un mesero a una mesa",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"table_id","waiter_id"},
     *             @OA\Property(property="table_id", type="integer", example=1),
     *             @OA\Property(property="waiter_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mesa asignada exitosamente"
     *     )
     * )
     */
    public function assign(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'table_id' => 'required|exists:tables,id',
                'waiter_id' => 'required|exists:users,id'
            ]);

            $waiter = $this->findAndValidateWaiter($validated['waiter_id']);
            $table = Table::find($validated['table_id']);
            $table->assignWaiter($validated['waiter_id']);

            return $this->successResponse([
                'table' => [
                    'id' => $table->id,
                    'name' => $table->name,
                    'number' => $table->number
                ],
                'waiter' => [
                    'id' => $waiter->id,
                    'name' => $waiter->name
                ],
                'assigned_at' => $table->assigned_at->format(ValidationRules::DATE_FORMAT_YMD_HIS)
            ], 'Mesa asignada exitosamente al mesero');

        } catch (\Exception $e) {
            return $this->handleTableException($e, 'asignar mesa');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table-assignments/unassign",
     *     tags={"Asignación de Mesas"},
     *     summary="Remover asignación de mesero de una mesa",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"table_id"},
     *             @OA\Property(property="table_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación removida exitosamente"
     *     )
     * )
     */
    public function unassign(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'table_id' => 'required|exists:tables,id'
            ]);

            $table = Table::find($validated['table_id']);
            $this->validateTableAssignment($table);
            $table->unassignWaiter();

            return $this->successResponse(null, 'Asignación removida exitosamente');

        } catch (\Exception $e) {
            return $this->handleTableException($e, 'remover asignación');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/table-assignments/waiter/{waiterId}",
     *     tags={"Asignación de Mesas"},
     *     summary="Obtener mesas asignadas a un mesero específico",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="waiterId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mesas asignadas al mesero"
     *     )
     * )
     */
    public function waiterTables(int $waiterId): JsonResponse
    {
        try {
            $waiter = $this->findAndValidateWaiter($waiterId);

            $tables = Table::where('waiter_id', $waiterId)
                ->with('currentOrder')
                ->get()
                ->map(function ($table) {
                    return [
                        'id' => $table->id,
                        'number' => $table->number,
                        'name' => $table->name,
                        'capacity' => $table->capacity,
                        'status' => $table->status,
                        'assigned_at' => $table->assigned_at?->format(ValidationRules::DATE_FORMAT_YMD_HIS),
                        'has_active_order' => $table->currentOrder !== null,
                        'active_order' => $table->currentOrder ? [
                            'id' => $table->currentOrder->id,
                            'status' => $table->currentOrder->status,
                            'total' => $table->currentOrder->total
                        ] : null
                    ];
                });

            return $this->successResponse([
                'waiter' => ['id' => $waiter->id, 'name' => $waiter->name],
                'tables' => $tables,
                'total_tables' => $tables->count()
            ], 'Mesas del mesero obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->handleTableException($e, 'obtener mesas del mesero');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table-assignments/bulk-assign",
     *     tags={"Asignación de Mesas"},
     *     summary="Asignar múltiples mesas a un mesero",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"table_ids","waiter_id"},
     *             @OA\Property(property="table_ids", type="array", @OA\Items(type="integer"), example={1,2,3}),
     *             @OA\Property(property="waiter_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mesas asignadas exitosamente"
     *     )
     * )
     */
    public function bulkAssign(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'table_ids' => 'required|array|min:1',
                'table_ids.*' => 'exists:tables,id',
                'waiter_id' => 'required|exists:users,id'
            ]);

            $waiter = $this->findAndValidateWaiter($validated['waiter_id']);

            DB::beginTransaction();

            $assignedTables = [];
            foreach ($validated['table_ids'] as $tableId) {
                $table = Table::find($tableId);
                $table->assignWaiter($validated['waiter_id']);
                $assignedTables[] = [
                    'id' => $table->id,
                    'name' => $table->name,
                    'number' => $table->number
                ];
            }

            DB::commit();

            return $this->successResponse([
                'waiter' => ['id' => $waiter->id, 'name' => $waiter->name],
                'tables' => $assignedTables,
                'total_assigned' => count($assignedTables)
            ], 'Mesas asignadas exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->serverErrorResponse('Error al asignar mesas: ' . $e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/table-assignments/available-waiters",
     *     tags={"Asignación de Mesas"},
     *     summary="Obtener lista de meseros disponibles",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de meseros"
     *     )
     * )
     */
    public function availableWaiters(): JsonResponse
    {
        try {
            $waiters = User::whereHas('roles', function ($query) {
                $query->where('nombre', 'Mesero');
            })
            ->withCount('tables')
            ->get()
            ->map(function ($waiter) {
                return [
                    'id' => $waiter->id,
                    'name' => $waiter->name,
                    'email' => $waiter->email,
                    'assigned_tables_count' => $waiter->tables_count ?? 0
                ];
            });

            return $this->successResponse($waiters, 'Meseros disponibles obtenidos exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener meseros: ' . $e->getMessage());
        }
    }

    /**
     * Métodos privados para SRP
     */
    private function findAndValidateWaiter($waiterId): User
    {
        $waiter = User::findOrFail($waiterId);

        if (!$waiter->hasAnyRole(['Mesero'])) {
            throw new \InvalidArgumentException('El usuario seleccionado no es un mesero');
        }

        return $waiter;
    }

    private function validateTableAssignment(Table $table): void
    {
        if (!$table->isAssigned()) {
            throw new \InvalidArgumentException('La mesa no tiene ninguna asignación');
        }
    }

    /**
     * Manejador centralizado de excepciones (SRP + OCP + Strategy Pattern)
     */
    private function handleTableException(\Exception $e, string $action): JsonResponse
    {
        $handlers = [
            \Illuminate\Validation\ValidationException::class =>
                fn($ex) => $this->validationErrorResponse($ex->errors()),
            \Illuminate\Database\Eloquent\ModelNotFoundException::class =>
                fn($ex) => $this->notFoundResponse('Mesero no encontrado'),
            \InvalidArgumentException::class =>
                fn($ex) => $this->errorResponse($ex->getMessage(), 400),
        ];

        $handler = $handlers[get_class($e)] ??
            fn($ex) => $this->serverErrorResponse("Error al {$action}: " . $ex->getMessage());

        return $handler($e);
    }
}

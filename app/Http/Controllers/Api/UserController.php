<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Constants\MessageConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Usuarios",
 *     description="Gestión de usuarios y roles"
 * )
 */
class UserController extends BaseApiController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Listar todos los usuarios",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filtrar por rol",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Lista de usuarios con roles")
     * )
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filtrar por rol
        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Búsqueda
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $request->has('per_page')
            ? $query->paginate($request->per_page)
            : $query->get();

        return $this->successResponse($users, 'Usuarios obtenidos exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Crear nuevo usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "role"},
     *             @OA\Property(property="name", type="string", example="Juan Pérez"),
     *             @OA\Property(property="email", type="string", example="juan@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="Mesero", description="Roles: Administrador, Gerente, Mesero, Cocinero, Cajero")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuario creado")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Administrador,Gerente,Mesero,Cocinero,Cajero'
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'El email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'role.required' => 'El rol es obligatorio',
            'role.in' => 'Rol inválido'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignar rol
            $user->assignRole($request->role);

            DB::commit();

            $user->load('roles');

            return $this->successResponse($user, 'Usuario creado exitosamente', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al crear usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Obtener detalle de un usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalle del usuario")
     * )
     */
    public function show($id)
    {
        $user = User::with(['roles', 'permissions'])->find($id);

        if (!$user) {
            return $this->errorResponse(MessageConstants::USER_NOT_FOUND, 404);
        }

        return $this->successResponse($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Actualizar usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $this->findUserOrFail($id);

            $validator = $this->validateUserUpdate($request, $id);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors()->first(), 422);
            }

            DB::beginTransaction();

            $data = [];
            if ($request->has('name')) {
                $data['name'] = $request->name;
            }
            if ($request->has('email')) {
                $data['email'] = $request->email;
            }
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if (!empty($data)) {
                $user->update($data);
            }

            // Actualizar rol si se proporciona
            if ($request->has('role')) {
                $user->syncRoles([$request->role]);
            }

            DB::commit();

            $user->load('roles');

            return $this->successResponse($user, 'Usuario actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Error al actualizar usuario: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Eliminar usuario",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuario eliminado")
     * )
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse(MessageConstants::USER_NOT_FOUND, 404);
        }

        // No permitir eliminar al usuario autenticado
        if ($user->getKey() === Auth::id()) {
            return $this->errorResponse('No puedes eliminar tu propio usuario', 400);
        }

        $user->delete();

        return $this->successResponse(null, 'Usuario eliminado exitosamente');
    }

    /**
     * @OA\Get(
     *     path="/api/users/roles/available",
     *     summary="Obtener lista de roles disponibles",
     *     tags={"Usuarios"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Lista de roles")
     * )
     */
    public function availableRoles()
    {
        $roles = [
            ['name' => 'Administrador', 'description' => 'Acceso completo al sistema'],
            ['name' => 'Gerente', 'description' => 'Gestión operativa y reportes'],
            ['name' => 'Mesero', 'description' => 'Gestión de pedidos y mesas'],
            ['name' => 'Cocinero', 'description' => 'Preparación de pedidos'],
            ['name' => 'Cajero', 'description' => 'Facturación y cobros']
        ];

        return $this->successResponse($roles, 'Roles disponibles');
    }

    /**
     * Métodos privados para SRP
     */
    private function findUserOrFail($id): User
    {
        $user = User::find($id);

        if (!$user) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(MessageConstants::USER_NOT_FOUND);
        }

        return $user;
    }

    private function validateUserUpdate(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:Administrador,Gerente,Mesero,Cocinero,Cajero'
        ]);
    }
}

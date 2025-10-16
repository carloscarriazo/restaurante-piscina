<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Autenticación"},
     *     summary="Iniciar sesión",
     *     description="Autenticar usuario y obtener token de acceso",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@ejemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login exitoso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="admin@ejemplo.com"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin"))
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Las credenciales proporcionadas son incorrectas.")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Cargar roles del usuario
        $user->load('roles');

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'roles' => $user->roles->pluck('nombre'),
                ],
                'token' => $token,
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Autenticación"},
     *     summary="Cerrar sesión",
     *     description="Invalidar el token de acceso actual",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout exitoso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Autenticación"},
     *     summary="Obtener usuario actual",
     *     description="Obtener información del usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="admin@ejemplo.com"),
     *                     @OA\Property(property="profile_photo_url", type="string"),
     *                     @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'roles' => $user->roles->pluck('nombre'),
                ]
            ]
        ]);
    }

    /**
     * Refresh del token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        // Eliminar token actual
        $request->user()->currentAccessToken()->delete();

        // Crear nuevo token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado exitosamente',
            'data' => [
                'token' => $token,
            ]
        ]);
    }
}

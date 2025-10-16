<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }

        $user = Auth::user();

        // Verificar si el usuario tiene alguno de los roles requeridos
        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->roles->contains('name', $role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción',
                'required_roles' => $roles,
                'user_roles' => $user->roles->pluck('name')
            ], 403);
        }

        return $next($request);
    }
}

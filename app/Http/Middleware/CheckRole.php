<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Verifica que el usuario autenticado tenga uno de los roles permitidos.
     *
     * Uso:
     * Route::middleware('role:admin,waiter')->group(function () { ... });
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles - Lista de roles permitidos separados por coma
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado. Por favor inicie sesión.'
            ], 401);
        }

        // Verificar que el usuario tenga uno de los roles requeridos
        if (!empty($roles)) {
            $userRoles = $request->user()->roles->pluck('name')->toArray();

            // Comprobar si el usuario tiene al menos uno de los roles permitidos
            $hasRole = false;
            foreach ($roles as $role) {
                if (in_array($role, $userRoles)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos suficientes para acceder a este recurso.',
                    'required_roles' => $roles,
                    'your_roles' => $userRoles
                ], 403);
            }
        }

        return $next($request);
    }
}

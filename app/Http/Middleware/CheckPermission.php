<?php

namespace App\Http\Middleware;

use App\Models\SessionLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            SessionLog::logAction(
                null,
                'permission_denied',
                false,
                null,
                $permission,
                ['route' => $request->route()->getName()]
            );

            abort(401, 'No autenticado');
        }

        $user = Auth::user();

        if (!$user->hasPermission($permission)) {
            SessionLog::logAction(
                $user->id,
                'permission_denied',
                false,
                null,
                $permission,
                ['route' => $request->route()->getName()]
            );

            abort(403, 'No tienes permisos para realizar esta acción');
        }

        // Log de acceso exitoso
        SessionLog::logAction(
            $user->id,
            'permission_granted',
            true,
            null,
            $permission,
            ['route' => $request->route()->getName()]
        );

        return $next($request);
    }
}

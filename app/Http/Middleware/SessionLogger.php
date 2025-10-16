<?php

namespace App\Http\Middleware;

use App\Models\SessionLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log de acceso a rutas protegidas
        if (Auth::check()) {
            $route = $request->route();
            if ($route) {
                SessionLog::logAction(
                    Auth::id(),
                    'page_access',
                    true,
                    null,
                    null,
                    [
                        'route' => $route->getName() ?? $route->uri(),
                        'method' => $request->method(),
                        'url' => $request->url()
                    ]
                );
            }
        }

        return $response;
    }
}

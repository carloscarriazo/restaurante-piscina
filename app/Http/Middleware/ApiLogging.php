<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLogging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log entrada de request
        $startTime = microtime(true);

        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $request->user() ? $request->user()->id : null,
            'input' => $request->except(['password', 'password_confirmation']),
            'timestamp' => now()->toISOString()
        ]);

        $response = $next($request);

        // Log respuesta
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // En milisegundos

        Log::channel('api')->info('API Response', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'execution_time_ms' => $executionTime,
            'user_id' => $request->user() ? $request->user()->id : null,
            'timestamp' => now()->toISOString()
        ]);

        // Log errores si el status code indica error
        if ($response->getStatusCode() >= 400) {
            Log::channel('api')->error('API Error', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'response_content' => $response->getContent(),
                'user_id' => $request->user() ? $request->user()->id : null,
                'timestamp' => now()->toISOString()
            ]);
        }

        return $response;
    }
}
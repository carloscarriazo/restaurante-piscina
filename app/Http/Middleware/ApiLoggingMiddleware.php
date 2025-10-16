<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        // Log request
        Log::channel('api')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth('sanctum')->id(),
            'request_id' => uniqid('req_'),
            'timestamp' => now()->toISOString()
        ]);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // milliseconds

        // Log response
        Log::channel('api')->info('API Response', [
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'content_length' => strlen($response->getContent()),
            'timestamp' => now()->toISOString()
        ]);

        // Log errors for non-successful responses
        if ($response->getStatusCode() >= 400) {
            Log::channel('api')->error('API Error Response', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status_code' => $response->getStatusCode(),
                'user_id' => auth('sanctum')->id(),
                'response_content' => $response->getContent(),
                'timestamp' => now()->toISOString()
            ]);
        }

        return $response;
    }
}

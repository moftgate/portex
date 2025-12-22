<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServerAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        $serverApiKey = config('portex.server_api_key');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'error' => 'Missing or invalid authorization header',
            ], 401);
        }

        $providedKey = substr($authHeader, 7); // Remove "Bearer "

        if ($providedKey !== $serverApiKey) {
            return response()->json([
                'error' => 'Invalid server API key',
            ], 401);
        }

        return $next($request);
    }
}

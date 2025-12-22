<?php

namespace App\Http\Middleware;

use App\Services\AgentService;
use Closure;
use Illuminate\Http\Request;

class AgentAuthentication
{
    public function __construct(
        protected AgentService $agentService
    ) {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'error' => 'Missing or invalid authorization header',
            ], 401);
        }

        // Extract API key and secret from header
        // Format: "Bearer {api_key}:{api_secret}"
        $credentials = substr($authHeader, 7); // Remove "Bearer "
        $parts = explode(':', $credentials, 2);

        if (count($parts) !== 2) {
            return response()->json([
                'error' => 'Invalid credentials format',
            ], 401);
        }

        [$apiKey, $apiSecret] = $parts;

        $agent = $this->agentService->validateCredentials($apiKey, $apiSecret);

        if (!$agent) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Attach agent to request
        $request->merge(['agent' => $agent]);
        $request->setUserResolver(fn () => $agent);

        return $next($request);
    }
}

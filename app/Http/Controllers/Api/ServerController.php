<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tunnel;
use App\Models\TunnelRequest;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Get all active tunnels for routing.
     */
    public function getTunnels(Request $request)
    {
        $tunnels = Tunnel::with('agent')
            ->active()
            ->get()
            ->map(function ($tunnel) {
                return [
                    'id' => $tunnel->id,
                    'subdomain' => $tunnel->subdomain,
                    'custom_domain' => $tunnel->custom_domain,
                    'protocol' => $tunnel->protocol,
                    'agent_id' => $tunnel->agent_id,
                    'auth_enabled' => $tunnel->auth_enabled,
                    'auth_username' => $tunnel->auth_username,
                    'auth_password' => $tunnel->auth_password,
                ];
            });

        return response()->json([
            'tunnels' => $tunnels,
        ]);
    }

    /**
     * Log incoming request for analytics.
     */
    public function logRequest(Request $request, Tunnel $tunnel)
    {
        $validated = $request->validate([
            'method' => 'required|string',
            'path' => 'required|string',
            'status_code' => 'required|integer',
            'response_time_ms' => 'required|integer',
            'ip_address' => 'required|string',
            'user_agent' => 'nullable|string',
            'bytes_uploaded' => 'nullable|integer',
            'bytes_downloaded' => 'nullable|integer',
        ]);

        TunnelRequest::create([
            'tunnel_id' => $tunnel->id,
            'method' => $validated['method'],
            'path' => $validated['path'],
            'status_code' => $validated['status_code'],
            'response_time_ms' => $validated['response_time_ms'],
            'ip_address' => $validated['ip_address'],
            'user_agent' => $validated['user_agent'] ?? null,
        ]);

        // Track bandwidth and usage
        $usageService = app(\App\Services\UsageTrackingService::class);
        $usageService->trackBandwidth(
            $tunnel, 
            $validated['bytes_uploaded'] ?? 0, 
            $validated['bytes_downloaded'] ?? 0
        );

        // For simplicity, we count each request as 1 second of "active usage" 
        // if no other time tracking is implemented yet.
        $usageService->trackUsageTime($tunnel, 1);

        return response()->json([
            'status' => 'logged',
        ]);
    }
}

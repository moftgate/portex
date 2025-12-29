<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tunnel;
use App\Models\TunnelRequest;
use App\Services\UsageTrackingService;
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
                    'pin' => $tunnel->pin,
                    'allowed_ips' => $tunnel->allowed_ips,
                ];
            });

        return response()->json([
            'tunnels' => $tunnels,
        ]);
    }

    /**
     * Get a single tunnel for routing.
     */
    public function getTunnel(Tunnel $tunnel)
    {
        return response()->json([
            'tunnel' => [
                'id' => $tunnel->id,
                'subdomain' => $tunnel->subdomain,
                'custom_domain' => $tunnel->custom_domain,
                'protocol' => $tunnel->protocol,
                'agent_id' => $tunnel->agent_id,
                'auth_enabled' => $tunnel->auth_enabled,
                'auth_username' => $tunnel->auth_username,
                'auth_password' => $tunnel->auth_password,
                'pin' => $tunnel->pin,
                'allowed_ips' => $tunnel->allowed_ips,
            ],
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
            'request_headers' => 'nullable|array',
            'request_body' => 'nullable|string',
            'response_headers' => 'nullable|array',
            'response_body' => 'nullable|string',
            'bytes_uploaded' => 'nullable|integer',
            'bytes_downloaded' => 'nullable|integer',
        ]);

        $decodedRequestBody = ! empty($validated['request_body']) ? base64_decode($validated['request_body']) : null;
        $decodedResponseBody = ! empty($validated['response_body']) ? base64_decode($validated['response_body']) : null;

        TunnelRequest::create([
            'tunnel_id' => $tunnel->id,
            'method' => $validated['method'],
            'path' => $validated['path'],
            'request_headers' => $validated['request_headers'] ?? [],
            'request_body' => $decodedRequestBody,
            'status_code' => $validated['status_code'],
            'response_headers' => $validated['response_headers'] ?? [],
            'response_body' => $decodedResponseBody,
            'response_time_ms' => $validated['response_time_ms'],
            'ip_address' => $validated['ip_address'],
            'user_agent' => ! empty($validated['user_agent']) ? $validated['user_agent'] : null,
        ]);

        // Track bandwidth and usage
        $usageService = app(UsageTrackingService::class);
        $usageService->trackBandwidth(
            $tunnel,
            $validated['bytes_uploaded'] ?? 0,
            $validated['bytes_downloaded'] ?? 0
        );

        // For simplicity, we count each request as 1 second of "active usage"
        // if no other time tracking is implemented yet.
        // $usageService->trackUsageTime($tunnel, 1);

        return response()->json([
            'status' => 'logged',
        ]);
    }

    /**
     * Update tunnel status (e.g., active, inactive).
     */
    public function updateStatus(Request $request, Tunnel $tunnel)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,online,offline',
        ]);

        $status = $validated['status'];
        // Map 'online' -> 'active', 'offline' -> 'inactive' if needed, or just use as is.
        // Assuming database uses 'active' / 'inactive'
        if ($status === 'online') $status = 'active';
        if ($status === 'offline') $status = 'inactive';

        $tunnel->update([
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Tunnel status updated',
            'status' => $tunnel->status,
        ]);
    }
}

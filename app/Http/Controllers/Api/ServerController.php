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
            'request_headers' => 'nullable|array',
            'request_body' => 'nullable|string',
            'response_headers' => 'nullable|array',
            'response_body' => 'nullable|string',
            'bytes_uploaded' => 'nullable|integer',
            'bytes_downloaded' => 'nullable|integer',
        ]);

        \Log::info('DEBUG: Received request body (base64)', [
            'has_body' => isset($validated['request_body']),
            'is_empty' => empty($validated['request_body']),
            'length' => isset($validated['request_body']) ? strlen($validated['request_body']) : 0,
            'raw' => $validated['request_body'] ?? 'NULL',
        ]);

        $decodedRequestBody = !empty($validated['request_body']) ? base64_decode($validated['request_body']) : null;
        $decodedResponseBody = !empty($validated['response_body']) ? base64_decode($validated['response_body']) : null;

        \Log::info('DEBUG: Decoded request body', [
            'decoded_length' => $decodedRequestBody ? strlen($decodedRequestBody) : 0,
            'decoded_content' => $decodedRequestBody,
        ]);

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
            'user_agent' => !empty($validated['user_agent']) ? $validated['user_agent'] : null,
        ]);

        \Log::info('DEBUG: TunnelRequest created successfully');

        // Track bandwidth and usage
        $usageService = app(UsageTrackingService::class);
        $usageService->trackBandwidth(
            $tunnel,
            $validated['bytes_uploaded'] ?? 0,
            $validated['bytes_downloaded'] ?? 0
        );

        // For simplicity, we count each request as 1 second of "active usage"
        // if no other time tracking is implemented yet.
        //$usageService->trackUsageTime($tunnel, 1);

        return response()->json([
            'status' => 'logged',
        ]);
    }
}

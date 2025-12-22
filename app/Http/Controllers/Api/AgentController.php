<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tunnel;
use App\Services\AgentService;
use App\Services\TunnelService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $agentService,
        protected TunnelService $tunnelService
    ) {
    }

    /**
     * Authenticate agent and return token.
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
        ]);

        $agent = $this->agentService->validateCredentials(
            $request->api_key,
            $request->api_secret
        );

        if (!$agent) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

        // Update agent status to online
        $this->agentService->updateAgentStatus($agent, 'online');

        return response()->json([
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'message' => 'Authentication successful',
        ]);
    }

    /**
     * Register a new anonymous agent (auto-registration).
     */
    public function registerAnonymous(Request $request)
    {
        // Get or create default user for anonymous agents
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'anonymous@portex.io'],
            [
                'name' => 'Anonymous User',
                'password' => bcrypt(str()->random(32)),
            ]
        );

        // Create agent
        $agent = $this->agentService->registerAgent($user, [
            'name' => 'Agent ' . now()->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'api_key' => $agent->api_key,
            'api_secret' => $agent->plain_api_secret,
            'message' => 'Agent registered successfully',
        ], 201);
    }

    /**
     * Get agent configuration.
     */
    public function getConfig(Request $request)
    {
        $agent = $request->agent;

        return response()->json(
            $this->agentService->getAgentConfig($agent)
        );
    }

    /**
     * Record agent heartbeat.
     */
    public function heartbeat(Request $request)
    {
        $agent = $request->agent;

        $this->agentService->heartbeat($agent);

        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get active tunnels for this agent.
     */
    public function getTunnels(Request $request)
    {
        $agent = $request->agent;

        $tunnels = $agent->tunnels()
            ->active()
            ->get()
            ->map(function ($tunnel) {
                return [
                    'id' => $tunnel->id,
                    'name' => $tunnel->name,
                    'subdomain' => $tunnel->subdomain,
                    'local_port' => $tunnel->local_port,
                    'protocol' => $tunnel->protocol,
                    'public_url' => $tunnel->public_url,
                ];
            });

        return response()->json([
            'tunnels' => $tunnels,
        ]);
    }

    /**
     * Create a new tunnel for the agent.
     */
    public function createTunnel(Request $request)
    {
        $agent = $request->agent;

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'subdomain' => 'nullable|string|max:255|alpha_dash',
            'local_port' => 'required|integer|min:1|max:65535',
            'protocol' => 'nullable|in:http,https,tcp',
        ]);

        // Set defaults
        $validated['name'] = $validated['name'] ?? 'Tunnel ' . now()->format('Y-m-d H:i');
        $validated['protocol'] = $validated['protocol'] ?? 'http';
        $validated['agent_id'] = $agent->id;

        // Check if this agent already has this subdomain
        if (!empty($validated['subdomain'])) {
            $existing = Tunnel::where('agent_id', $agent->id)
                ->where('subdomain', $validated['subdomain'])
                ->first();

            if ($existing) {
                $existing->update([
                    'local_port' => $validated['local_port'],
                    'protocol' => $validated['protocol'],
                    'status' => 'active',
                ]);

                return response()->json([
                    'tunnel' => [
                        'id' => $existing->id,
                        'name' => $existing->name,
                        'subdomain' => $existing->subdomain,
                        'local_port' => $existing->local_port,
                        'protocol' => $existing->protocol,
                        'public_url' => $existing->public_url,
                        'status' => $existing->status,
                    ],
                    'message' => 'Tunnel updated successfully',
                ], 200);
            }
        }

        // Create tunnel for the agent's user
        $tunnel = $this->tunnelService->createTunnel($agent->user, $validated);

        return response()->json([
            'tunnel' => [
                'id' => $tunnel->id,
                'name' => $tunnel->name,
                'subdomain' => $tunnel->subdomain,
                'local_port' => $tunnel->local_port,
                'protocol' => $tunnel->protocol,
                'public_url' => $tunnel->public_url,
                'status' => $tunnel->status,
            ],
            'message' => 'Tunnel created successfully',
        ], 201);
    }
}

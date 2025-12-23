<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\AgentLoginToken;
use App\Models\Tunnel;
use App\Models\User;
use App\Services\AgentService;
use App\Services\TunnelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $agentService,
        protected TunnelService $tunnelService
    ) {}

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

        if (! $agent) {
            return response()->json([
                'error' => 'Invalid credentials',
            ], 401);
        }

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
        $validated = $request->validate([
            'device_id' => 'required|string|max:255',
            'hostname' => 'nullable|string|max:255',
        ]);

        $deviceId = $validated['device_id'];
        $hostname = $validated['hostname'] ?? $deviceId;

        // Sanitize hostname for email (remove spaces, special chars)
        $sanitizedHostname = preg_replace('/[^a-zA-Z0-9-]/', '-', strtolower($hostname));
        $sanitizedHostname = preg_replace('/-+/', '-', $sanitizedHostname); // Remove multiple dashes
        $sanitizedHostname = trim($sanitizedHostname, '-'); // Remove leading/trailing dashes

        // Create email from hostname
        $deviceEmail = "{$sanitizedHostname}@portex.space";

        // Get or create device-specific user
        $user = User::firstOrCreate(
            ['email' => $deviceEmail],
            [
                'name' => $hostname,
                'password' => bcrypt(str()->random(32)),
                'email_verified_at' => now(),
            ]
        );

        // Check if agent already exists for this device
        $existingAgent = Agent::where('device_id', $deviceId)->first();

        if ($existingAgent) {
            // Regenerate credentials for existing agent
            $result = $this->agentService->regenerateCredentials($existingAgent);

            return response()->json([
                'agent_id' => $result['agent']->id,
                'agent_name' => $result['agent']->name,
                'api_key' => $result['agent']->api_key,
                'api_secret' => $result['plain_secret'],
                'message' => 'Agent credentials regenerated',
            ], 200);
        }

        // Create new agent for this device
        $result = $this->agentService->registerAgent($user, [
            'name' => $hostname ?? 'Agent '.now()->format('Y-m-d H:i:s'),
            'metadata' => ['device_id' => $deviceId],
        ]);

        $agent = $result['agent'];
        $plainSecret = $result['plain_secret'];

        // Update device_id directly in database
        \DB::table('agents')
            ->where('id', $agent->id)
            ->update(['device_id' => $deviceId]);

        return response()->json([
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'api_key' => $agent->api_key,
            'api_secret' => $plainSecret,
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
        $user = $agent->user;

        // Check usage limits
        $usageService = app(\App\Services\UsageTrackingService::class);

        if ($usageService->hasExceededDailyLimit($user)) {
            $stats = $usageService->getUsageStats($user);

            return response()->json([
                'error' => 'Daily usage limit exceeded',
                'message' => "You've used {$stats['used_formatted']} of your {$stats['limit_formatted']} daily limit. Upgrade to Premium for unlimited usage!",
                'usage_stats' => $stats,
                'upgrade_url' => config('app.url').'/panel/upgrade',
            ], 429); // Too Many Requests
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'subdomain' => 'nullable|string|max:63|regex:/^[a-z0-9-]+$/',
            'local_port' => 'required|integer|min:1|max:65535',
            'protocol' => 'nullable|in:http,https,tcp',
        ]);

        // Set defaults
        $validated['name'] = $validated['name'] ?? 'Tunnel '.now()->format('Y-m-d H:i');
        $validated['protocol'] = $validated['protocol'] ?? 'http';
        $validated['agent_id'] = $agent->id;

        // Check if this agent already has this subdomain
        if (! empty($validated['subdomain'])) {
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

    /**
     * Create a magic login token for dashboard access
     *
     * @throws \Throwable
     */
    public function createLoginToken(Request $request)
    {
        // Comes from AgentAuthentication middleware
        $agent = $request->agent;

        // Generate a secure random token
        $token = bin2hex(random_bytes(32));

        $minutes = 60;

        $loginUrl = DB::transaction(function () use ($agent, $token, $minutes) {
            AgentLoginToken::where('agent_id', $agent->id)->delete();

            $loginToken = AgentLoginToken::create([
                'agent_id' => $agent->id,
                'token' => $token,
                'expires_at' => now()->addMinutes($minutes),
            ]);

            return config('app.url').'/auth/magic/'.$loginToken->token;
        });

        return response()->json([
            'login_url' => $loginUrl,
            'expires_in' => $minutes * 60, // seconds
            'message' => 'Login token created successfully',
        ]);
    }

    /**
     * Get usage statistics for the agent's user
     */
    public function getUsageStats(Request $request)
    {
        $agent = $request->agent;
        $user = $agent->user;

        $usageService = app(\App\Services\UsageTrackingService::class);
        $stats = $usageService->getUsageStats($user);

        return response()->json($stats);
    }
}

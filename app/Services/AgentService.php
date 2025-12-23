<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentService
{
    /**
     * Register a new agent.
     */
    public function registerAgent(User $user, array $data): array
    {
        // Generate API credentials
        $credentials = $this->generateApiCredentials();

        $agent = $user->agents()->create([
            'name' => $data['name'] ?? 'Agent '.Str::random(4),
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret_hash'],
            'status' => 'offline',
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Return agent and plain secret separately
        return [
            'agent' => $agent,
            'plain_secret' => $credentials['api_secret'],
        ];
    }

    /**
     * Generate secure API credentials.
     */
    public function generateApiCredentials(): array
    {
        $apiKey = 'pk_'.Str::random(32);
        $apiSecret = 'sk_'.Str::random(48);

        return [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'api_secret_hash' => bcrypt($apiSecret),
        ];
    }

    /**
     * Update agent status.
     */
    public function updateAgentStatus(Agent $agent, string $status): void
    {
        $agent->update([
            'status' => $status,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Get agent configuration for connection.
     */
    public function getAgentConfig(Agent $agent): array
    {
        return [
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'server_url' => config('portex.server_url'),
            'websocket_url' => config('portex.websocket_url'),
            'tunnels' => $agent->tunnels()
                ->active()
                ->get()
                ->map(function ($tunnel) {
                    return [
                        'id' => $tunnel->id,
                        'subdomain' => $tunnel->subdomain,
                        'local_port' => $tunnel->local_port,
                        'protocol' => $tunnel->protocol,
                        'auth_enabled' => $tunnel->auth_enabled,
                    ];
                }),
        ];
    }

    /**
     * Validate agent API credentials.
     */
    public function validateCredentials(string $apiKey, string $apiSecret): ?Agent
    {
        $agent = Agent::firstWhere('api_key', $apiKey);

        if (! $agent) {
            return null;
        }

        if (! Hash::check($apiSecret, $agent->api_secret)) {
            return null;
        }

        return $agent;
    }

    /**
     * Regenerate agent API credentials.
     */
    public function regenerateCredentials(Agent $agent): array
    {
        $credentials = $this->generateApiCredentials();

        $agent->update([
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret_hash'],
        ]);

        return [
            'agent' => $agent->fresh(),
            'plain_secret' => $credentials['api_secret'],
        ];
    }

    /**
     * Record agent heartbeat.
     */
    public function heartbeat(Agent $agent): void
    {
        $this->updateAgentStatus($agent, 'online');

        // Track usage time for active tunnels
        $activeTunnels = $agent->tunnels()->where('status', 'active')->get();
        if ($activeTunnels->count() > 0) {
            $usageService = app(\App\Services\UsageTrackingService::class);

            foreach ($activeTunnels as $tunnel) {
                // Heartbeat happens every 10 seconds
                $usageService->trackUsageTime($tunnel, 10);
            }
        }
    }
}

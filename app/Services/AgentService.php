<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Str;

class AgentService
{
    /**
     * Register a new agent.
     */
    public function registerAgent(User $user, array $data): Agent
    {
        // Generate API credentials
        $credentials = $this->generateApiCredentials();

        $agent = $user->agents()->create([
            'name' => $data['name'] ?? 'Agent ' . Str::random(4),
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret_hash'],
            'status' => 'offline',
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Return agent with plain API secret (only time it's visible)
        $agent->plain_api_secret = $credentials['api_secret'];

        return $agent;
    }

    /**
     * Generate secure API credentials.
     */
    public function generateApiCredentials(): array
    {
        $apiKey = 'pk_' . Str::random(32);
        $apiSecret = 'sk_' . Str::random(48);

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
        $agent = Agent::where('api_key', $apiKey)->first();

        if (!$agent) {
            return null;
        }

        if (!\Illuminate\Support\Facades\Hash::check($apiSecret, $agent->api_secret)) {
            return null;
        }

        return $agent;
    }

    /**
     * Regenerate agent API credentials.
     */
    public function regenerateCredentials(Agent $agent): Agent
    {
        $credentials = $this->generateApiCredentials();

        $agent->update([
            'api_key' => $credentials['api_key'],
            'api_secret' => $credentials['api_secret_hash'],
        ]);

        // Return agent with plain API secret
        $agent->plain_api_secret = $credentials['api_secret'];

        return $agent->fresh();
    }

    /**
     * Record agent heartbeat.
     */
    public function heartbeat(Agent $agent): void
    {
        $this->updateAgentStatus($agent, 'online');
    }
}

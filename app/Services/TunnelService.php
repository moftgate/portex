<?php

namespace App\Services;

use App\Models\Tunnel;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TunnelService
{
    /**
     * Create a new tunnel.
     */
    public function createTunnel(User $user, array $data): Tunnel
    {
        // Generate subdomain if not provided
        if (empty($data['subdomain'])) {
            $data['subdomain'] = $this->assignSubdomain();
        } else {
            // Validate subdomain availability
            if (!$this->validateSubdomain($data['subdomain'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subdomain' => ['Subdomain is already taken'],
                ]);
            }
        }

        // Hash auth password if provided
        if (!empty($data['auth_password'])) {
            $data['auth_password'] = bcrypt($data['auth_password']);
        }

        $tunnel = $user->tunnels()->create($data);

        // Notify Go server about new tunnel
        $this->notifyServer($tunnel, 'create');

        return $tunnel;
    }

    /**
     * Update an existing tunnel.
     */
    public function updateTunnel(Tunnel $tunnel, array $data): Tunnel
    {
        // Hash auth password if provided and changed
        if (!empty($data['auth_password'])) {
            $data['auth_password'] = bcrypt($data['auth_password']);
        }

        $tunnel->update($data);

        // Notify Go server about tunnel update
        $this->notifyServer($tunnel, 'update');

        return $tunnel->fresh();
    }

    /**
     * Delete a tunnel.
     */
    public function deleteTunnel(Tunnel $tunnel): bool
    {
        // Notify Go server about tunnel deletion
        $this->notifyServer($tunnel, 'delete');

        return $tunnel->delete();
    }

    /**
     * Generate a unique random subdomain.
     */
    public function assignSubdomain(): string
    {
        do {
            // Generate random 8-character subdomain
            $subdomain = Str::lower(Str::random(8));
        } while (!$this->validateSubdomain($subdomain));

        return $subdomain;
    }

    /**
     * Validate subdomain availability.
     */
    public function validateSubdomain(string $subdomain): bool
    {
        // Check if subdomain is already taken
        return !Tunnel::where('subdomain', $subdomain)->exists();
    }

    /**
     * Notify Go server about tunnel changes.
     */
    public function notifyServer(Tunnel $tunnel, string $action): void
    {
        $serverUrl = config('portex.server_url');
        $serverApiKey = config('portex.server_api_key');

        if (empty($serverUrl) || empty($serverApiKey)) {
            return; // Server not configured yet
        }

        logger()->debug('Notifying Go server about tunnel change', [
            'url' => "{$serverUrl}/api/internal/tunnels/{$action}",
            'tunnel_id' => $tunnel->id
        ]);

        try {
            Http::withHeaders([
                'Authorization' => "Bearer {$serverApiKey}",
            ])->post("{$serverUrl}/api/internal/tunnels/{$action}", [
                'tunnel_id' => $tunnel->id,
                'subdomain' => $tunnel->subdomain,
                'custom_domain' => $tunnel->custom_domain,
                'agent_id' => $tunnel->agent_id,
                'status' => $tunnel->status,
                'protocol' => $tunnel->protocol,
                'auth_enabled' => $tunnel->auth_enabled,
                'auth_username' => $tunnel->auth_username,
                'auth_password' => $tunnel->auth_password,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the operation
            logger()->error('Failed to notify server about tunnel change', [
                'tunnel_id' => $tunnel->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Activate a tunnel.
     */
    public function activateTunnel(Tunnel $tunnel): Tunnel
    {
        $tunnel->update(['status' => 'active']);
        $this->notifyServer($tunnel, 'activate');

        return $tunnel->fresh();
    }

    /**
     * Deactivate a tunnel.
     */
    public function deactivateTunnel(Tunnel $tunnel): Tunnel
    {
        $tunnel->update(['status' => 'inactive']);
        $this->notifyServer($tunnel, 'deactivate');

        return $tunnel->fresh();
    }
}

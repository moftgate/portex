<?php

use App\Models\Agent;
use App\Services\TunnelService;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public string $name = '';
    public string $subdomain = '';
    public int $local_port = 3000;
    public string $protocol = 'http';
    public ?string $agent_id = null;
    public bool $auth_enabled = false;
    public string $auth_username = '';
    public string $auth_password = '';

    public function mount(): void
    {
        // Auto-generate subdomain
        $this->subdomain = app(TunnelService::class)->assignSubdomain();
    }

    public function save(TunnelService $tunnelService): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|alpha_dash',
            'local_port' => 'required|integer|min:1|max:65535',
            'protocol' => 'required|in:http,https,tcp',
            'agent_id' => 'nullable|exists:agents,id',
            'auth_enabled' => 'boolean',
            'auth_username' => 'required_if:auth_enabled,true|nullable|string',
            'auth_password' => 'required_if:auth_enabled,true|nullable|string',
        ]);

        try {
            $tunnel = $tunnelService->createTunnel(auth()->user(), $validated);

            $this->success("Tunnel '{$tunnel->name}' created successfully!", position: 'toast-bottom');
            $this->redirect('/tunnels');
        } catch (\Exception $e) {
            $this->error($e->getMessage(), position: 'toast-bottom');
        }
    }

    public function generateSubdomain(TunnelService $tunnelService): void
    {
        $this->subdomain = $tunnelService->assignSubdomain();
    }

    public function with(): array
    {
        return [
            'agents' => Agent::where('user_id', auth()->id())->get(),
            'protocolOptions' => [['id' => 'http', 'name' => 'HTTP'], ['id' => 'https', 'name' => 'HTTPS'], ['id' => 'tcp', 'name' => 'TCP']],
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold" style="color: var(--color-neutral);">Create Tunnel</h1>
        <p class="text-sm text-gray-600 mt-1">Expose your local service to the internet</p>
    </div>

    <!-- Form -->
    <form wire:submit="save">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <!-- Basic Info -->
            <div class="grid gap-6 md:grid-cols-2 mb-6">
                <!-- Tunnel Name -->
                <div>
                    <label for="name" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                        Tunnel Name
                    </label>
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        style="--tw-ring-color: var(--color-secondary);" placeholder="My Awesome App" required />
                </div>

                <!-- Subdomain -->
                <div>
                    <label for="subdomain" class="block text-sm font-medium mb-1.5"
                        style="color: var(--color-neutral);">
                        Subdomain
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="subdomain" wire:model="subdomain"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                            style="--tw-ring-color: var(--color-secondary);" placeholder="myapp" required />
                        <button type="button" wire:click="generateSubdomain"
                            class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                            title="Generate random">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        Public URL: https://{{ $subdomain }}.{{ config('portex.tunnel_domain') }}
                    </div>
                </div>

                <!-- Local Port -->
                <div>
                    <label for="local_port" class="block text-sm font-medium mb-1.5"
                        style="color: var(--color-neutral);">
                        Local Port
                    </label>
                    <input type="number" id="local_port" wire:model="local_port" min="1" max="65535"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        style="--tw-ring-color: var(--color-secondary);" required />
                </div>

                <!-- Protocol -->
                <div>
                    <label for="protocol" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                        Protocol
                    </label>
                    <select id="protocol" wire:model="protocol"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        style="--tw-ring-color: var(--color-secondary);" required>
                        @foreach ($protocolOptions as $option)
                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Agent -->
                <div class="md:col-span-2">
                    <label for="agent_id" class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">
                        Agent (Optional)
                    </label>
                    <select id="agent_id" wire:model="agent_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                        style="--tw-ring-color: var(--color-secondary);">
                        <option value="">Select an agent</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 my-6"></div>

            <!-- Authentication -->
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="auth_enabled" wire:model.live="auth_enabled"
                        class="w-4 h-4 border-gray-300 rounded focus:ring-2"
                        style="color: var(--color-primary); --tw-ring-color: var(--color-primary);" />
                    <label for="auth_enabled" class="ml-2 text-sm font-medium" style="color: var(--color-neutral);">
                        Enable Basic Authentication
                    </label>
                </div>

                @if ($auth_enabled)
                    <div class="grid gap-6 md:grid-cols-2 ml-6">
                        <div>
                            <label for="auth_username" class="block text-sm font-medium mb-1.5"
                                style="color: var(--color-neutral);">
                                Username
                            </label>
                            <input type="text" id="auth_username" wire:model="auth_username"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                style="--tw-ring-color: var(--color-secondary);" required />
                        </div>
                        <div>
                            <label for="auth_password" class="block text-sm font-medium mb-1.5"
                                style="color: var(--color-neutral);">
                                Password
                            </label>
                            <input type="password" id="auth_password" wire:model="auth_password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                                style="--tw-ring-color: var(--color-secondary);" required />
                        </div>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('tunnels.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                    style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
                    onmouseout="this.style.opacity='1'" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Create Tunnel</span>
                    <span wire:loading wire:target="save">Creating...</span>
                </button>
            </div>
        </div>
    </form>
</div>

<?php

use App\Models\Agent;
use App\Models\Tunnel;
use App\Models\TunnelRequest;
use App\Services\UsageTrackingService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $user = auth()->user();
        $usageService = app(UsageTrackingService::class);

        return [
            'activeTunnelsCount' => Tunnel::query()->when(is_user(), fn(Tunnel|Builder $query) => $query->where('user_id', $user->id))->where('status', 'active')->count(),

            'onlineAgentsCount' => Agent::query()->when(is_user(), fn(Agent|Builder $query) => $query->where('user_id', $user->id))->where('status', 'online')->count(),

            'todayRequestsCount' => TunnelRequest::query()
                ->whereHas('tunnel', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereDate('created_at', today())
                ->count(),

            'recentTunnels' => Tunnel::query()->when(is_user(), fn(Tunnel|Builder $query) => $query->where('user_id', $user->id))->with('agent')->latest()->take(5)->get(),

            'usageStats' => $usageService->getUsageStats($user),
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold" style="color: var(--color-neutral);">Dashboard</h1>
        <p class="text-sm text-gray-600 mt-1">Overview of your tunnels and agents</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-6 md:grid-cols-3 mb-8">
        <!-- Active Tunnels -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600 mb-1">Active Tunnels</div>
                    <div class="text-3xl font-semibold" style="color: var(--color-neutral);">{{ $activeTunnelsCount }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     style="background-color: rgba(249, 115, 22, 0.1);">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Online Agents -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600 mb-1">Online Agents</div>
                    <div class="text-3xl font-semibold" style="color: var(--color-neutral);">{{ $onlineAgentsCount }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                     style="background-color: rgba(37, 99, 235, 0.1);">
                    <svg class="w-6 h-6" style="color: var(--color-secondary);" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Requests Today -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600 mb-1">Requests Today</div>
                    <div class="text-3xl font-semibold" style="color: var(--color-neutral);">
                        {{ number_format($todayRequestsCount) }}</div>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Section -->
    <div class="grid gap-6 md:grid-cols-2 mb-8">
        <!-- Daily Usage -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold" style="color: var(--color-neutral);">Daily Usage Limit</h3>
                @if ($usageStats['is_premium'])
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        ✨ Premium
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Free Tier
                    </span>
                @endif
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Time Used Today</span>
                        <span class="font-medium text-gray-900">{{ $usageStats['used_formatted'] }} /
                            {{ $usageStats['limit_formatted'] }}</span>
                    </div>
                    @if ($usageStats['is_premium'])
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500">Unlimited tunnel time for Premium users</div>
                    @else
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div
                                class="h-2 rounded-full transition-all duration-500 {{ $usageStats['percentage_used'] > 90 ? 'bg-red-500' : ($usageStats['percentage_used'] > 75 ? 'bg-orange-500' : 'bg-blue-600') }}"
                                style="width: {{ min(100, $usageStats['percentage_used']) }}%"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-xs text-gray-500">{{ $usageStats['remaining_formatted'] }}
                                remaining</span>
                            <span
                                class="text-xs font-medium {{ $usageStats['percentage_used'] > 90 ? 'text-red-600' : 'text-gray-600' }}">{{ $usageStats['percentage_used'] }}%</span>
                        </div>
                    @endif
                </div>

                @if (!$usageStats['is_premium'])
                    <div class="p-4 rounded-lg bg-blue-50 border border-blue-100 mt-4">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Need more time?</h4>
                                <p class="text-xs text-blue-600 mt-1">Upgrade to Premium for unlimited tunnel duration
                                    and custom subdomains.</p>
                                <a href="#"
                                   class="mt-2 text-xs font-semibold text-blue-800 hover:text-blue-900 flex items-center gap-1">
                                    Upgrade Soon
                                   <x-icon name="fas.hourglass" class="w-3"/>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bandwidth & Health -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="font-semibold mb-4" style="color: var(--color-neutral);">Traffic Overview</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-center">
                    <div class="text-xs text-gray-500 uppercase font-medium mb-1">Total Bandwidth</div>
                    <div class="text-xl font-bold text-gray-900">{{ $usageStats['total_bandwidth_formatted'] }}</div>
                </div>
                <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-center">
                    <div class="text-xs text-gray-500 uppercase font-medium mb-1">System Health</div>
                    <div class="text-xl font-bold text-green-600">Stable</div>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">WebSocket Server</span>
                    </div>
                    <span
                        class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Connected</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">API Gateway</span>
                    </div>
                    <span
                        class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Operational</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tunnels -->
    <div class="bg-white rounded-lg border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold" style="color: var(--color-neutral);">Recent Tunnels</h2>
            <a href="{{ route('tunnels.index') }}" class="text-sm font-medium transition-colors"
               style="color: var(--color-secondary);" onmouseover="this.style.opacity='0.8'"
               onmouseout="this.style.opacity='1'">
                View All →
            </a>
        </div>

        @if ($recentTunnels->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Agent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Public URL
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($recentTunnels as $tunnel)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium" style="color: var(--color-neutral);">{{ $tunnel->name }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $tunnel->subdomain }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($tunnel->agent)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-2 h-2 rounded-full {{ $tunnel->agent->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}">
                                        </div>
                                        <span class="text-sm">{{ $tunnel->agent->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No agent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tunnel->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($tunnel->status) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ $tunnel->public_url }}" target="_blank"
                                   class="text-sm transition-colors" style="color: var(--color-secondary);"
                                   onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    {{ $tunnel->public_url }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 mb-4">No tunnels yet</p>
                <a href="{{ route('tunnels.create') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                   style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
                   onmouseout="this.style.opacity='1'">
                    Create Tunnel
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-6 md:grid-cols-2">
        <!-- Create Tunnel -->
       {{-- <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: rgba(249, 115, 22, 0.1);">
                    <svg class="w-6 h-6" style="color: var(--color-primary);" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
              <div class="flex-1">
                    <h3 class="font-semibold mb-1" style="color: var(--color-neutral);">Create New Tunnel</h3>
                    <p class="text-sm text-gray-600 mb-4">Expose your local service to the internet</p>
                    <a href="{{ route('tunnels.create') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                       style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
                       onmouseout="this.style.opacity='1'">
                        Create
                    </a>
                </div>
            </div>
        </div>--}}

        <!-- Register Agent -->
        {{--<div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background-color: rgba(37, 99, 235, 0.1);">
                    <svg class="w-6 h-6" style="color: var(--color-secondary);" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold mb-1" style="color: var(--color-neutral);">Register New Agent</h3>
                    <p class="text-sm text-gray-600 mb-4">Add a new agent to connect tunnels</p>
                    <a href="{{ route('agents.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md border transition-colors"
                       style="border-color: var(--color-secondary); color: var(--color-secondary);"
                       onmouseover="this.style.backgroundColor='rgba(37, 99, 235, 0.05)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        Register
                    </a>
                </div>
            </div>
        </div>--}}
    </div>
</div>

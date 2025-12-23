<?php

use App\Models\Agent;
use App\Services\AgentService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';
    public bool $showCredentials = false;
    public ?string $newApiKey = null;
    public ?string $newApiSecret = null;

    public function headers(): array
    {
        return [['key' => 'name', 'label' => 'Name'], ['key' => 'status', 'label' => 'Status'], ['key' => 'last_seen_at', 'label' => 'Last Seen'], ['key' => 'tunnels_count', 'label' => 'Tunnels']];
    }

    public function agents(): LengthAwarePaginator
    {
        return Agent::query()
            ->when(is_user(), fn(Agent|Builder $q) => $q->where('user_id', auth()->id()))
            ->withCount('tunnels')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);
    }

    public function createAgent(AgentService $agentService): void
    {
        $agent = $agentService->registerAgent(auth()->user(), [
            'name' => 'Agent ' . now()->format('Y-m-d H:i'),
        ]);

        $this->newApiKey = $agent->api_key;
        $this->newApiSecret = $agent->plain_api_secret;
        $this->showCredentials = true;

        $this->success('Agent created successfully!', position: 'toast-bottom');
    }

    public function delete($id): void
    {
        $agent = Agent::where('user_id', auth()->id())->findOrFail($id);
        $agent->delete();

        $this->success("Agent '{$agent->name}' deleted successfully.", position: 'toast-bottom');
    }

    public function with(): array
    {
        return [
            'agents' => $this->agents(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold" style="color: var(--color-neutral);">Agents</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your connected agents</p>
        </div>
        {{--<button wire:click="createAgent"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'" wire:loading.attr="disabled">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span wire:loading.remove wire:target="createAgent">Register Agent</span>
            <span wire:loading wire:target="createAgent">Creating...</span>
        </button>--}}
    </div>

    <!-- Search -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" wire:model.live.debounce="search" placeholder="Search agents..."
                   class="w-full md:w-96 px-4 py-2 pl-10 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                   style="--tw-ring-color: var(--color-secondary);"/>
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Credentials Modal -->
    @if ($showCredentials)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6">
                <h2 class="text-xl font-semibold mb-2" style="color: var(--color-neutral);">Agent Credentials</h2>
                <p class="text-sm text-gray-600 mb-6">Save these credentials - they won't be shown again!</p>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-yellow-800">
                            <strong>Important:</strong> Copy these credentials now. You won't be able to see the API
                            secret again.
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">API
                            Key</label>
                        <div class="flex gap-2">
                            <input type="text" value="{{ $newApiKey }}" readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900"/>
                            <button onclick="navigator.clipboard.writeText('{{ $newApiKey }}')"
                                    class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                                    title="Copy">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--color-neutral);">API
                            Secret</label>
                        <div class="flex gap-2">
                            <input type="text" value="{{ $newApiSecret }}" readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900"/>
                            <button onclick="navigator.clipboard.writeText('{{ $newApiSecret }}')"
                                    class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
                                    title="Copy">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="text-sm font-semibold mb-2" style="color: var(--color-neutral);">Agent
                            Configuration:
                        </div>
                        <code class="text-xs text-gray-700">
                            portex auth --api-key {{ $newApiKey }} --api-secret {{ $newApiSecret }}
                        </code>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button wire:click="$set('showCredentials', false)"
                            class="px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                            style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
                            onmouseout="this.style.opacity='1'">
                        I've Saved the Credentials
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Last
                        Seen
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                        Tunnels
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse ($agents as $agent)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium" style="color: var(--color-neutral);">{{ $agent->name }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $agent->api_key }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-2 h-2 rounded-full {{ $agent->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}">
                                </div>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $agent->status === 'online' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($agent->status) }}
                                    </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if ($agent->last_seen_at)
                                <span class="text-sm text-gray-600">{{ $agent->last_seen_human }}</span>
                            @else
                                <span class="text-sm text-gray-400">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $agent->tunnels_count }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="delete('{{ $agent->id }}')"
                                    wire:confirm="Are you sure? All associated tunnels will be disconnected."
                                    class="text-sm text-red-600 hover:text-red-700 transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <p class="text-gray-500 mb-4">No agents found</p>
                            {{--<button wire:click="createAgent"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
                                    style="background-color: var(--color-primary);"
                                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                Register your first agent
                            </button>--}}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($agents->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $agents->links() }}
            </div>
        @endif
    </div>
</div>

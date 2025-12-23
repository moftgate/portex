<?php

use App\Models\Tunnel;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use WithPagination, Toast;

    public string $search = '';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];

    public function headers(): array
    {
        return [['key' => 'name', 'label' => 'Name'], ['key' => 'subdomain', 'label' => 'Subdomain'], ['key' => 'status', 'label' => 'Status'], ['key' => 'agent.name', 'label' => 'Agent'], ['key' => 'protocol', 'label' => 'Protocol'], ['key' => 'local_port', 'label' => 'Port']];
    }

    public function tunnels(): LengthAwarePaginator
    {
        return Tunnel::query()
            ->when(is_user(), fn(Tunnel|Builder $q) => $q->where('user_id', auth()->id()))
            ->with('agent')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")->orWhere('subdomain', 'like', "%{$this->search}%");
                });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
    }

    public function delete($id): void
    {
        if (is_user()) {
            return;
        }

        $tunnel = Tunnel::where('user_id', auth()->id())->findOrFail($id);
        $tunnel->delete();

        $this->success("Tunnel '{$tunnel->name}' deleted successfully.", position: 'toast-bottom');
    }

    public function with(): array
    {
        return [
            'tunnels' => $this->tunnels(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold" style="color: var(--color-neutral);">Tunnels</h1>
            <p class="text-sm text-gray-600 mt-1">Manage your active tunnels</p>
        </div>
        {{-- <a href="{{ route('tunnels.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-md transition-colors"
            style="background-color: var(--color-primary);" onmouseover="this.style.opacity='0.9'"
            onmouseout="this.style.opacity='1'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Tunnel
        </a> --}}
    </div>

    <!-- Search -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" wire:model.live.debounce="search" placeholder="Search tunnels..."
                class="w-full md:w-96 px-4 py-2 pl-10 border border-gray-300 rounded-md bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-colors"
                style="--tw-ring-color: var(--color-secondary);" />
            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Subdomain
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Agent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                            Protocol
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Port
                        </th>

                        @if (is_admin())
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($tunnels as $tunnel)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="font-medium" style="color: var(--color-neutral);">{{ $tunnel->name }}
                                    </div>
                                    @if ($tunnel->pin)
                                        <x-icon name="o-lock-closed" class="w-3.5 h-3.5 text-orange-500"
                                            title="Protected with PIN" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ $tunnel->public_url }}" target="_blank" class="text-sm transition-colors"
                                    style="color: var(--color-secondary);" onmouseover="this.style.opacity='0.8'"
                                    onmouseout="this.style.opacity='1'">
                                    {{ $tunnel->subdomain }}
                                </a>
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
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ strtoupper($tunnel->protocol) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-sm text-gray-600">{{ $tunnel->local_port }}</code>
                            </td>
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-3">
                                <a href="{{ route('tunnels.show', $tunnel->id) }}"
                                    class="text-sm font-medium transition-colors" style="color: var(--color-primary);"
                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    Inspect
                                </a>

                                @if (is_admin())
                                    <button wire:click="delete('{{ $tunnel->id }}')"
                                        wire:confirm="Are you sure you want to delete this tunnel?"
                                        class="text-sm text-red-600 hover:text-red-700 transition-colors">
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-gray-500 mb-4">No tunnels found</p>

                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($tunnels->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tunnels->links() }}
            </div>
        @endif
    </div>
</div>

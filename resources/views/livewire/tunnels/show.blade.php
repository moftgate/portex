<?php

use App\Models\Tunnel;
use App\Models\TunnelRequest;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public Tunnel $tunnel;
    public ?string $selectedRequestId = null;

    public function mount(Tunnel $tunnel)
    {
        if (!is_admin() && $this->tunnel->user_id !== auth()->id()) {
            abort(404);
        }

        $this->tunnel = $tunnel;
    }

    public function selectRequest($id)
    {
        $this->selectedRequestId = $id;
    }

    public function getRequestsProperty()
    {
        return $this->tunnel->requests()->latest()->paginate(20);
    }

    public function getSelectedRequestProperty()
    {
        return $this->selectedRequestId ? TunnelRequest::find($this->selectedRequestId) : null;
    }

    public function with()
    {
        return [
            'requests' => $this->requests,
            'selectedRequest' => $this->selectedRequest,
        ];
    }
}; ?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('tunnels.index') }}"
                               class="text-sm font-medium text-gray-500 hover:text-gray-700">Tunnels</a>
                        </li>
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <li>
                            <span class="text-sm font-bold"
                                  style="color: var(--color-neutral);">{{ $tunnel->name }}</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold tracking-tight" style="color: var(--color-neutral);">Tunnel Traffic</h1>
                <p class="text-sm text-gray-600 mt-1">Real-time status and request inspection for <span
                        class="font-mono bg-gray-100 px-1 rounded">{{ $tunnel->public_url }}</span></p>
            </div>

            <div class="flex items-center gap-3">
                <div
                    class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-2 {{ $tunnel->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    <div
                        class="w-2 h-2 rounded-full {{ $tunnel->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}">
                    </div>
                    {{ ucfirst($tunnel->status) }}
                </div>
            </div>
        </div>

        <!-- Requests Inspect Area -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[700px]">
            <!-- List (Sol) -->
            <div
                class="lg:col-span-4 bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col shadow-sm">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Live Requests</h3>
                    <div wire:loading wire:target="selectRequest">
                        <x-loading class="loading-xs"/>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-gray-100" wire:poll.3s>
                    @forelse($requests as $request)
                        <button wire:click="selectRequest('{{ $request->id }}')"
                                class="w-full text-left px-4 py-3 transition-colors hover:bg-gray-50 {{ $selectedRequestId === $request->id ? 'bg-orange-50 ring-1 ring-inset ring-orange-200' : '' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="text-xs font-mono font-bold @if ($request->status_code >= 400) text-red-600 @elseif($request->status_code >= 300) text-blue-600 @else text-green-600 @endif">
                                    {{ $request->status_code }} {{ $request->method }}
                                </span>
                                <span
                                    class="text-[10px] text-gray-400">{{ $request->created_at->format('H:i:s') }}</span>
                            </div>
                            <div class="text-sm font-medium truncate text-gray-700 font-mono">
                                {{ $request->path }}
                            </div>
                            <div class="mt-1 flex items-center gap-2 text-[10px] text-gray-500">
                                <span>{{ $request->response_time_ms }}ms</span>
                                <span>•</span>
                                <span>{{ $request->ip_address }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <p class="text-xs text-gray-500">Waiting for requests...</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-2 border-t border-gray-100">
                    {{ $requests->links(data: ['scrollTo' => false]) }}
                </div>
            </div>

            <!-- Detail (Sağ) -->
            <div
                class="lg:col-span-8 bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col shadow-sm">
                @if ($selectedRequest)
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                        <div class="flex items-center gap-3">
                            <span
                                class="px-2 py-1 bg-gray-800 text-white rounded text-xs font-bold font-mono">{{ $selectedRequest->method }}</span>
                            <h2 class="text-sm font-mono font-bold text-gray-800 truncate">{{ $selectedRequest->path }}
                            </h2>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $selectedRequest->created_at->format('M d, Y H:i:s.v') }}
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <!-- Summary Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-500 font-bold mb-1">Status</div>
                                <div
                                    class="text-lg font-bold @if ($selectedRequest->status_code >= 400) text-red-600 @else text-green-600 @endif">
                                    {{ $selectedRequest->status_code }}
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-500 font-bold mb-1">Duration</div>
                                <div class="text-lg font-bold text-gray-800">
                                    {{ $selectedRequest->response_time_ms }} ms
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-500 font-bold mb-1">IP Address</div>
                                <div class="text-lg font-bold text-gray-800">
                                    {{ $selectedRequest->ip_address }}
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-500 font-bold mb-1">Agent</div>
                                <div class="text-sm font-bold text-gray-800 truncate mt-1">
                                    {{ Str::limit($selectedRequest->user_agent, 20) }}
                                </div>
                            </div>
                        </div>

                        <!-- Full User Agent -->
                        <div>
                            <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">User Agent</h4>
                            <div
                                class="bg-gray-50 rounded-lg p-4 font-mono text-xs text-gray-700 break-all border border-gray-100">
                                {{ $selectedRequest->user_agent }}
                            </div>
                        </div>

                        <!-- Headers (Placeholder for now) -->
                        <div>
                            <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Request Headers</h4>
                            <div
                                class="bg-gray-800 rounded-lg p-4 font-mono text-xs text-green-400 space-y-1 overflow-x-auto shadow-inner">
                                <div><span class="text-blue-400">Host:</span> {{ $tunnel->subdomain }}.portex.io</div>
                                <div><span class="text-blue-400">X-Forwarded-For:</span>
                                    {{ $selectedRequest->ip_address }}</div>
                                <div><span class="text-blue-400">User-Agent:</span>
                                    {{ Str::limit($selectedRequest->user_agent, 50) }}</div>
                                <div><span class="text-blue-400">Accept:</span> */*</div>
                                <div><span class="text-blue-400">X-Portex-ID:</span> {{ $selectedRequest->id }}</div>
                            </div>
                        </div>

                        <div class="p-4 bg-orange-50 border border-orange-100 rounded-lg flex items-center gap-3">
                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs text-orange-800 font-medium">Body inspection and custom headers will be
                                available in the next update.</p>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-12 text-center text-gray-500">
                        <svg class="w-20 h-20 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-lg font-medium" style="color: var(--color-neutral);">Inspect a request</h3>
                        <p class="text-sm max-w-xs mx-auto mt-2">Select a request from the list on the left to view
                            detailed timing, headers and more.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

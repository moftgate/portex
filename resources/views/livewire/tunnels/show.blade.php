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

    public function replayRequest($id)
    {
        $request = TunnelRequest::findOrFail($id);
        $tunnel = $request->tunnel;

        if (!$tunnel) {
            return;
        }

        $url = $tunnel->public_url . $request->path;

        $headers = collect($request->request_headers)
            ->filter(function ($v, $k) {
                $lowered = strtolower($k);
                return !in_array($lowered, ['host', 'content-length', 'connection', 'upgrade', 'x-real-ip', 'x-forwarded-for', 'x-forwarded-proto', 'accept-encoding']);
            })
            ->toArray();

        $http = \Illuminate\Support\Facades\Http::withHeaders($headers);

        try {
            $response = match (strtoupper($request->method)) {
                'GET' => $http->get($url),
                'POST' => $http->withBody($request->request_body, $request->request_headers['content-type'] ?? ($request->request_headers['Content-Type'] ?? 'application/json'))->post($url),
                'PUT' => $http->withBody($request->request_body, $request->request_headers['content-type'] ?? ($request->request_headers['Content-Type'] ?? 'application/json'))->put($url),
                'PATCH' => $http->withBody($request->request_body, $request->request_headers['content-type'] ?? ($request->request_headers['Content-Type'] ?? 'application/json'))->patch($url),
                'DELETE' => $http->delete($url),
                default => $http->send($request->method, $url),
            };

            $this->dispatch('request-replayed', ['status' => $response->status()]);
        } catch (\Exception $e) {
            $this->dispatch('request-failed', ['error' => $e->getMessage()]);
        }
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
                                clip-rule="evenodd" />
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
                        <x-loading class="loading-xs" />
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-gray-100" wire:poll.3s>
                    @forelse($requests as $request)
                        <button wire:click="selectRequest('{{ $request->id }}')"
                            class="w-full text-left px-4 py-3 transition-colors hover:bg-gray-50 {{ $selectedRequestId === $request->id ? 'bg-orange-50 ring-1 ring-inset ring-orange-200' : '' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-black tracking-wider border
                                    @if ($request->method === 'GET') bg-blue-50 text-blue-600 border-blue-100
                                    @elseif($request->method === 'POST') bg-green-50 text-green-600 border-green-100
                                    @elseif($request->method === 'PUT' || $request->method === 'PATCH') bg-orange-50 text-orange-600 border-orange-100
                                    @elseif($request->method === 'DELETE') bg-red-50 text-red-600 border-red-100
                                    @else bg-gray-50 text-gray-600 border-gray-100 @endif">
                                    {{ $request->method }}
                                </span>
                                <span
                                    class="text-xs font-mono font-bold @if ($request->status_code >= 500) text-red-600 @elseif($request->status_code >= 400) text-orange-600 @elseif($request->status_code >= 300) text-blue-600 @else text-green-600 @endif">
                                    {{ $request->status_code }}
                                </span>
                                <span
                                    class="text-[10px] text-gray-400 ml-auto">{{ $request->created_at->format('H:i:s') }}</span>
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
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
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
                        <div class="flex items-center gap-3">
                            <button wire:click="replayRequest('{{ $selectedRequest->id }}')"
                                wire:loading.attr="disabled"
                                class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 rounded-lg transition-colors border border-blue-100">
                                <svg wire:loading.remove wire:target="replayRequest" class="w-3.5 h-3.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <svg wire:loading wire:target="replayRequest"
                                    class="animate-spin h-3.5 w-3.5 text-blue-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Replay</span>
                            </button>
                            <div class="text-xs text-gray-500">
                                {{ $selectedRequest->created_at->format('M d, Y H:i:s.v') }}
                            </div>
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

                        <!-- Headers -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Request Headers</h4>
                                <div
                                    class="bg-gray-800 rounded-lg p-4 font-mono text-[10px] text-gray-300 space-y-1 overflow-x-auto shadow-inner max-h-[300px]">
                                    @foreach ($selectedRequest->request_headers ?? [] as $key => $values)
                                        <div class="flex gap-2">
                                            <span class="text-blue-400 shrink-0">{{ $key }}:</span>
                                            <span
                                                class="break-all">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Response Headers</h4>
                                <div
                                    class="bg-gray-800 rounded-lg p-4 font-mono text-[10px] text-gray-300 space-y-1 overflow-x-auto shadow-inner max-h-[300px]">
                                    @foreach ($selectedRequest->response_headers ?? [] as $key => $values)
                                        <div class="flex gap-2">
                                            <span class="text-green-400 shrink-0">{{ $key }}:</span>
                                            <span
                                                class="break-all">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Bodies -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Request Body</h4>
                                <div
                                    class="bg-gray-900 rounded-lg p-4 font-mono text-[10px] text-white overflow-x-auto shadow-xl min-h-[100px] max-h-[400px]">
                                    @if ($selectedRequest->request_body)
                                        @php
                                            $jsonBody = $selectedRequest->request_body;
                                            if (
                                                str_starts_with(trim($selectedRequest->request_body), '{') ||
                                                str_starts_with(trim($selectedRequest->request_body), '[')
                                            ) {
                                                $decoded = json_decode($selectedRequest->request_body);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    $jsonBody = json_encode(
                                                        $decoded,
                                                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                                                    );
                                                }
                                            }
                                        @endphp
                                        <pre class="whitespace-pre-wrap">{{ $jsonBody }}</pre>
                                    @else
                                        <span class="text-gray-600 italic">No Body</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Response Body</h4>
                                <div
                                    class="bg-gray-900 rounded-lg p-4 font-mono text-[10px] text-green-400 overflow-x-auto shadow-xl min-h-[100px] max-h-[400px]">
                                    @if ($selectedRequest->response_body)
                                        @php
                                            $jsonBody = $selectedRequest->response_body;
                                            if (
                                                str_starts_with(trim($selectedRequest->response_body), '{') ||
                                                str_starts_with(trim($selectedRequest->response_body), '[')
                                            ) {
                                                $decoded = json_decode($selectedRequest->response_body);
                                                if (json_last_error() === JSON_ERROR_NONE) {
                                                    $jsonBody = json_encode(
                                                        $decoded,
                                                        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
                                                    );
                                                }
                                            }
                                        @endphp
                                        <pre class="whitespace-pre-wrap">{{ $jsonBody }}</pre>
                                    @else
                                        <span class="text-gray-600 italic">No Body</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-12 text-center text-gray-500">
                        <svg class="w-20 h-20 mb-4 opacity-20" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium" style="color: var(--color-neutral);">Inspect a request</h3>
                        <p class="text-sm max-w-xs mx-auto mt-2">Select a request from the list on the left to view
                            detailed timing, headers and more.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('request-replayed', (event) => {
                alert('Request replayed! Remote server returned: ' + event[0].status);
            });

            Livewire.on('request-failed', (event) => {
                alert('Replay failed: ' + event[0].error);
            });
        });
    </script>
</div>

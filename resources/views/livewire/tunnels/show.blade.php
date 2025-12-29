<?php

use App\Models\Tunnel;
use App\Models\TunnelRequest;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component {
    use WithPagination;

    public Tunnel $tunnel;
    public ?string $selectedRequestId = null;
    public bool $showQrModal = false;
    public string $activeTab = 'traffic';
    public string $newIp = '';
    public array $allowedIps = [];

    public function mount(Tunnel $tunnel)
    {
        if (!is_admin() && $tunnel->user_id !== auth()->id()) {
            abort(404);
        }

        $this->tunnel = $tunnel;
        $this->allowedIps = $tunnel->allowed_ips ?? [];
    }

    public function addIp()
    {
        $this->validate([
            'newIp' => 'required|ip',
        ]);

        if (!in_array($this->newIp, $this->allowedIps)) {
            $this->allowedIps[] = $this->newIp;
            $this->saveIps();
        }

        $this->newIp = '';
    }

    public function removeIp($ip)
    {
        $this->allowedIps = array_values(array_filter($this->allowedIps, fn($i) => $i !== $ip));
        $this->saveIps();
    }

    protected function saveIps()
    {
        $this->tunnel->update([
            'allowed_ips' => $this->allowedIps,
        ]);

        $this->dispatch('ips-updated');
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
                <h1 class="text-2xl font-bold tracking-tight" style="color: var(--color-neutral);">Tunnel Details</h1>
                <p class="text-sm text-gray-600 mt-1">Manage and inspect <span
                        class="font-mono bg-gray-100 px-1 rounded">{{ $tunnel->public_url }}</span></p>

                <div class="mt-4 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button wire:click="$set('activeTab', 'traffic')"
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition-all {{ $activeTab === 'traffic' ? 'border-orange-500 text-orange-600 scale-105' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Traffic Inspect
                        </button>
                        <button wire:click="$set('activeTab', 'security')"
                            class="whitespace-nowrap pb-4 px-1 border-b-2 font-bold text-sm transition-all {{ $activeTab === 'security' ? 'border-orange-500 text-orange-600 scale-105' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Access Control
                        </button>
                    </nav>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button @click="$dispatch('open-qr-modal')"
                    class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-gray-600 hover:text-orange-600"
                    title="Show QR Code">
                    <x-icon name="o-qr-code" class="w-5 h-5" />
                </button>

                <div
                    class="px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-2 {{ $tunnel->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    <div
                        class="w-2 h-2 rounded-full {{ $tunnel->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}">
                    </div>
                    {{ ucfirst($tunnel->status) }}
                </div>
            </div>
        </div>

        @if ($activeTab === 'traffic')
            <!-- Requests Inspect Area -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[700px] animate-in fade-in duration-500">
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
                                <h2 class="text-sm font-mono font-bold text-gray-800 truncate">
                                    {{ $selectedRequest->path }}
                                </h2>
                            </div>
                            <div class="flex items-center gap-3">
                                <button wire:click="replayRequest('{{ $selectedRequest->id }}')"
                                    wire:loading.attr="disabled"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 rounded-lg transition-colors border border-blue-100">
                                    <svg wire:loading.remove wire:target="replayRequest" class="w-3.5 h-3.5"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <svg wire:loading wire:target="replayRequest"
                                        class="animate-spin h-3.5 w-3.5 text-blue-600"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Replay</span>
                                </button>

                                <button
                                    onclick="copyAsCurl({{ json_encode([
                                        'method' => $selectedRequest->method,
                                        'url' => $tunnel->public_url . $selectedRequest->path,
                                        'headers' => $selectedRequest->request_headers,
                                        'body' => $selectedRequest->request_body,
                                    ]) }})"
                                    class="text-xs font-bold text-gray-600 hover:text-gray-700 flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 rounded-lg transition-colors border border-gray-200">
                                    <x-icon name="o-clipboard-document" class="w-3.5 h-3.5" />
                                    <span>Copy as cURL</span>
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
                            <h3 class="text-lg font-medium" style="color: var(--color-neutral);">Inspect a request
                            </h3>
                            <p class="text-sm max-w-xs mx-auto mt-2">Select a request from the list on the left to view
                                detailed timing, headers and more.</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Security Tab -->
            <div class="animate-in slide-in-from-bottom-4 duration-500">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- IP Whitelisting Card -->
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                                <x-icon name="o-shield-check" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">IP Whitelisting</h3>
                                <p class="text-[11px] text-gray-500 uppercase tracking-wider font-semibold">Security
                                    Layer</p>
                            </div>
                        </div>

                        <div class="p-8">
                            <p class="text-sm text-gray-600 mb-6 leading-relaxed">
                                Restrict access to this tunnel to specific IP addresses. If the list is empty, anyone
                                with the URL can access it (unless PIN is enabled).
                            </p>

                            <form wire:submit="addIp" class="flex gap-2 mb-6">
                                <div class="flex-1">
                                    <x-input wire:model="newIp" placeholder="e.g. 1.2.3.4"
                                        class="input-bordered h-11" />
                                </div>
                                <x-button type="submit" label="Add"
                                    class="btn-primary h-11 px-6 shadow-sm shadow-orange-200" icon="o-plus" />
                            </form>

                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest pl-1">Whitelisted
                                    IPs</h4>

                                @forelse($allowedIps as $ip)
                                    <div
                                        class="flex items-center justify-between p-3.5 bg-gray-50 rounded-xl border border-gray-100 group hover:border-orange-200 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                            <span
                                                class="font-mono text-sm font-bold text-gray-700">{{ $ip }}</span>
                                        </div>
                                        <button wire:click="removeIp('{{ $ip }}')"
                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                                            <x-icon name="o-trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                @empty
                                    <div
                                        class="py-8 text-center border-2 border-dashed border-gray-100 rounded-2xl bg-gray-50/30">
                                        <x-icon name="o-globe-alt" class="w-8 h-8 mx-auto text-gray-300 mb-2" />
                                        <p class="text-sm text-gray-400">No restrictions applied.<br><span
                                                class="text-xs">Public access enabled.</span></p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- PIN Protection Card (ReadOnly placeholder for now or move it here) -->
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm opacity-60">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                <x-icon name="o-lock-closed" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">PIN Protection</h3>
                                <p class="text-[11px] text-gray-500 uppercase tracking-wider font-semibold">
                                    Authentication</p>
                            </div>
                        </div>

                        <div class="p-8">
                            @if ($tunnel->pin)
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold mb-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                    Enabled
                                </div>
                                <div
                                    class="p-4 bg-gray-50 rounded-xl border border-gray-100 font-mono text-2xl font-black tracking-widest text-center text-gray-800">
                                    {{ $tunnel->pin }}
                                </div>
                            @else
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold mb-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                    Disabled
                                </div>
                                <p class="text-sm text-gray-500">PIN protection is currently controlled via CLI when
                                    starting the tunnel.</p>
                            @endif

                            <div
                                class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 text-xs text-blue-700 leading-relaxed">
                                <strong>Note:</strong> PIN protection adds a web-based entry page. Requests without the
                                correct session cookie will be redirected.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function copyAsCurl(request) {
            let curl = `curl -X ${request.method} "${request.url}"`;
            for (const [header, values] of Object.entries(request.headers || {})) {
                // Skip headers that curl adds automatically or are internal
                const lowered = header.toLowerCase();
                if (['content-length', 'host', 'connection'].includes(lowered)) continue;

                const val = Array.isArray(values) ? values.join(', ') : values;
                curl += ` -H "${header}: ${val}"`;
            }
            if (request.body) {
                // Escape single quotes for shell
                const escapedBody = request.body.replace(/'/g, "'\\''");
                curl += ` -d '${escapedBody}'`;
            }

            navigator.clipboard.writeText(curl).then(() => {
                alert('cURL command copy to clipboard!');
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('request-replayed', (event) => {
                alert('Request replayed! Remote server returned: ' + event[0].status);
            });

            Livewire.on('request-failed', (event) => {
                alert('Replay failed: ' + event[0].error);
            });
        });
    </script>
    <!-- QR Code Modal -->
    <x-modal wire:model="showQrModal" id="qr-modal" title="Scan for Mobile Testing">
        <div class="flex flex-col items-center justify-center p-6 text-center">
            <div class="bg-white p-4 rounded-3xl shadow-lg border border-gray-100 mb-6 font-mono">
                {!! QrCode::size(250)->margin(1)->generate($tunnel->public_url) !!}
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Tunnel Public URL</h3>
            <p class="text-sm text-gray-500 break-all bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 cursor-pointer hover:bg-gray-100"
                @click="navigator.clipboard.writeText('{{ $tunnel->public_url }}').then(() => alert('URL kopyalandı!'))">
                {{ $tunnel->public_url }}
            </p>
            <p class="mt-4 text-xs text-gray-400">Scan this code with your mobile device to test your local service
                instantly.</p>
        </div>
        <x-slot:actions>
            <x-button label="Close" @click="$wire.showQrModal = false" class="btn-ghost" />
        </x-slot:actions>
    </x-modal>

    <script>
        document.addEventListener('open-qr-modal', () => {
            @this.set('showQrModal', true);
        });
    </script>
</div>

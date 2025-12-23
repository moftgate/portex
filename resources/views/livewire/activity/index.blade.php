<?php

use App\Models\TunnelRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public ?string $selectedRequestId = null;

    public string $search = '';

    public function selectRequest($id)
    {
        $this->selectedRequestId = $id;
    }

    public function clearLogs()
    {
        $user = auth()->user();

        TunnelRequest::whereHas('tunnel', function (Builder $query) use ($user) {
            $query->when(is_user(), function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        })->delete();

        $this->selectedRequestId = null;
        $this->dispatch('logs-cleared');
    }

    public function replayRequest($id)
    {
        $request = TunnelRequest::findOrFail($id);
        $tunnel = $request->tunnel;

        if (!$tunnel) {
            return;
        }

        // We use Laravel's Http client to send a request to the public URL
        // mimicking the original request.
        $url = $tunnel->public_url . $request->path;

        $headers = collect($request->request_headers)
            ->filter(function ($v, $k) {
                $lowered = strtolower($k);
                return !in_array($lowered, [
                    'host',
                    'content-length',
                    'connection',
                    'upgrade',
                    'x-real-ip',
                    'x-forwarded-for',
                    'x-forwarded-proto',
                    'accept-encoding', // Let Http client handle compression
                ]);
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

    public function getRequestsProperty()
    {
        $user = auth()->user();

        return TunnelRequest::query()
            ->whereHas('tunnel', function (Builder $query) use ($user) {
                $query->when(is_user(), function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->where('path', 'like', "%{$this->search}%")
                        ->orWhere('method', 'like', "%{$this->search}%")
                        ->orWhere('ip_address', 'like', "%{$this->search}%")
                        ->orWhere('user_agent', 'like', "%{$this->search}%");
                });
            })
            ->with('tunnel')
            ->latest()
            ->paginate(50);
    }

    public function getChartDataProperty()
    {
        $user = auth()->user();

        // Base query for user's requests
        $baseQuery = TunnelRequest::query()->whereHas('tunnel', function (Builder $query) use ($user) {
            $query->when(is_user(), function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        });

        // 1. Requests per hour (last 24h)
        $requestsPerHour = $baseQuery
            ->clone()
            ->where('created_at', '>=', now()->subHours(24))
            ->select(DB::raw("DATE_TRUNC('hour', created_at) as hour"), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->hour)->format('H:00') => $item->count];
            })
            ->toArray();

        // Fill missing hours
        $last24Hours = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = now()->subHours($i)->format('H:00');
            $last24Hours[$hour] = $requestsPerHour[$hour] ?? 0;
        }

        // 2. Status Code Distribution
        $statusDistribution = $baseQuery
            ->clone()
            ->select(DB::raw('status_code / 100 as category'), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                $labels = [2 => '2xx', 3 => '3xx', 4 => '4xx', 5 => '5xx'];

                return [$labels[$item->category] ?? 'Other' => $item->count];
            })
            ->toArray();

        // 3. Top Paths
        $topPaths = $baseQuery->clone()->select('path', DB::raw('count(*) as count'))->groupBy('path')->orderBy('count', 'desc')->limit(5)->get()->map(fn($item) => ['name' => $item->path, 'value' => $item->count])->toArray();

        return [
            'trafficLines' => [
                'labels' => array_keys($last24Hours),
                'data' => array_values($last24Hours),
            ],
            'statusPie' => array_map(fn($k, $v) => ['name' => $k, 'value' => $v], array_keys($statusDistribution), array_values($statusDistribution)),
            'topPaths' => $topPaths,
        ];
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
            'charts' => $this->chartData,
        ];
    }
}; ?>

<div class="py-6" x-data="{
    chartsData: @js($charts),
    trafficChart: null,
    statusChart: null,
    pathsChart: null,

    initCharts() {
        if (typeof echarts === 'undefined') return;

        this.trafficChart = echarts.init(document.getElementById('trafficChart'));
        this.statusChart = echarts.init(document.getElementById('statusChart'));
        this.pathsChart = echarts.init(document.getElementById('pathsChart'));

        this.renderCharts();

        window.addEventListener('resize', () => {
            this.trafficChart.resize();
            this.statusChart.resize();
            this.pathsChart.resize();
        });
    },

    renderCharts() {
        const commonOptions = {
            textStyle: { fontFamily: 'Inter, system-ui, sans-serif' },
            animationDuration: 1000
        };

        this.trafficChart.setOption({
            ...commonOptions,
            tooltip: { trigger: 'axis' },
            grid: { top: 20, right: 20, bottom: 40, left: 40, containLabel: true },
            xAxis: {
                type: 'category',
                data: this.chartsData.trafficLines.labels,
                axisLine: { lineStyle: { color: '#E5E7EB' } },
                axisLabel: { color: '#6B7280', fontSize: 10 }
            },
            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { type: 'dashed', color: '#F3F4F6' } },
                axisLabel: { color: '#6B7280', fontSize: 10 }
            },
            series: [{
                data: this.chartsData.trafficLines.data,
                type: 'line',
                smooth: true,
                showSymbol: false,
                lineStyle: { width: 3, color: '#F97316' },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(249, 115, 22, 0.2)' },
                        { offset: 1, color: 'rgba(249, 115, 22, 0)' }
                    ])
                }
            }]
        });

        this.statusChart.setOption({
            ...commonOptions,
            tooltip: { trigger: 'item' },
            series: [{
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: { borderRadius: 10, borderColor: '#fff', borderWidth: 2 },
                label: { show: false },
                emphasis: { label: { show: true, fontSize: 12, fontWeight: 'bold' } },
                data: this.chartsData.statusPie,
                color: ['#10B981', '#3B82F6', '#EF4444', '#F59E0B']
            }]
        });

        this.pathsChart.setOption({
            ...commonOptions,
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            grid: { top: 10, right: 20, bottom: 10, left: 10, containLabel: true },
            xAxis: { type: 'value', show: false },
            yAxis: {
                type: 'category',
                data: this.chartsData.topPaths.map(i => i.name).reverse(),
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: { color: '#374151', fontSize: 11 }
            },
            series: [{
                data: this.chartsData.topPaths.map(i => i.value).reverse(),
                type: 'bar',
                itemStyle: { color: '#2563EB', borderRadius: [0, 4, 4, 0] },
                barWidth: '60%'
            }]
        });
    }
}" x-init="initCharts()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight" style="color: var(--color-neutral);">Traffic Activity</h1>
                <p class="text-sm text-gray-600 mt-1">Real-time analytics and detailed request logs</p>
            </div>
            <div class="flex items-center gap-4">
                @if (is_admin())
                    <button wire:click="clearLogs" wire:confirm="Are you sure you want to clear all request logs?"
                        class="text-xs font-bold text-gray-500 hover:text-red-600 flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-red-50 rounded-lg transition-colors border border-gray-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Clear Logs
                    </button>
                @endif

                <div
                    class="flex items-center gap-2 text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Monitoring Live Traffic
                </div>
            </div>
        </div>
        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col h-64">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Requests (Last 24h)
                </h3>
                <div id="trafficChart" wire:ignore class="flex-1 w-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col h-64">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Status Distribution
                </h3>
                <div id="statusChart" wire:ignore class="flex-1 w-full"></div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col h-64">
                <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Top Requested Paths
                </h3>
                <div id="pathsChart" wire:ignore class="flex-1 w-full"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[750px] mb-12">
            <div
                class="lg:col-span-4 bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Activity Log</h3>
                        <div wire:loading wire:target="selectRequest, search, clearLogs">
                            <svg class="animate-spin h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Filter by path, method or IP..."
                            class="w-full pl-8 pr-4 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-gray-50 bg-white" wire:poll.10s>
                    @forelse($requests as $request)
                        <button wire:click="selectRequest('{{ $request->id }}')"
                            class="w-full text-left px-5 py-3.5 transition-all hover:bg-gray-50/80 group {{ $selectedRequestId === $request->id ? 'bg-orange-50/50 ring-1 ring-inset ring-orange-100' : '' }}">
                            <div class="flex items-center justify-between mb-1.5">
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 truncate max-w-[120px]">
                                    {{ $request->tunnel?->name ?? 'Deleted' }}
                                </span>
                                <span
                                    class="text-[10px] text-gray-400 group-hover:text-gray-500">{{ $request->created_at->diffForHumans(null, true) }}</span>
                            </div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-black tracking-wider border
                                    @if ($request->method === 'GET') bg-blue-50 text-blue-600 border-blue-100
                                    @elseif($request->method === 'POST') bg-green-50 text-green-600 border-green-100
                                    @elseif($request->method === 'PUT' || $request->method === 'PATCH') bg-orange-50 text-orange-600 border-orange-100
                                    @elseif($request->method === 'DELETE') bg-red-50 text-red-600 border-red-100
                                    @else bg-gray-50 text-gray-600 border-gray-100 @endif">
                                    {{ $request->method }}
                                </span>
                                <span class="text-xs font-mono font-bold truncate text-gray-800 flex-1">
                                    {{ $request->path }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-[10px] font-medium text-gray-400">
                                <span
                                    class="{{ $request->status_code >= 400 ? 'text-red-500' : 'text-green-600' }}">{{ $request->status_code }}</span>
                                <span class="text-gray-200">|</span>
                                <span class="text-gray-500">{{ $request->response_time_ms }}ms</span>
                                <span class="text-gray-200">|</span>
                                <span class="text-gray-500">{{ $request->ip_address }}</span>
                                <span class="text-gray-200">|</span>
                                <span class="text-gray-500 truncate max-w-[80px]"
                                    title="{{ $request->user_agent }}">{{ $request->user_agent ?? 'Unknown Client' }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="p-12 text-center bg-white h-full flex flex-col items-center justify-center">
                            <div
                                class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">No entries found
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="p-3 border-t border-gray-50 bg-gray-50/30">
                    {{ $requests->links(data: ['scrollTo' => false]) }}
                </div>
            </div>

            <div
                class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col shadow-sm">
                @if ($selectedRequest)
                    <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                        <div class="flex items-center gap-4">
                            <span
                                class="px-3 py-1 bg-gray-900 text-white rounded-md text-xs font-black font-mono shadow-sm">{{ $selectedRequest->method }}</span>
                            <h2 class="text-base font-mono font-bold text-gray-900 truncate">
                                {{ $selectedRequest->path }}</h2>
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
                            <a href="{{ route('tunnels.show', $selectedRequest->tunnel_id) }}"
                                class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 rounded-lg transition-colors border border-orange-100">
                                Inspect Tunnel
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-[#FAFAFA]">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">Status
                                </div>
                                <div
                                    class="text-3xl font-black @if ($selectedRequest->status_code >= 500) text-red-600 @elseif($selectedRequest->status_code >= 400) text-orange-600 @else text-green-600 @endif">
                                    {{ $selectedRequest->status_code }}
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">
                                    Duration
                                </div>
                                <div class="text-3xl font-black text-gray-900">
                                    {{ $selectedRequest->response_time_ms }}<span
                                        class="text-sm ml-1 text-gray-400 font-medium">ms</span>
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">Client
                                    IP
                                </div>
                                <div class="text-base font-bold text-gray-900 mt-2 font-mono">
                                    {{ $selectedRequest->ip_address }}</div>
                            </div>
                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">
                                    Timestamp
                                </div>
                                <div class="text-xs font-bold text-gray-900 mt-3 font-mono">
                                    {{ $selectedRequest->created_at->format('Y-m-d H:i:s.v') }}</div>
                            </div>
                        </div>

                        <div>
                            <h4
                                class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                User Agent
                            </h4>
                            <div
                                class="bg-white rounded-2xl p-5 font-mono text-[10px] text-gray-600 leading-relaxed border border-gray-100 shadow-sm">
                                {{ $selectedRequest->user_agent ?? 'Unknown User Agent' }}
                            </div>
                        </div>

                        <!-- Request Details -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Request Headers -->
                            <div>
                                <h4
                                    class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    Request Headers
                                </h4>
                                <div
                                    class="bg-white rounded-2xl p-6 font-mono text-xs border border-gray-100 shadow-sm space-y-1 overflow-x-auto max-h-[300px]">
                                    @foreach ($selectedRequest->request_headers ?? [] as $key => $values)
                                        <div class="flex items-start gap-4 ring-1 ring-gray-50 py-1">
                                            <span
                                                class="text-gray-400 font-bold w-32 flex-shrink-0">{{ $key }}:</span>
                                            <span
                                                class="text-gray-800 break-all">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Response Headers -->
                            <div>
                                <h4
                                    class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                    Response Headers
                                </h4>
                                <div
                                    class="bg-white rounded-2xl p-6 font-mono text-xs border border-gray-100 shadow-sm space-y-1 overflow-x-auto max-h-[300px]">
                                    @foreach ($selectedRequest->response_headers ?? [] as $key => $values)
                                        <div class="flex items-start gap-4 ring-1 ring-gray-50 py-1">
                                            <span
                                                class="text-gray-400 font-bold w-32 flex-shrink-0">{{ $key }}:</span>
                                            <span
                                                class="text-gray-800 break-all">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Bodies Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Request Body -->
                            <div>
                                <h4
                                    class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Request Body
                                </h4>
                                <div
                                    class="bg-gray-900 rounded-2xl p-6 font-mono text-xs text-white shadow-xl ring-1 ring-white/10 overflow-x-auto min-h-[100px] max-h-[400px]">
                                    @if ($selectedRequest->request_body !== null && $selectedRequest->request_body !== '')
                                        @php
                                            $isJson = false;
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
                                                    $isJson = true;
                                                }
                                            }
                                        @endphp
                                        <pre class="whitespace-pre-wrap">{{ $jsonBody }}</pre>
                                    @else
                                        <div
                                            class="flex flex-col items-center justify-center h-full py-4 text-gray-500 italic">
                                            <svg class="w-6 h-6 mb-2 opacity-20" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No request body
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Response Body -->
                            <div>
                                <h4
                                    class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Response Body
                                </h4>
                                <div
                                    class="bg-gray-900 rounded-2xl p-6 font-mono text-xs text-green-400 shadow-xl ring-1 ring-white/10 overflow-x-auto min-h-[100px] max-h-[400px]">
                                    @if ($selectedRequest->response_body !== null && $selectedRequest->response_body !== '')
                                        @php
                                            $isJson = false;
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
                                                    $isJson = true;
                                                }
                                            }
                                        @endphp
                                        <pre class="whitespace-pre-wrap">{{ $jsonBody }}</pre>
                                    @else
                                        <div
                                            class="flex flex-col items-center justify-center h-full py-4 text-gray-500 italic">
                                            <svg class="w-6 h-6 mb-2 opacity-20" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No response body
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="flex-1 flex flex-col items-center justify-center p-20 text-center">
                        <div
                            class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center mb-6 ring-4 ring-gray-50/50">
                            <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Select an entry</h3>
                        <p class="text-sm text-gray-500 max-w-xs mx-auto mt-2">Insights and headers for each request
                            will appear here. Monitoring all tunnels live.</p>
                    </div>
                @endif
            </div>
        </div>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('logs-cleared', () => {
                    // Refresh charts? Activity log is polled anyway.
                });

                Livewire.on('request-replayed', (event) => {
                    // Simple toast or notification
                    alert('Request replayed! Remote server returned: ' + event[0].status);
                });

                Livewire.on('request-failed', (event) => {
                    alert('Replay failed: ' + event[0].error);
                });
            });
        </script>
    </div>
</div>

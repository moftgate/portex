<?php

use App\Models\TunnelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;

    public ?string $selectedRequestId = null;

    public function selectRequest($id)
    {
        $this->selectedRequestId = $id;
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
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Monitoring Live Traffic
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

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[650px]">
            <div
                class="lg:col-span-4 bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Activity Log</h3>
                    <div wire:loading wire:target="selectRequest">
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

                <div class="flex-1 overflow-y-auto divide-y divide-gray-50" wire:poll.5s>
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
                                    class="text-[10px] font-black font-mono w-10 text-center py-0.5 rounded @if ($request->status_code >= 500) bg-red-100 text-red-700 @elseif($request->status_code >= 400) bg-orange-100 text-orange-700 @elseif($request->status_code >= 300) bg-blue-100 text-blue-700 @else bg-green-100 text-green-700 @endif">
                                    {{ $request->method }}
                                </span>
                                <span class="text-sm font-semibold truncate text-gray-800 font-mono">
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
                            </div>
                        </button>
                    @empty
                        <div class="p-12 text-center">
                            <div
                                class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Waiting for traffic
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
                        <a href="{{ route('tunnels.show', $selectedRequest->tunnel_id) }}"
                            class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 rounded-lg transition-colors">
                            Inspect Tunnel
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 space-y-8">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">Result
                                </div>
                                <div
                                    class="text-3xl font-black @if ($selectedRequest->status_code >= 500) text-red-600 @elseif($selectedRequest->status_code >= 400) text-orange-600 @else text-green-600 @endif">
                                    {{ $selectedRequest->status_code }}
                                </div>
                            </div>
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">
                                    Duration</div>
                                <div class="text-3xl font-black text-gray-900">
                                    {{ $selectedRequest->response_time_ms }}<span
                                        class="text-sm ml-1 text-gray-400">ms</span>
                                </div>
                            </div>
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">Client
                                    IP</div>
                                <div class="text-xl font-bold text-gray-900 mt-2">
                                    {{ $selectedRequest->ip_address }}
                                </div>
                            </div>
                            <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                <div class="text-[10px] uppercase text-gray-400 font-bold tracking-widest mb-2">
                                    Timestamp</div>
                                <div class="text-sm font-bold text-gray-900 mt-3 font-mono">
                                    {{ $selectedRequest->created_at->format('H:i:s.v') }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3">User Agent</h4>
                            <div
                                class="bg-gray-50 rounded-xl p-5 font-mono text-xs text-gray-600 leading-relaxed border border-gray-100">
                                {{ $selectedRequest->user_agent }}
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase text-gray-400 tracking-widest mb-3">Request Headers
                            </h4>
                            <div
                                class="bg-gray-900 rounded-2xl p-6 font-mono text-xs text-green-400 space-y-2 overflow-x-auto shadow-xl ring-1 ring-white/10">
                                <div class="flex items-start gap-4"><span
                                        class="text-blue-400 font-bold w-24 flex-shrink-0">Host:</span> <span
                                        class="text-white">{{ $selectedRequest->tunnel?->subdomain ?? 'deleted' }}.portex.io</span>
                                </div>
                                <div class="flex items-start gap-4"><span
                                        class="text-blue-400 font-bold w-24 flex-shrink-0">Remote-Addr:</span> <span
                                        class="text-white">{{ $selectedRequest->ip_address }}</span></div>
                                <div class="flex items-start gap-4"><span
                                        class="text-blue-400 font-bold w-24 flex-shrink-0">Accept:</span> <span
                                        class="text-white">*/*</span></div>
                                <div class="flex items-start gap-4"><span
                                        class="text-blue-400 font-bold w-24 flex-shrink-0">X-Request-ID:</span> <span
                                        class="text-orange-400">{{ $selectedRequest->id }}</span></div>
                            </div>
                        </div>
                    </div>
                @else
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
    </div>
</div>

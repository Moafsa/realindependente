@extends('layouts.portal')

@section('title', 'Minha Evolução')
@section('body_class', 'bg-[#0f172a]')
@section('main_class', 'bg-[#0f172a]')
@section('content_padding', 'py-0')
@section('content_container', 'max-w-full')

@section('header_styles')
<style>
    .glass-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
    }
    .premium-gradient-bg {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
    }
    .apexcharts-tooltip {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen premium-gradient-bg text-gray-100 font-sans pb-12 w-full">
    <div class="max-w-7xl mx-auto pt-10 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-black italic tracking-tighter text-white uppercase">Análise de <span class="text-blue-500">Performance</span></h1>
                <p class="text-gray-400 mt-2">Acompanhe seu desenvolvimento técnico e físico.</p>
            </div>
            
            <div class="flex items-center space-x-3 bg-white/5 p-1.5 rounded-2xl border border-white/10">
                <button onclick="changeChartType('radar')" id="btn-radar" class="chart-type-btn px-6 py-2.5 rounded-xl text-[10px] font-black tracking-widest transition-all bg-blue-600 text-white shadow-lg shadow-blue-600/20">RADAR</button>
                <button onclick="changeChartType('bar')" id="btn-bar" class="chart-type-btn px-6 py-2.5 rounded-xl text-[10px] font-black tracking-widest transition-all text-gray-500 hover:text-white">BARRAS</button>
                <button onclick="changeChartType('line')" id="btn-line" class="chart-type-btn px-6 py-2.5 rounded-xl text-[10px] font-black tracking-widest transition-all text-gray-500 hover:text-white">LINHA</button>
                
                <div class="h-6 w-[1px] bg-white/10 mx-2"></div>
                
                <select id="performance-period" class="bg-transparent border-none text-[10px] font-black uppercase tracking-widest text-gray-400 focus:ring-0 cursor-pointer">
                    <option value="1month">Mês</option>
                    <option value="3months">3 Meses</option>
                    <option value="6months" selected>6 Meses</option>
                    <option value="1year">1 Ano</option>
                </select>
            </div>
        </div>

        <!-- Main Chart Container -->
        <div class="glass-card p-8 mb-8">
            <div id="performanceChart" class="w-full" style="min-height: 450px;"></div>
        </div>

        <!-- Metric Sparklines -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($sparklineData as $metric => $data)
            <div class="glass-card p-6 hover:border-blue-500/30 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h5 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">{{ $metric }}</h5>
                        <p class="text-2xl font-black text-white italic">
                            @php
                                $unit = match($metric) {
                                    'Peso' => 'kg',
                                    'Altura', 'Cintura', 'Bíceps', 'Coxa', 'Panturrilha' => 'cm',
                                    'Biotipo' => '',
                                    default => '%'
                                };
                                $val = $data->last()['y'];
                                $isNumeric = is_numeric($val);
                            @endphp
                            {{ $val }}{{ $unit }}
                            @if($isNumeric)
                                @php
                                    $last = (float)$val;
                                    $prev = $data->count() > 1 ? (float)$data->get($data->count() - 2)['y'] : $last;
                                    $diff = $last - $prev;
                                @endphp
                                <span class="text-[10px] font-bold {{ $diff > 0 ? 'text-green-400' : ($diff < 0 ? 'text-red-400' : 'text-gray-600') }} ml-2">
                                    {{ $diff > 0 ? '▲' : ($diff < 0 ? '▼' : '•') }} {{ abs($diff) }}{{ $unit }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div id="sparkline-{{ Str::slug($metric) }}" class="w-full h-16"></div>
            </div>
            @endforeach
        </div>

        <!-- History Table -->
        <div class="glass-card overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-sm font-black text-white uppercase tracking-widest italic">Histórico de Registros</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Métrica</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Valor</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Avaliador</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($performanceRecords as $record)
                        <tr class="hover:bg-white/2 transition-colors">
                            <td class="px-8 py-6">
                                <span class="text-sm text-gray-400">{{ $record->recorded_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm text-white font-bold italic uppercase tracking-tight">{{ $record->metric }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    @php
                                        $unit = match($record->metric) {
                                            'Peso' => 'kg',
                                            'Altura', 'Cintura', 'Bíceps', 'Coxa', 'Panturrilha' => 'cm',
                                            'Biotipo' => '',
                                            default => '%'
                                        };
                                        $isHigh = is_numeric($record->value) && $record->value >= 70;
                                        $isMid = is_numeric($record->value) && $record->value >= 50;
                                    @endphp
                                    <span class="text-sm font-black {{ $isHigh ? 'text-green-400' : ($isMid ? 'text-yellow-400' : 'text-red-400') }}">
                                        {{ $record->value }}{{ $unit }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs text-gray-500">{{ $record->recordedBy->name ?? 'Sistema' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center text-gray-500 italic">Nenhum registro encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($performanceRecords->hasPages())
            <div class="px-8 py-6 border-t border-white/5">
                {{ $performanceRecords->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    window.athleteId = {{ $athlete->id }};
    window.sparklineData = @json($sparklineData);
    
    // Performance Chart Logic (Adapted for Portal)
    let performanceChart = null;
    let currentChartType = 'radar';

    function changeChartType(type) {
        currentChartType = type;
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-600/20');
            btn.classList.add('text-gray-500');
        });
        const activeBtn = document.getElementById('btn-' + type);
        activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-600/20');
        activeBtn.classList.remove('text-gray-500');
        loadChartData();
    }

    function loadChartData() {
        const period = document.getElementById('performance-period').value;
        fetch(`{{ route('portal.performance-data') }}?period=${period}`)
            .then(res => res.json())
            .then(data => {
                if(data.success) renderChart(data.data);
            });
    }

    function renderChart(data) {
        const metricsList = Object.keys(data);
        let series = [];
        let labels = [];

        if (currentChartType === 'line') {
            const labelsSet = new Set();
            Object.keys(data).forEach(m => data[m].athlete.forEach(p => labelsSet.add(p.date)));
            labels = Array.from(labelsSet).sort();
            series = metricsList.map(m => ({
                name: m,
                data: labels.map(d => {
                    const p = data[m].athlete.find(x => x.date === d);
                    return p ? p.value : null;
                })
            }));
        } else {
            labels = metricsList;
            const latest = metricsList.map(m => data[m].athlete.length ? data[m].athlete[data[m].athlete.length-1].value : 0);
            const avg = metricsList.map(m => data[m].category_avg.length ? data[m].category_avg[data[m].category_avg.length-1].value : 0);
            series = [
                { name: 'Minha Performance', data: latest },
                { name: 'Média da Equipe', data: avg }
            ];
        }

        const options = {
            series: series,
            chart: {
                type: currentChartType,
                height: 450,
                foreColor: '#94a3b8',
                toolbar: { show: false },
                animations: { enabled: true }
            },
            theme: { mode: 'dark' },
            colors: ['#3b82f6', '#475569'],
            stroke: { width: 3, curve: 'smooth' },
            markers: { size: 4 },
            xaxis: { categories: labels },
            yaxis: { min: 0, max: 100 },
            legend: { position: 'top', horizontalAlign: 'right' },
            grid: { borderColor: 'rgba(255,255,255,0.05)' }
        };

        if(performanceChart) performanceChart.destroy();
        performanceChart = new ApexCharts(document.querySelector("#performanceChart"), options);
        performanceChart.render();
    }

    function initSparklines() {
        Object.keys(window.sparklineData).forEach(metric => {
            const slug = metric.toLowerCase().replace(/[^a-z0-9]/g, '-');
            const container = document.querySelector(`#sparkline-${slug}`);
            if(!container) return;

            new ApexCharts(container, {
                series: [{ data: window.sparklineData[metric].map(p => p.y) }],
                chart: { type: 'area', height: 60, sparkline: { enabled: true } },
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
                colors: ['#3b82f6']
            }).render();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadChartData();
        initSparklines();
        document.getElementById('performance-period').addEventListener('change', loadChartData);
    });
</script>
@endsection

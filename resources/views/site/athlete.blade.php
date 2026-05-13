@extends('layouts.site')

@section('title', $athlete->full_name)
@section('description', $athlete->bio ?? 'Perfil oficial do atleta ' . $athlete->full_name . ' no ' . (tenant('name') ?? 'Clube'))
@section('og-image', $athlete->profile_picture_url)

@section('content')
<div class="min-h-screen bg-[#0f172a] text-white">
    <!-- Player Hero Header -->
    <div class="relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-600/20 to-[#0f172a] z-10"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center md:items-end space-y-8 md:space-y-0 md:space-x-12">
                <!-- Avatar with Badge -->
                <div class="relative">
                    <div class="h-48 w-48 md:h-64 md:w-64 rounded-3xl overflow-hidden border-4 border-blue-500 shadow-[0_0_50px_rgba(59,130,246,0.3)] bg-gray-800">
                        <img src="{{ $athlete->profile_picture_url }}" alt="{{ $athlete->full_name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-blue-600 px-6 py-2 rounded-2xl border-4 border-[#0f172a] shadow-xl">
                        <span class="text-2xl font-black italic tracking-tighter">#{{ $athlete->jersey_number ?? '00' }}</span>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-black uppercase tracking-widest border border-blue-500/30">
                            {{ $athlete->subcategory ?? 'Categoria' }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-black uppercase tracking-widest border border-green-500/30">
                            {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black uppercase italic tracking-tighter leading-none mb-4">
                        {{ explode(' ', $athlete->full_name)[0] }} <span class="text-blue-500">{{ explode(' ', $athlete->full_name)[1] ?? '' }}</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-400 font-bold uppercase tracking-tight italic">
                        {{ $athlete->position ?? 'Atleta' }} • {{ $athlete->team->name ?? 'Sem Equipe' }}
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center min-w-[120px]">
                        <div class="text-3xl font-black text-blue-500 italic">{{ $athlete->age ?? '--' }}</div>
                        <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Idade</div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center min-w-[120px]">
                        <div class="text-3xl font-black text-blue-500 italic">{{ $athlete->height ? number_format($athlete->height, 0) : '--' }}<small class="text-xs">cm</small></div>
                        <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Altura</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Biography & Technical Info -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Bio Card -->
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6 flex items-center">
                        <span class="w-8 h-1 bg-blue-500 mr-4"></span> Biografia do Atleta
                    </h2>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        {{ $athlete->bio ?? 'Este atleta ainda não possui uma biografia cadastrada. Fique ligado para futuras atualizações sobre sua carreira e conquistas.' }}
                    </p>
                </section>

                <!-- Evolution Chart -->
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-black uppercase italic tracking-tighter flex items-center">
                            <span class="w-8 h-1 bg-blue-500 mr-4"></span> Evolução Técnica
                        </h2>
                        <select id="metric-selector" class="bg-white/5 border border-white/10 rounded-xl text-xs font-bold uppercase px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($chartData as $metric => $data)
                            <option value="{{ $metric }}">{{ $metric }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="evolution-chart" class="h-80 w-full">
                        @if($chartData->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-gray-500 opacity-50">
                            <svg class="h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <p class="font-bold uppercase tracking-widest text-xs">Sem dados de evolução disponíveis</p>
                        </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right: Secondary Info & Actions -->
            <div class="space-y-8">
                <!-- Team Card -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-blue-200 text-xs font-black uppercase tracking-[0.2em] mb-2">Equipe Atual</h3>
                        <div class="text-3xl font-black uppercase italic tracking-tighter mb-4">{{ $athlete->team->name ?? 'Independente' }}</div>
                        <a href="{{ $athlete->team ? route('site.team', $athlete->team->id) : '#' }}" class="inline-flex items-center text-sm font-bold uppercase tracking-widest bg-white/20 hover:bg-white/30 px-6 py-3 rounded-2xl transition-all">
                            Ver Equipe <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -bottom-4 -right-4 opacity-20 transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="h-32 w-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z"></path></svg>
                    </div>
                </div>

                <!-- Attributes Card -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Atributos</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Peso</span>
                            <span class="text-sm font-black italic">{{ $athlete->weight ? number_format($athlete->weight, 1) : '--' }} kg</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Gênero</span>
                            <span class="text-sm font-black italic uppercase">{{ $athlete->gender ?? '--' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Membro Forte</span>
                            <span class="text-sm font-black italic">Destro</span>
                        </div>
                    </div>
                </div>

                <!-- Share -->
                <div class="flex gap-4">
                    <button class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </button>
                    <button class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </button>
                    <button class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884 0 2.225.569 3.447 1.698 5.407l-1.047 3.821 3.838-1.005zm10.925-8.032c-.32-.16-.1.896-.24-.16-.14-.14-1.44-.74-2.42-1.18-.18-.08-.34-.14-.48-.2-.48-.18-.7-.1-.94.14-.34.34-1.32 1.32-1.6 1.6-.14.14-.24.16-.56.02-.32-.16-1.36-.5-2.58-1.58-1-.88-1.68-1.98-1.88-2.3-.2-.32-.02-.5.14-.66.14-.14.32-.38.48-.56.16-.18.22-.32.32-.54.1-.22.05-.4-.02-.56-.07-.16-.62-1.5-.86-2.06-.24-.54-.48-.48-.64-.48-.16 0-.34-.02-.52-.02-.18 0-.48.06-.74.34-.26.28-1 1-1 2.42s1.04 2.8 1.18 3c.14.2 2.04 3.12 4.94 4.38.7.3 1.24.48 1.66.62.7.22 1.34.2 1.84.12.56-.08 1.7-.7 1.94-1.38.24-.68.24-1.26.16-1.38-.08-.12-.3-.2-.62-.36z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartData = @json($chartData);
    
    function initChart(metric) {
        const data = chartData[metric] || [];
        
        const options = {
            series: [{
                name: metric,
                data: data.map(d => d.y)
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#3b82f6'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 4,
                lineCap: 'round'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: data.map(d => new Date(d.x).toLocaleDateString('pt-BR')),
                labels: { style: { colors: '#64748b', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#64748b', fontWeight: 600 } }
            },
            grid: {
                borderColor: 'rgba(255, 255, 255, 0.05)',
                strokeDashArray: 4
            },
            theme: { mode: 'dark' },
            tooltip: {
                theme: 'dark',
                x: { format: 'dd/MM/yyyy' },
                y: { formatter: (val) => val.toFixed(1) }
            }
        };

        if (window.chart) {
            window.chart.destroy();
        }
        
        window.chart = new ApexCharts(document.querySelector("#evolution-chart"), options);
        window.chart.render();
    }

    document.getElementById('metric-selector')?.addEventListener('change', (e) => {
        initChart(e.target.value);
    });

    // Iniciar com a primeira métrica disponível
    const firstMetric = Object.keys(chartData)[0];
    if (firstMetric) {
        initChart(firstMetric);
    }
</script>
@endpush

<style>
    body { background-color: #0f172a; }
</style>
@endsection

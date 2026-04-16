@extends('layouts.portal')

@section('title', 'Minha Evolução')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Minha Evolução</h1>
                    <p class="mt-1 text-sm text-gray-600">Acompanhe seu desempenho e evolução</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
        <!-- Filters and Period Selector -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                        Período
                    </label>
                    <select id="period" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1month">Último mês</option>
                        <option value="3months" selected>Últimos 3 meses</option>
                        <option value="6months">Últimos 6 meses</option>
                        <option value="1year">Último ano</option>
                    </select>
                </div>
                <div>
                    <label for="metric" class="block text-sm font-medium text-gray-700 mb-2">
                        Métrica
                    </label>
                    <select id="metric" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">Todas as métricas</option>
                        @foreach($metrics as $metricItem)
                        <option value="{{ $metricItem->metric }}">{{ ucfirst(str_replace('_', ' ', $metricItem->metric)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="loadPerformanceData()" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        Atualizar Gráficos
                    </button>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($metrics as $metricItem)
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $metricItem->metric)) }}</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($metricItem->avg_value, 1) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500">Última medição: {{ $metricItem->last_recorded ? \Carbon\Carbon::parse($metricItem->last_recorded)->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Main Performance Chart -->
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Evolução da Performance</h3>
                <p class="text-sm text-gray-600">Gráfico interativo mostrando sua evolução ao longo do tempo</p>
            </div>
            <div class="p-6">
                <canvas id="performance-chart" height="80"></canvas>
            </div>
        </div>

        <!-- Metrics Comparison Chart -->
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Comparação de Métricas</h3>
                <p class="text-sm text-gray-600">Compare diferentes métricas lado a lado</p>
            </div>
            <div class="p-6">
                <canvas id="comparison-chart" height="80"></canvas>
            </div>
        </div>

        <!-- Performance Records Table -->
        <div class="bg-white shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Histórico de Registros</h3>
                <p class="text-sm text-gray-600">Todos os registros de performance</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Métrica</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado por</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($performanceRecords as $record)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $record->recorded_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $record->metric)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($record->value, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $record->recordedBy->name ?? 'Sistema' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $record->notes ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Nenhum registro de performance encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($performanceRecords->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $performanceRecords->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/portal-performance.js') }}"></script>
@endsection


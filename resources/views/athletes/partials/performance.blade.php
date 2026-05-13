<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <h3 class="text-lg font-semibold text-gray-900">Análise de Desempenho</h3>
        <div class="flex items-center space-x-2 bg-gray-100 p-1 rounded-lg">
            <button onclick="changeChartType('radar')" id="btn-radar" class="chart-type-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all bg-white shadow-sm text-blue-600">RADAR</button>
            <button onclick="changeChartType('bar')" id="btn-bar" class="chart-type-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700">BARRAS</button>
            <button onclick="changeChartType('line')" id="btn-line" class="chart-type-btn px-3 py-1.5 rounded-md text-xs font-bold transition-all text-gray-500 hover:text-gray-700">EVOLUÇÃO</button>
        </div>
        <select id="performance-period" class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            <option value="1month">Último mês</option>
            <option value="3months">Últimos 3 meses</option>
            <option value="6months" selected>Últimos 6 meses</option>
            <option value="1year">Último ano</option>
        </select>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-600">
            Os dados de desempenho são carregados dinamicamente. Use o seletor acima para filtrar por período.
        </p>
    </div>

    <div id="performanceChart" class="w-full" style="min-height: 400px;"></div>

    <div class="mt-12 border-t pt-8">
        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Evolução por Indicador</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($sparklineData as $metric => $data)
            <div class="bg-white border rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h5 class="text-sm font-bold text-gray-700 uppercase tracking-tight">{{ $metric }}</h5>
                        <p class="text-2xl font-black text-blue-600">
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
                                <span class="text-xs font-bold {{ $diff > 0 ? 'text-green-500' : ($diff < 0 ? 'text-red-500' : 'text-gray-400') }}">
                                    {{ $diff > 0 ? '▲' : ($diff < 0 ? '▼' : '•') }} {{ abs($diff) }}{{ $unit }}
                                </span>
                            @endif
                        </p>
                    </div>
                    <div id="sparkline-{{ Str::slug($metric) }}" class="w-32 h-12"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-8 border-t pt-8">
        <h4 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-6">Histórico de Evolução</h4>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Métrica</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Avaliador</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Escolinha</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($performanceRecords as $record)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $record->recorded_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700">
                            {{ $record->metric }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
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
                                <span class="text-sm font-bold {{ $isHigh ? 'text-green-600' : ($isMid ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $record->value }}{{ $unit }}
                                </span>
                                @if($isNumeric)
                                    @if($record->trend === 'improving')
                                        <svg class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    @elseif($record->trend === 'declining')
                                        <svg class="w-4 h-4 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                                    @endif
                                    <span class="text-[10px] ml-1 font-bold {{ $record->change_percentage > 0 ? 'text-green-500' : ($record->change_percentage < 0 ? 'text-red-500' : 'text-gray-400') }}">
                                        {{ $record->change_percentage > 0 ? '+' : '' }}{{ $record->change_percentage }}{{ $unit }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $record->recordedBy->name ?? 'Sistema' }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $record->tenant_name ?? 'Sede Principal' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 italic">
                            Nenhum registro de desempenho encontrado para este período.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $performanceRecords->links() }}
        </div>
    </div>
</div>

<script>
    // Data for charts
    window.athleteId = {{ $athlete->id }};
    window.sparklineData = @json($sparklineData);
</script>


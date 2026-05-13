<div class="space-y-6">
    <!-- Resumo Comparativo (Hoje) -->
    @php
        $today = now()->format('Y-m-d');
        $todayTotals = $nutritionDailyTotals->get($today) ?? ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
        $targets = $activeMealPlan ? ($activeMealPlan->content['total'] ?? []) : [];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Calorias -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Calorias</span>
                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[9px] font-bold">Meta: {{ $targets['calories'] ?? '?' }}</span>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-gray-900">{{ number_format($todayTotals['calories']) }}</span>
                <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase">kcal</span>
            </div>
            <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                @php $pct = isset($targets['calories']) && $targets['calories'] > 0 ? ($todayTotals['calories'] / $targets['calories']) * 100 : 0; @endphp
                <div class="h-full bg-blue-500 rounded-full transition-all duration-500" style="width: {{ min($pct, 100) }}%"></div>
            </div>
        </div>

        <!-- Proteínas -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Proteínas</span>
                <span class="px-2 py-1 bg-green-50 text-green-600 rounded-lg text-[9px] font-bold">Meta: {{ $targets['protein'] ?? '?' }}g</span>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-gray-900">{{ number_format($todayTotals['protein']) }}</span>
                <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase">gramas</span>
            </div>
            <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                @php $pct = isset($targets['protein']) && $targets['protein'] > 0 ? ($todayTotals['protein'] / $targets['protein']) * 100 : 0; @endphp
                <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ min($pct, 100) }}%"></div>
            </div>
        </div>

        <!-- Carboidratos -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Carboidratos</span>
                <span class="px-2 py-1 bg-yellow-50 text-yellow-600 rounded-lg text-[9px] font-bold">Meta: {{ $targets['carbs'] ?? '?' }}g</span>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-gray-900">{{ number_format($todayTotals['carbs']) }}</span>
                <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase">gramas</span>
            </div>
            <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                @php $pct = isset($targets['carbs']) && $targets['carbs'] > 0 ? ($todayTotals['carbs'] / $targets['carbs']) * 100 : 0; @endphp
                <div class="h-full bg-yellow-500 rounded-full transition-all duration-500" style="width: {{ min($pct, 100) }}%"></div>
            </div>
        </div>

        <!-- Gorduras -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gorduras</span>
                <span class="px-2 py-1 bg-red-50 text-red-600 rounded-lg text-[9px] font-bold">Meta: {{ $targets['fat'] ?? '?' }}g</span>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-2xl font-black text-gray-900">{{ number_format($todayTotals['fat']) }}</span>
                <span class="text-[10px] text-gray-400 font-bold mb-1 uppercase">gramas</span>
            </div>
            <div class="mt-3 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                @php $pct = isset($targets['fat']) && $targets['fat'] > 0 ? ($todayTotals['fat'] / $targets['fat']) * 100 : 0; @endphp
                <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ min($pct, 100) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Comparação (7 Dias) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <h3 class="text-sm font-black text-gray-900 uppercase italic tracking-tight mb-6">Adesão Nutricional (Últimos 7 dias)</h3>
        <div class="h-[300px]">
            <canvas id="nutritionComparisonChart"></canvas>
        </div>
    </div>

    <!-- Histórico de Refeições Analisadas -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-black text-gray-900 uppercase italic tracking-tight">Logs de Refeições (IA Vision)</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($mealLogs as $log)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden shadow-sm flex-shrink-0 bg-gray-100">
                            @if($log->photo_path)
                                <img src="{{ Storage::url($log->photo_path) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h5 class="text-sm font-bold text-gray-900">Análise de IA</h5>
                                <span class="text-[10px] font-black text-gray-400 uppercase">{{ $log->consumed_at->format('H:i') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px] font-bold uppercase">{{ $log->calories }} kcal</span>
                                <span class="px-2 py-0.5 bg-green-50 text-green-600 rounded text-[9px] font-bold uppercase">{{ $log->proteins }}g P</span>
                                <span class="px-2 py-0.5 bg-yellow-50 text-yellow-600 rounded text-[9px] font-bold uppercase">{{ $log->carbs }}g C</span>
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded text-[9px] font-bold uppercase">{{ $log->fats }}g G</span>
                            </div>
                            <p class="text-[11px] text-gray-500 italic">"{{ $log->ai_analysis['coach_notes'] ?? 'Refeição registrada via WhatsApp' }}"</p>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-black text-xs">
                                {{ $log->ai_analysis['health_score'] ?? '?' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Nenhuma refeição registrada nos últimos 7 dias.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('nutritionComparisonChart').getContext('2d');
        const labels = @json($nutritionDailyTotals->keys()->toArray());
        const dataAthlete = @json($nutritionDailyTotals->values()->pluck('calories')->toArray());
        const targetValue = {{ $targets['calories'] ?? 0 }};
        const targetData = labels.map(() => targetValue);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.map(d => {
                    const date = new Date(d);
                    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                }),
                datasets: [
                    {
                        label: 'Consumo (kcal)',
                        data: dataAthlete,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Meta IA (kcal)',
                        data: targetData,
                        borderColor: '#e5e7eb',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { size: 11, weight: 'bold' }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#111',
                        bodyColor: '#666',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        bodyFont: { size: 12 },
                        titleFont: { size: 12, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6', drawBorder: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                    }
                }
            }
        });
    });
</script>

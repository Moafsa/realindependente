<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Desempenho do Atleta</h3>
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

    <canvas id="performanceChart" height="300"></canvas>

    <div class="mt-6">
        <a href="{{ route('athletes.performance-data', $athlete) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            Ver todos os registros de desempenho →
        </a>
    </div>
</div>

<script>
    // Performance chart will be initialized by athlete-profile.js
    window.athleteId = {{ $athlete->id }};
</script>


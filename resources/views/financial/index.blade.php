@extends('layouts.dashboard')

@section('title', 'Financeiro')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Financeiro</h1>
            <p class="text-gray-600">Gerencie as finanças do seu clube</p>
        </div>
        <button onclick="openGenerateChargesModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Gerar Cobranças
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Receita Total -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Receita Total</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['total_revenue'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendente -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pendente</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['pending_revenue'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vencido -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Vencido</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['overdue_revenue'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saídas -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-gray-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Saídas Reais</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['total_expenses'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salários Estimados -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-purple-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Folha Estimada</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['estimated_salaries'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Líquido Estimado -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 {{ ($stats['total_revenue'] - $stats['total_expenses'] - $stats['estimated_salaries']) >= 0 ? 'border-indigo-500' : 'border-red-500' }}">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Saldo Estimado</dt>
                            <dd class="text-lg font-medium text-gray-900">R$ {{ number_format($stats['total_revenue'] - $stats['total_expenses'] - $stats['estimated_salaries'], 2, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pedidos Recentes</h3>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                        <li class="py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color === 'green' ? 'bg-green-100 text-green-800' : ($order->status_color === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $order->athlete->full_name ?? $order->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex-shrink-0 text-sm text-gray-500">
                                    {{ $order->formatted_total }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mt-4">
                    <a href="{{ route('financial.charges') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Ver todas as cobranças →
                    </a>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Receita Mensal</h3>
                <div class="space-y-3">
                    @foreach($monthlyRevenue as $month)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-600">{{ $month->month->format('M/Y') }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">R$ {{ number_format($month->revenue, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $month->orders_count }} pedidos</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Charges Modal -->
<div id="generateChargesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Gerar Cobranças</h3>
                <button onclick="closeGenerateChargesModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('financial.generate-charges') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Equipes</label>
                    <div class="mt-2 space-y-2">
                        @foreach($teams as $team)
                        <label class="flex items-center">
                            <input type="checkbox" name="teams[]" value="{{ $team->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">{{ $team->name }} ({{ $team->athletes_count }} atletas)</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Mês de Referência</label>
                    <input type="month" name="month" id="month" value="{{ date('Y-m') }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Valor</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Data de Vencimento</label>
                    <input type="date" name="due_date" id="due_date" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <input type="text" name="description" id="description"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Mensalidade - Janeiro 2024">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeGenerateChargesModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Gerar Cobranças
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGenerateChargesModal() {
    document.getElementById('generateChargesModal').classList.remove('hidden');
}

function closeGenerateChargesModal() {
    document.getElementById('generateChargesModal').classList.add('hidden');
}
</script>
@endsection

@extends('layouts.portal')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Meu Portal</h1>
                    <p class="mt-1 text-sm text-gray-600">Bem-vindo de volta, {{ $athlete->full_name ?? Auth::user()->name }}!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Último acesso</p>
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($athlete->full_name ?? Auth::user()->name, 0, 1)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- My Plans -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Meus Planos</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_plans'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-purple-600">{{ $stats['active_plans'] }} ativos</span>
                        @if($stats['pending_plans'] > 0)
                        <span class="text-gray-500">{{ $stats['pending_plans'] }} pendente(s)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Training Today -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Treinos Hoje</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_trainings'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-blue-600">{{ $stats['completed_trainings'] }} concluído(s)</span>
                        @if($stats['upcoming_trainings'] > 0)
                        <span class="text-gray-500">{{ $stats['upcoming_trainings'] }} restante(s)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Performance -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Performance</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['performance_score'], 0) }}%</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-5 py-3">
                    <div class="text-sm">
                        @if($stats['performance_change'] > 0)
                        <span class="font-medium text-green-600">+{{ number_format($stats['performance_change'], 1) }}%</span>
                        @elseif($stats['performance_change'] < 0)
                        <span class="font-medium text-red-600">{{ number_format($stats['performance_change'], 1) }}%</span>
                        @else
                        <span class="font-medium text-gray-600">0%</span>
                        @endif
                        <span class="text-gray-500">vs semana anterior</span>
                    </div>
                </div>
            </div>

            <!-- Goals -->
            <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Metas</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_goals'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-red-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-orange-600">{{ $stats['achieved_goals'] }} alcançada(s)</span>
                        @if($stats['in_progress_goals'] > 0)
                        <span class="text-gray-500">{{ $stats['in_progress_goals'] }} em andamento</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- My AI Plans -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Meus Planos de IA</h3>
                    <p class="text-sm text-gray-600">Planos personalizados gerados por inteligência artificial</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentAiContent as $content)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 {{ $content->type === 'workout_plan' ? 'bg-blue-100' : ($content->type === 'meal_plan' ? 'bg-green-100' : 'bg-purple-100') }} rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 {{ $content->type === 'workout_plan' ? 'text-blue-600' : ($content->type === 'meal_plan' ? 'text-green-600' : 'text-purple-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($content->type === 'workout_plan')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            @elseif($content->type === 'meal_plan')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                            @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $content->type)) }}
                                        </h4>
                                        <p class="text-sm text-gray-500">Gerado em {{ $content->generated_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $content->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($content->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>Nenhum plano de IA ainda.</p>
                            <a href="{{ route('portal.ai_plans') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Gerar primeiro plano →
                            </a>
                        </div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('portal.ai_plans') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Ver todos os planos →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Training -->
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Treino de Hoje</h3>
                    <p class="text-sm text-gray-600">Sessões programadas para hoje</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Morning Training -->
                        <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Treino Matinal</h4>
                                    <p class="text-sm text-gray-600">Cardio e força</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">08:00</p>
                                    <p class="text-xs text-gray-500">60 min</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm text-green-600 font-medium">Concluído</span>
                                </div>
                            </div>
                        </div>

                        <!-- Evening Training -->
                        <div class="border-l-4 border-orange-500 bg-orange-50 p-4 rounded-r-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Treino Vespertino</h4>
                                    <p class="text-sm text-gray-600">Técnica e tática</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">18:00</p>
                                    <p class="text-xs text-gray-500">90 min</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-orange-600 font-medium">Em andamento</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-2 px-4 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-medium">
                            Ver Detalhes do Treino
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="mt-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Evolução da Performance</h3>
                            <p class="text-sm text-gray-600">Acompanhe sua evolução ao longo do tempo</p>
                        </div>
                        <select id="performance-period" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="1month">Último mês</option>
                            <option value="3months" selected>Últimos 3 meses</option>
                            <option value="6months">Últimos 6 meses</option>
                            <option value="1year">Último ano</option>
                        </select>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="performance-chart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ações Rápidas</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="{{ route('portal.ai_plans') }}" class="relative group bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-lg border border-purple-200 hover:border-purple-300 hover:shadow-md transition-all">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900">Novo Plano IA</h3>
                                    <p class="text-xs text-gray-500">Gerar plano personalizado</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('portal.profile') }}" class="relative group bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-lg border border-blue-200 hover:border-blue-300 hover:shadow-md transition-all">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900">Meu Perfil</h3>
                                    <p class="text-xs text-gray-500">Atualizar informações</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="relative group bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-lg border border-green-200 hover:border-green-300 hover:shadow-md transition-all">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900">Relatórios</h3>
                                    <p class="text-xs text-gray-500">Ver performance</p>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="relative group bg-gradient-to-r from-orange-50 to-red-50 p-6 rounded-lg border border-orange-200 hover:border-orange-300 hover:shadow-md transition-all">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-gray-900">Suporte</h3>
                                    <p class="text-xs text-gray-500">Falar com técnico</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="{{ asset('js/portal-dashboard.js') }}"></script>
@endsection
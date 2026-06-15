@extends('layouts.portal')

@section('body_class', 'bg-[#0f172a]')
@section('main_class', 'bg-[#0f172a]')

@section('header_styles')
<style>
    .glass-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glass-card:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(74, 222, 128, 0.2);
        transform: translateY(-4px);
    }
    .premium-gradient-bg {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
    }
    .text-glow {
        text-shadow: 0 0 15px rgba(74, 222, 128, 0.4);
    }
    .step-indicator {
        position: relative;
    }
    .step-indicator::after {
        content: '';
        position: absolute;
        left: 20px;
        top: 40px;
        bottom: 0;
        width: 1px;
        background: linear-gradient(to bottom, rgba(74, 222, 128, 0.3), transparent);
    }
    .bento-highlight {
        background: linear-gradient(135deg, rgba(74, 222, 128, 0.1) 0%, transparent 100%);
    }
</style>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
@endsection

@section('content_padding', 'py-0')
@section('content_container', 'max-w-full')

@section('content')
<div class="min-h-screen premium-gradient-bg text-gray-100 font-sans pb-12 w-full">
    <!-- Subscription Warning -->
    @if(!$activeSubscription)
    <div class="max-w-7xl mx-auto pt-6 px-4 sm:px-6 lg:px-8">
        <div class="glass-card p-6 border-l-4 border-yellow-500 bg-yellow-500/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-yellow-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-white font-bold uppercase tracking-tight italic">Assinatura {{ $pendingOrder ? 'Aguardando Pagamento' : 'Pendente' }}</h3>
                    <p class="text-sm text-gray-300">
                        @if($pendingOrder)
                            Sua fatura do plano <span class="text-white font-bold">{{ $pendingOrder->orderItems->first()->product->name ?? '' }}</span> já foi gerada.
                        @else
                            Selecione um plano para continuar sua jornada profissional e ter acesso total aos recursos de IA.
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                @if($pendingOrder && $pendingOrder->asaas_payment_url)
                    <a href="{{ $pendingOrder->asaas_payment_url }}" target="_blank" class="px-8 py-3 bg-green-500 hover:bg-green-400 text-black font-black uppercase tracking-widest text-xs rounded-xl transition-all shadow-lg shadow-green-500/20">
                        PAGAR AGORA
                    </a>
                @endif
                <a href="{{ route('portal.subscriptions') }}" class="px-8 py-3 {{ ($pendingOrder && $pendingOrder->asaas_payment_url) ? 'bg-white/10 text-white hover:bg-white/20' : 'bg-yellow-500 hover:bg-yellow-400 text-black' }} font-black uppercase tracking-widest text-xs rounded-xl transition-all shadow-lg">
                    {{ $pendingOrder ? 'GERENCIAR PLANO' : 'ESCOLHER PLANO' }}
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Top Header -->
    <div class="max-w-7xl mx-auto pt-10 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="animate-in fade-in slide-in-from-left duration-700">
                <h1 class="text-5xl font-black tracking-tighter text-white mb-2 italic">
                    OLÁ, <span class="text-green-400 text-glow">{{ strtoupper(explode(' ', $athlete->full_name ?? Auth::user()->name)[0]) }}</span>
                </h1>
                <p class="text-gray-400 text-lg font-medium">Seu desempenho está <span class="text-green-400 underline decoration-2 underline-offset-4">evoluindo</span> constantemente.</p>
            </div>
            <div class="flex items-center space-x-4 animate-in fade-in slide-in-from-right duration-700">
                <div class="glass-card px-6 py-4 flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-[10px] text-gray-500 uppercase tracking-[0.2em] font-bold">Status Atual</p>
                        <p class="text-sm font-black text-green-400">EM ALTA PERFORMANCE</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-green-400/10 flex items-center justify-center border border-green-400/20">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Athletes Count (If coach) -->
        @if(Auth::user()->isCoach())
        <div class="glass-card p-8 border-l-2 border-green-500/30">
            <span class="text-gray-500 text-xs font-bold uppercase tracking-widest block mb-6">Atletas Sob Gestão</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-white leading-none">{{ Auth::user()->teams->flatMap->athletes->count() ?? 0 }}</span>
            </div>
            <p class="text-xs text-green-400 mt-6 font-bold uppercase tracking-wider">TOTAL DE ALUNOS</p>
        </div>
        @endif
        
        <!-- Performances Recorded -->
        <div class="glass-card p-8 border-l-2 border-green-500/30">
            <span class="text-gray-500 text-xs font-bold uppercase tracking-widest block mb-6">Score Desempenho</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-white leading-none">{{ round($stats['performance_score']) }}</span>
                <span class="text-xl {{ $stats['performance_change'] >= 0 ? 'text-green-400' : 'text-red-400' }} font-bold">
                    {{ $stats['performance_change'] >= 0 ? '+' : '' }}{{ $stats['performance_change'] }}%
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-6 font-bold uppercase tracking-wider">MÉDIA GERAL (3 MESES)</p>
        </div>

        <!-- Training Attendance -->
        <div class="glass-card p-8">
            <div class="flex justify-between items-start mb-6">
                <span class="text-gray-500 text-xs font-bold uppercase tracking-widest">Treinos/Frequência</span>
                <span class="text-blue-400 text-xs font-bold bg-blue-400/10 px-2 py-1 rounded">
                    {{ $stats['total_trainings'] > 0 ? round(($stats['completed_trainings'] / $stats['total_trainings']) * 100) : 0 }}%
                </span>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-white leading-none">{{ $stats['completed_trainings'] }}</span>
                <span class="text-xl text-gray-500 font-bold uppercase">/{{ $stats['total_trainings'] }}</span>
            </div>
            <p class="text-xs text-blue-400 mt-6 font-bold uppercase tracking-wider">{{ $stats['upcoming_trainings'] }} AGENDADOS</p>
        </div>

        <!-- AI Insights -->
        <div class="glass-card p-8">
            <div class="flex justify-between items-start mb-6">
                <span class="text-gray-500 text-xs font-bold uppercase tracking-widest">Estratégias IA</span>
                <div class="flex -space-x-2">
                    @foreach($recentAiContent as $content)
                    <div class="w-7 h-7 rounded-full border-2 border-black {{ $content->type === 'workout_plan' ? 'bg-blue-600' : 'bg-purple-600' }} shadow-lg"></div>
                    @endforeach
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-white leading-none">{{ $stats['total_plans'] }}</span>
            </div>
            <p class="text-xs text-purple-400 mt-6 font-bold uppercase tracking-wider">{{ $stats['active_plans'] }} PLANOS ATIVOS</p>
        </div>

        <!-- Profile Completion (Gamification) -->
        <div class="glass-card p-8 border-l-2 border-blue-500/30 bento-highlight">
            <div class="flex justify-between items-start mb-6">
                <span class="text-gray-500 text-xs font-bold uppercase tracking-widest">Nível do Perfil</span>
                <span class="text-blue-400 text-xs font-bold bg-blue-400/10 px-2 py-1 rounded">
                    {{ $athlete->profile_completion ?? 0 }}%
                </span>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-6xl font-black text-white leading-none">{{ $athlete->is_verified ?? false ? 'V1' : 'V0' }}</span>
                <span class="text-xl text-gray-500 font-bold uppercase">Rank</span>
            </div>
            <div class="mt-6 w-full bg-white/5 rounded-full h-2 overflow-hidden">
                <div class="bg-blue-500 h-full rounded-full shadow-[0_0_10px_#3b82f6] transition-all duration-1000" style="width: {{ $athlete->profile_completion ?? 0 }}%"></div>
            </div>
            <p class="text-[10px] text-gray-400 mt-4 uppercase font-bold">
                @if(($athlete->profile_completion ?? 0) < 100)
                    Complete seu perfil para <span class="text-blue-400">subir de rank</span>
                @else
                    Perfil Verificado <i class="fas fa-check-circle text-blue-400 ml-1"></i>
                @endif
            </p>
        </div>

        <!-- Rank -->
        <div class="glass-card p-8 border-l-2 border-yellow-500/30">
            <span class="text-gray-500 text-xs font-bold uppercase tracking-widest block mb-6">Status no Clube</span>
            <div class="flex items-baseline space-x-2">
                <span class="text-4xl md:text-5xl font-black text-white leading-none truncate" title="{{ strtoupper($athlete->subcategory) ?: 'N/A' }}">{{ strtoupper($athlete->subcategory) ?: 'N/A' }}</span>
            </div>
            <div class="mt-6 inline-flex items-center text-[10px] text-yellow-500 font-black bg-yellow-400/10 px-4 py-2 rounded-full uppercase tracking-widest border border-yellow-500/20">
                <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                Elite Performance
            </div>
        </div>
    </div>

    <!-- Coach Financial Extract (Only for Coaches) -->
    @if(Auth::user()->isCoach())
    <div class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        <div class="glass-card overflow-hidden">
            <div class="px-8 py-6 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-xl font-bold text-white uppercase tracking-tighter italic">Extrato Financeiro</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Histórico de pagamentos recebidos</p>
                </div>
                
                <form action="{{ route('portal.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Início</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-green-400/50">
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-[10px] text-gray-500 uppercase font-bold">Fim</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-green-400/50">
                    </div>
                    <button type="submit" class="p-2 bg-green-400/10 text-green-400 rounded-lg hover:bg-green-400 hover:text-black transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('portal.dashboard') }}" class="p-2 bg-red-400/10 text-red-400 rounded-lg hover:bg-red-400 hover:text-black transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>

                <div class="text-right">
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Saldo Acumulado</p>
                    <p class="text-2xl font-black text-green-400 italic">R$ {{ number_format($coachBalance, 2, ',', '.') }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/5 text-[10px] uppercase tracking-widest font-bold text-gray-500">
                        <tr>
                            <th class="px-8 py-4">Data</th>
                            <th class="px-8 py-4">Descrição</th>
                            <th class="px-8 py-4 text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($coachPayments as $payment)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-8 py-5 text-sm text-gray-400 font-medium">{{ $payment->date->format('d/m/Y') }}</td>
                            <td class="px-8 py-5">
                                <p class="text-sm text-white font-bold">{{ $payment->description }}</p>
                                @if($payment->notes)
                                    <p class="text-xs text-gray-500 mt-1 italic">{{ $payment->notes }}</p>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="text-sm font-black text-green-400 italic">+ R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-10 text-center text-gray-500 text-sm font-bold uppercase italic tracking-widest">
                                Nenhum registro encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Grid -->
    <div class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left: Evolution & Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Evolution Card -->
                <div class="glass-card overflow-hidden">
                    <div class="p-8 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold italic tracking-tighter">PERFIL DE SKILLS</h3>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mt-1">Última avaliação do Coach</p>
                        </div>
                        <div class="flex items-center space-x-4 bg-white/5 px-4 py-2 rounded-xl">
                            <span class="text-xs font-bold text-green-400 bg-green-400/10 px-2 py-1 rounded">LIVE</span>
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                <span class="text-[10px] text-gray-400 uppercase font-bold">Resistência</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-8 h-[350px] relative">
                        <canvas id="evolutionChart"></canvas>
                        @if(empty($skills))
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-3xl m-8">
                                <span class="text-sm text-gray-500 italic font-bold uppercase tracking-widest">Nenhuma avaliação técnica encontrada</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Plans Content -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @php
                        $workout = $recentAiContent->where('type', 'workout_plan')->first();
                        $diet = $recentAiContent->where('type', 'meal_plan')->first();
                    @endphp

                    @if($workout)
                    <div class="bg-white/5 p-8 rounded-3xl border border-white/5 hover:border-blue-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="text-right">
                                <span class="block text-[10px] font-black text-white uppercase tracking-widest">Treino IA</span>
                                <span class="block text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $workout->generated_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed mb-8">O algoritmo de IA preparou sugestões baseadas nos seus últimos resultados...</p>
                        <a href="{{ route('portal.ai-plans') }}" class="inline-flex items-center text-[10px] font-black text-green-400 uppercase tracking-widest group-hover:translate-x-2 transition-transform">
                            Acessar Detalhes
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                    @endif

                    @if($diet)
                    <div class="bg-white/5 p-8 rounded-3xl border border-white/5 hover:border-purple-500/30 transition-all group">
                        <div class="flex items-center justify-between mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/20 flex items-center justify-center text-purple-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                            </div>
                            <div class="text-right">
                                <span class="block text-[10px] font-black text-white uppercase tracking-widest">Dieta IA</span>
                                <span class="block text-[9px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $diet->generated_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed mb-8">Nutrição personalizada para otimizar sua recuperação e ganho de massa...</p>
                        <a href="{{ route('portal.ai-plans') }}" class="inline-flex items-center text-[10px] font-black text-green-400 uppercase tracking-widest group-hover:translate-x-2 transition-transform">
                            Acessar Detalhes
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                    @endif

                    @if(!$workout && !$diet)
                    <div class="lg:col-span-2 glass-card p-12 text-center">
                        <p class="text-gray-500 italic">Nenhuma estratégia gerada. Melhore sua performance com IA hoje.</p>
                        <a href="{{ route('portal.ai-plans') }}" class="mt-8 inline-block px-10 py-4 bg-green-500 text-black font-black uppercase tracking-widest text-xs rounded-full hover:bg-green-400 transition-all shadow-lg shadow-green-500/20">Gerar Estratégia</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right: Agenda -->
            <div class="space-y-8">
                <div class="glass-card p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-bold italic tracking-tighter uppercase">CALENDÁRIO</h3>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Próximos 7 dias</span>
                    </div>
                    <div class="space-y-8">
                        @if($soonestTraining && $soonestTraining->latitude)
                        <div class="mb-8 p-4 bg-white/5 rounded-3xl border border-white/10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black text-green-400 uppercase tracking-widest">Localização em Tempo Real</span>
                                <span id="distance-badge" class="text-[9px] font-bold text-gray-400 bg-white/5 px-2 py-1 rounded">Calculando distância...</span>
                            </div>
                            <div id="training-map" class="h-48 w-full rounded-2xl mb-4 border border-white/5"></div>
                            <a id="btn-route" href="#" target="_blank" class="w-full flex items-center justify-center space-x-2 py-3 bg-blue-600 hover:bg-blue-500 rounded-xl text-[10px] font-black text-white uppercase tracking-widest transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                <span>Como Chegar (Rota)</span>
                            </a>
                        </div>
                        @endif

                        @forelse($upcomingTrainings as $training)
                        <div class="step-indicator pl-12">
                            <a href="{{ route('portal.trainings') }}?id={{ $training->id }}" class="absolute left-0 top-1 w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex flex-col items-center justify-center group-hover:border-green-400/30 transition-all">
                                <span class="text-[9px] font-black text-gray-500 uppercase">{{ \Carbon\Carbon::parse($training->date)->translatedFormat('M') }}</span>
                                <span class="text-lg font-black text-white leading-none">{{ \Carbon\Carbon::parse($training->date)->format('d') }}</span>
                            </a>
                            <a href="{{ route('portal.trainings') }}?id={{ $training->id }}" class="bg-white/5 p-5 rounded-3xl border border-transparent hover:border-white/10 hover:bg-white/10 transition-all cursor-pointer block">
                                <h4 class="font-black text-xs text-white uppercase tracking-wider mb-2">{{ $training->title }}</h4>
                                <div class="flex items-center space-x-4">
                                    <span class="text-[10px] text-gray-500 flex items-center font-bold">
                                        <svg class="w-3 h-3 mr-1.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $training->time }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 flex items-center font-bold">
                                        <svg class="w-3 h-3 mr-1.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        {{ $training->location }}
                                    </span>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/5">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">Sem atividades agendadas</p>
                        </div>
                        @endforelse
                    </div>
                    <a href="{{ route('portal.trainings') }}" class="w-full mt-10 p-5 bg-white/5 hover:bg-white/10 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all border border-white/5 text-center block">Acessar Calendário Completo</a>
                </div>

                <!-- AI Insight Card -->
                <div class="glass-card overflow-hidden relative group">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/20 to-blue-400/20 opacity-30"></div>
                    <div class="p-8 relative">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-green-400 flex items-center justify-center shadow-lg shadow-green-400/20">
                                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            </div>
                            <h3 class="font-black italic uppercase tracking-tighter">INSIGHT IA</h3>
                        </div>
                        <p class="text-sm text-gray-300 leading-relaxed italic mb-8">
                            "{{ $aiInsight }}"
                        </p>
                        <a href="{{ route('portal.ai-plans') }}" class="w-full bg-green-400 hover:bg-green-300 py-4 rounded-xl text-black text-[10px] font-black uppercase tracking-widest transition-all text-center block">Ver Recomendação</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    function switchTab(tab, element) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.replace('border-green-400', 'border-transparent') && b.classList.add('text-gray-500'));
        
        document.getElementById('tab-' + tab).classList.remove('hidden');
        element.classList.replace('text-gray-500', 'text-white');
        element.classList.add('border-green-400');
    }

    (function() {
        console.log('Iniciando script do Gráfico de Skills...');
        const canvas = document.getElementById('evolutionChart');
        if (!canvas) {
            console.warn('Canvas evolutionChart não encontrado');
            return;
        }
        
        const ctx = canvas.getContext('2d');
        
        // Dados vindos do PHP (array com 'labels' e 'values')
        let skillsDataRaw = { labels: [], values: [] };
        try {
            skillsDataRaw = @json($skills);
            console.log('Dados de Skills recebidos:', skillsDataRaw);
        } catch (e) {
            console.error('Erro ao parsear dados de skills:', e);
        }
        
        const defaultLabels = ['Técnica', 'Velocidade', 'Força', 'Resistência', 'Tática', 'Mental'];
        const defaultValues = [0, 0, 0, 0, 0, 0];

        let labels = (skillsDataRaw && skillsDataRaw.labels) ? skillsDataRaw.labels : [];
        let values = (skillsDataRaw && skillsDataRaw.values) ? skillsDataRaw.values : [];

        if (labels.length === 0) {
            labels = defaultLabels;
            values = defaultValues;
            console.log('Usando labels padrão para o gráfico');
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js não carregado! Tentando aguardar...');
            setTimeout(renderChart, 1000);
            return;
        }

        renderChart();

        function renderChart() {
            try {
                if (window.skillsChart instanceof Chart) {
                    window.skillsChart.destroy();
                }

                window.skillsChart = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nível Atual',
                            data: values,
                            backgroundColor: 'rgba(74, 222, 128, 0.2)',
                            borderColor: '#4ade80',
                            borderWidth: 2,
                            pointBackgroundColor: '#4ade80',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#4ade80'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            r: {
                                angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                pointLabels: {
                                    color: 'rgba(255, 255, 255, 0.7)',
                                    font: { size: 10, weight: 'bold' }
                                },
                                ticks: { display: false, stepSize: 20 },
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        }
                    }
                });
                console.log('Gráfico de Skills renderizado com sucesso');
            } catch (err) {
                console.error('Erro ao renderizar gráfico:', err);
            }
        }

        // Mapbox for Portal
        @if($soonestTraining && $soonestTraining->latitude)
        (function() {
            mapboxgl.accessToken = '{{ $settings['mapbox_public_token'] ?? '' }}';
            
            const dest = [{{ $soonestTraining->longitude }}, {{ $soonestTraining->latitude }}];
            
            const map = new mapboxgl.Map({
                container: 'training-map',
                style: 'mapbox://styles/mapbox/dark-v11',
                center: dest,
                zoom: 14,
                attributionControl: false
            });

            new mapboxgl.Marker({ color: '#4ade80' })
                .setLngLat(dest)
                .addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    const userLoc = [position.coords.longitude, position.coords.latitude];
                    
                    // Add user marker
                    new mapboxgl.Marker({ color: '#3b82f6' })
                        .setLngLat(userLoc)
                        .addTo(map);

                    // Zoom to fit both
                    const bounds = new mapboxgl.LngLatBounds()
                        .extend(userLoc)
                        .extend(dest);
                    map.fitBounds(bounds, { padding: 50 });

                    // Calculate Distance (Simple Haversine)
                    const dist = calculateDistance(userLoc[1], userLoc[0], dest[1], dest[0]);
                    document.getElementById('distance-badge').innerText = dist.toFixed(1) + ' km de você';

                    // Route Button (Google Maps fallback for better native navigation)
                    document.getElementById('btn-route').href = `https://www.google.com/maps/dir/?api=1&origin=${userLoc[1]},${userLoc[0]}&destination=${dest[1]},${dest[0]}`;
                }, (error) => {
                    console.warn('Geolocation error:', error);
                    const badge = document.getElementById('distance-badge');
                    if (badge) badge.innerText = 'Localização negada';
                });
            }

            function calculateDistance(lat1, lon1, lat2, lon2) {
                const R = 6371;
                const dLat = (lat2-lat1) * Math.PI / 180;
                const dLon = (lon2-lon1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }
        })();
        @endif
    })();
</script>
@endsection
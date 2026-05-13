@extends('layouts.admin')

@section('title', 'Painel de Controle')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Payment Alert (Tenant Specific) -->
    @if(tenant('status') === 'pending')
        <div class="mb-10 p-6 bg-orange-500/10 border border-orange-500/20 rounded-[2rem] flex flex-col md:flex-row items-center gap-6 animate__animated animate__pulse animate__infinite animate__slower">
            <div class="w-16 h-16 bg-orange-500/20 text-orange-500 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="text-center md:text-left flex-1">
                <h3 class="text-xl font-black text-white tracking-tight">Acesso Restrito: Pendente de Regularização</h3>
                <p class="text-sm text-gray-400 mt-1">Sua conta está em modo de leitura. Para habilitar todas as funcionalidades de gestão, regularize sua mensalidade.</p>
            </div>
            <a href="#" class="px-8 py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-orange-600/20 hover:bg-orange-700 transition-all">Pagar via PIX</a>
        </div>
    @endif

    <!-- Performance Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Athletes Card -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full group-hover:bg-indigo-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-6 relative">
                <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl group-hover:bg-indigo-500 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                @if($stats['athlete_trend'] >= 0)
                    <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">+{{ $stats['athlete_trend'] }}%</span>
                @else
                    <span class="text-[10px] font-black text-rose-500 bg-rose-500/10 px-2 py-1 rounded-lg border border-rose-500/20">{{ $stats['athlete_trend'] }}%</span>
                @endif
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Total de Atletas</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $stats['total_athletes'] }}</h3>
        </div>

        <!-- Active Card -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-600/10 blur-[50px] rounded-full group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-6 relative">
                <div class="p-3 bg-emerald-500/10 text-emerald-400 rounded-xl group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/20">ATIVOS</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Status Ativo</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $stats['active_athletes'] }}</h3>
        </div>

        <!-- Teams Card -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-purple-600/10 blur-[50px] rounded-full group-hover:bg-purple-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-6 relative">
                <div class="p-3 bg-purple-500/10 text-purple-400 rounded-xl group-hover:bg-purple-500 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <span class="text-[10px] font-black text-purple-500 bg-purple-500/10 px-2 py-1 rounded-lg border border-purple-500/20">{{ $stats['total_teams'] }} ELENCOS</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Equipes / Grupos</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $stats['total_teams'] }}</h3>
        </div>

        @if(auth()->user()->role === 'admin')
        <!-- Revenue Card (Admin Only) -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-orange-600/10 blur-[50px] rounded-full group-hover:bg-orange-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-6 relative">
                <div class="p-3 bg-orange-500/10 text-orange-400 rounded-xl group-hover:bg-orange-500 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </div>
                <span class="text-[10px] font-black text-orange-500 bg-orange-500/10 px-2 py-1 rounded-lg border border-orange-500/20">ESTE MÊS</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Receita Mensal</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($stats['this_month_revenue'], 2, ',', '.') }}</h3>
        </div>
        @else
        <!-- AI Requests Card (Coach Only) -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full group-hover:bg-indigo-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-6 relative">
                <div class="p-3 bg-indigo-500/10 text-indigo-400 rounded-xl group-hover:bg-indigo-500 group-hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-[10px] font-black text-indigo-500 bg-indigo-500/10 px-2 py-1 rounded-lg border border-indigo-500/20">PENDENTES</span>
            </div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Solicitações IA</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $stats['pending_ai_requests'] }}</h3>
        </div>
        @endif
    </div>

    <!-- Quick Actions & Alerts -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        <!-- Quick Actions Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-[#0F1423] rounded-[2.5rem] p-8 shadow-2xl border border-white/5">
                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-8 px-2">Ações Estratégicas</h3>
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('admin.athletes.create') }}" class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 hover:border-indigo-500/50 hover:-translate-y-1 transition-all group">
                        <div class="w-12 h-12 bg-indigo-500/10 text-indigo-400 rounded-xl flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white">Novo Atleta</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Registrar contrato</p>
                        </div>
                    </a>
                    <a href="{{ route('trainings.index') }}" class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 hover:border-emerald-500/50 hover:-translate-y-1 transition-all group">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white">Novo Treino</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Agendar sessão</p>
                        </div>
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.cash-flow.index') }}" class="flex items-center gap-4 p-5 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 hover:border-orange-500/50 hover:-translate-y-1 transition-all group">
                        <div class="w-12 h-12 bg-orange-500/10 text-orange-400 rounded-xl flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-white">Lançamento</p>
                            <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Fluxo financeiro</p>
                        </div>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Pending AI Requests Alert Box -->
            @if($recent_ai_requests->count() > 0)
            <div class="bg-[#0F1423] rounded-[2.5rem] p-8 shadow-2xl border border-white/5">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest">Solicitações IA</h3>
                    <span class="w-2 h-2 bg-indigo-500 rounded-full animate-ping"></span>
                </div>
                <div class="space-y-4">
                    @foreach($recent_ai_requests as $request)
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/5 group hover:bg-white/10 transition-all">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <img src="{{ $request->athlete->profile_picture_url }}" class="w-8 h-8 rounded-full object-cover">
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-white truncate">{{ $request->athlete->full_name }}</p>
                                <p class="text-[8px] text-indigo-400 font-black uppercase tracking-widest mt-0.5">{{ $request->type === 'workout_plan' ? 'Plano Treino' : 'Plano Nutri' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.athletes.show', $request->athlete_id) }}" class="p-2 text-gray-500 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Charts and Evolution -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">Atletas em Destaque</h3>
                        <p class="text-xs text-gray-500 mt-1 uppercase font-bold tracking-widest">Top 5 performance recente</p>
                    </div>
                    <a href="{{ route('admin.athletes.index') }}" class="text-[10px] font-black text-indigo-400 uppercase tracking-widest hover:text-indigo-300 transition-colors">Ver Ranking Completo →</a>
                </div>
                
                <div class="space-y-6">
                    @forelse($top_athletes as $record)
                    <div class="flex items-center justify-between p-6 bg-white/[0.02] rounded-3xl border border-white/5 hover:bg-white/[0.05] transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="{{ $record->athlete->profile_picture_url }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-white/5 group-hover:ring-indigo-500/50 transition-all">
                                <div class="absolute -top-2 -left-2 w-6 h-6 bg-indigo-600 rounded-lg flex items-center justify-center text-[10px] font-black text-white shadow-lg">
                                    {{ $loop->iteration }}
                                </div>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-white leading-none mb-2">{{ $record->athlete->full_name }}</h4>
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $record->athlete->team->name ?? 'Geral' }}</span>
                                    <span class="w-1 h-1 bg-white/10 rounded-full"></span>
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $record->athlete->position ?? 'Base' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-black text-indigo-500 tracking-tighter">{{ round($record->avg_score, 1) }}</div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Score Técnico</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-20 border-2 border-dashed border-white/5 rounded-3xl">
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Aguardando Avaliações</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
                <div class="p-8 border-b border-white/5 flex items-center justify-between">
                    <h3 class="text-lg font-black text-white tracking-tight">Novas Inscrições</h3>
                    <span class="px-3 py-1 bg-white/5 text-gray-400 text-[10px] font-black rounded-lg uppercase tracking-widest">Últimos Atletas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-white/5">
                            @foreach($recent_athletes as $athlete)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $athlete->profile_picture_url }}" class="w-10 h-10 rounded-xl object-cover grayscale group-hover:grayscale-0 transition-all">
                                        <div>
                                            <p class="text-sm font-black text-white leading-none mb-1">{{ $athlete->full_name }}</p>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ $athlete->team->name ?? 'Sem Equipe' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.athletes.show', $athlete) }}" class="px-4 py-2 bg-white/5 text-gray-400 hover:text-white hover:bg-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Perfil →</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

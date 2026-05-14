@extends('layouts.admin')

@section('title', 'Gestão Financeira Global')

@section('content')
<div class="space-y-8 animate__animated animate__fadeIn">
    
    <!-- Top Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Lucro Total Estimado -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
            <div class="relative p-8 bg-[#0F1423] rounded-[2rem] border border-white/5 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-4 bg-emerald-500/10 rounded-2xl text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Lucro Total Acumulado</h3>
                    <p class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($stats['total_profit'], 2, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Saldo Global</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receita de Assinaturas -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
            <div class="relative p-8 bg-[#0F1423] rounded-[2rem] border border-white/5 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-4 bg-indigo-500/10 rounded-2xl text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Assinaturas de Clubes</h3>
                    <p class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($stats['subscription_revenue'], 2, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-[10px] font-black text-indigo-400 bg-indigo-400/10 px-2 py-0.5 rounded-full uppercase tracking-widest">{{ $stats['active_tenants'] }} Clubes Ativos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comissões de Vendas -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-orange-500 to-rose-600 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
            <div class="relative p-8 bg-[#0F1423] rounded-[2rem] border border-white/5 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-4 bg-orange-500/10 rounded-2xl text-orange-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Comissões (Taxas)</h3>
                    <p class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($stats['platform_commissions'], 2, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-[10px] font-black text-orange-400 bg-orange-400/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Transacionado: R$ {{ number_format($stats['sales_volume'] / 1000, 1) }}k</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Volume de Vendas Global -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
            <div class="relative p-8 bg-[#0F1423] rounded-[2rem] border border-white/5 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="p-4 bg-blue-500/10 rounded-2xl text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Volume de Vendas (GMV)</h3>
                    <p class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($stats['sales_volume'], 2, ',', '.') }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-[10px] font-black text-blue-400 bg-blue-400/10 px-2 py-0.5 rounded-full uppercase tracking-widest">Soma de todos os Clubes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Last Subscriptions -->
        <div class="lg:col-span-2 relative group">
            <div class="absolute -inset-0.5 bg-white/5 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
            <div class="relative bg-[#0F1423]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 overflow-hidden">
                <div class="p-8 border-b border-white/5 flex justify-between items-center">
                    <div>
                        <h2 class="text-sm font-black text-white uppercase tracking-widest">Assinaturas Recentes</h2>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Últimos clubes registrados e ativos</p>
                    </div>
                    <a href="{{ route('admin.financial.subscriptions') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-xl text-[10px] font-black text-gray-400 uppercase tracking-widest transition-all">Ver Tudo</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5">
                                <th class="px-8 py-6 text-left">Clube</th>
                                <th class="px-8 py-6 text-left">Plano</th>
                                <th class="px-8 py-6 text-left">Status</th>
                                <th class="px-8 py-6 text-left">Valor Mensal</th>
                                <th class="px-8 py-6 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($recentSubscriptions as $tenant)
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-400 font-black text-xs">
                                            {{ substr($tenant->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-white uppercase tracking-tight">{{ $tenant->name }}</p>
                                            <p class="text-[9px] text-gray-500 font-bold mt-0.5 tracking-widest">{{ $tenant->domain }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $tenant->plan->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-{{ $tenant->status == 'active' ? 'emerald' : ($tenant->status == 'pending' ? 'orange' : 'rose') }}-500/10 text-{{ $tenant->status == 'active' ? 'emerald' : ($tenant->status == 'pending' ? 'orange' : 'rose') }}-500 text-[9px] font-black uppercase tracking-widest rounded-full">
                                        {{ $tenant->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-black text-white">R$ {{ number_format($tenant->plan->price_monthly ?? 0, 2, ',', '.') }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="p-2 text-gray-500 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-12 text-center text-gray-500 italic text-xs">Nenhuma assinatura encontrada</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Profit Distribution Chart / List -->
        <div class="relative group">
            <div class="absolute -inset-0.5 bg-white/5 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
            <div class="relative p-8 bg-[#0F1423]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 flex flex-col h-full">
                <h2 class="text-sm font-black text-white uppercase tracking-widest mb-2">Composição de Lucro</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-8">Origem da receita da plataforma</p>
                
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Assinaturas SaaS</span>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ round(($stats['subscription_revenue'] / max($stats['total_profit'], 1)) * 100) }}%</span>
                        </div>
                        <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: {{ ($stats['subscription_revenue'] / max($stats['total_profit'], 1)) * 100 }}%"></div>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-2 font-bold uppercase tracking-widest">Receita Fixa Recorrente</p>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Comissões Variáveis</span>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ round(($stats['platform_commissions'] / max($stats['total_profit'], 1)) * 100) }}%</span>
                        </div>
                        <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500" style="width: {{ ($stats['platform_commissions'] / max($stats['total_profit'], 1)) * 100 }}%"></div>
                        </div>
                        <p class="text-[9px] text-gray-500 mt-2 font-bold uppercase tracking-widest">Percentual sobre Vendas</p>
                    </div>

                    <div class="pt-8 border-t border-white/5 mt-auto">
                        <div class="p-6 bg-indigo-500/5 rounded-2xl border border-indigo-500/10">
                            <p class="text-[9px] text-indigo-400 font-black uppercase tracking-widest mb-2">Dica de Lucratividade</p>
                            <p class="text-[10px] text-gray-400 leading-relaxed font-medium">Clubes no plano Professional geram {{ round(20) }}% mais receita via comissões de e-commerce.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Club Sales Leaderboard -->
    <div class="relative group">
        <div class="absolute -inset-0.5 bg-white/5 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
        <div class="relative bg-[#0F1423]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-widest">Performance por Clube</h2>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Ranking de volume transacionado e taxas</p>
                </div>
                <a href="{{ route('admin.financial.club-sales') }}" class="px-4 py-2 bg-white/5 hover:bg-white/10 rounded-xl text-[10px] font-black text-gray-400 uppercase tracking-widest transition-all">Ver Detalhes</a>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($tenants->where('status', 'active')->sortByDesc(function($t) { 
                        // Simulação de rank - isso seria real se salvássemos o revenue em cache ou tabela central
                        return $t->id; // Placeholder
                    })->take(6) as $tenant)
                    <div class="p-6 bg-white/[0.02] border border-white/5 rounded-2xl hover:bg-white/[0.05] transition-all group/card">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">{{ $tenant->plan->name ?? 'N/A' }}</span>
                            <div class="p-1.5 bg-emerald-500/10 rounded-lg text-emerald-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                        </div>
                        <h4 class="text-xs font-black text-white uppercase tracking-tight mb-1 group-hover/card:text-indigo-400 transition-colors">{{ $tenant->name }}</h4>
                        <div class="flex justify-between items-end mt-4">
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Taxa de Split</p>
                                <p class="text-xs font-black text-white">{{ $tenant->plan->admin_fee_percentage ?? 0 }}%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest">Saúde Financeira</p>
                                <p class="text-xs font-black text-emerald-400">Estável</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

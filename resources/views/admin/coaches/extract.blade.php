@extends('layouts.admin')

@section('title', auth()->user()->role === 'admin' ? 'Extrato de ' . $targetCoach->name : 'Meu Extrato Financeiro')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">
                {{ auth()->user()->role === 'admin' ? 'Extrato Financeiro de ' . $targetCoach->name : 'Meu Extrato Financeiro' }}
            </h1>
            <p class="text-gray-400 mt-2">Acompanhe seus rendimentos, bônus e histórico de transações em tempo real.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-6 py-3 bg-white/5 border border-white/10 rounded-2xl text-gray-300 hover:bg-white/10 hover:text-white transition-all flex items-center gap-2 font-bold text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Exportar Relatório
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Saldo Atual -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full group-hover:bg-indigo-600/20 transition-all duration-700"></div>
            
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-3">Saldo Disponível</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">
                <span class="text-indigo-500 text-2xl mr-1">R$</span>{{ number_format($stats['total_balance'], 2, ',', '.') }}
            </h3>
            <div class="mt-6 flex items-center text-[10px] font-black text-indigo-400 bg-indigo-500/5 border border-indigo-500/10 px-4 py-2 rounded-xl w-fit uppercase tracking-widest">
                <span class="relative flex h-2 w-2 mr-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                Atualizado agora
            </div>
        </div>

        <!-- Total Entradas -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-600/10 blur-[50px] rounded-full group-hover:bg-emerald-600/20 transition-all duration-700"></div>
            
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-3">Total de Ganhos</p>
            <h3 class="text-4xl font-black text-emerald-500 tracking-tighter">
                <span class="text-emerald-600 text-2xl mr-1">R$</span>{{ number_format($stats['entries'], 2, ',', '.') }}
            </h3>
            <div class="mt-6 flex items-center text-[10px] font-black text-emerald-500/80 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                Soma de créditos
            </div>
        </div>

        <!-- Total Saídas -->
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-rose-600/10 blur-[50px] rounded-full group-hover:bg-rose-600/20 transition-all duration-700"></div>
            
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-3">Total de Retiradas</p>
            <h3 class="text-4xl font-black text-rose-500 tracking-tighter">
                <span class="text-rose-600 text-2xl mr-1">R$</span>{{ number_format($stats['exits'], 2, ',', '.') }}
            </h3>
            <div class="mt-6 flex items-center text-[10px] font-black text-rose-500/80 uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0-7-7m7 7V3"/></svg>
                Resgates efetuados
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
        <div class="p-8 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white/[0.02]">
            <div>
                <h2 class="text-xl font-black text-white tracking-tight">Histórico Detalhado</h2>
                <p class="text-xs text-gray-500 mt-1 uppercase font-bold tracking-widest">Listando últimos lançamentos</p>
            </div>
            
            <div class="flex items-center gap-3">
                <select class="px-5 py-3 rounded-2xl bg-white/5 border-white/10 text-xs font-bold text-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    <option value="">Todos os Períodos</option>
                    <option value="current">Mês Atual</option>
                </select>
                <select class="px-5 py-3 rounded-2xl bg-white/5 border-white/10 text-xs font-bold text-gray-300 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    <option value="">Todas Categorias</option>
                    <option value="entry">Entradas</option>
                    <option value="exit">Saídas</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.01]">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Data Operação</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Descrição / Referência</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Valor Líquido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm text-white font-black">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-200 group-hover:text-white transition-colors">{{ $transaction->description }}</span>
                                <span class="text-[10px] font-black text-indigo-500/60 uppercase tracking-widest mt-1">{{ $transaction->category }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            @if($transaction->status === 'completed')
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                    Efetivado
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                    Pendente
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            {{-- Para o Treinador, uma saída (exit) do clube é uma entrada (Crédito) para ele --}}
                            <span class="text-lg font-black {{ $transaction->type === 'exit' ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $transaction->type === 'exit' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-gray-700 mb-6">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-sm">Nenhuma movimentação registrada</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
        <div class="p-8 bg-white/[0.01] border-t border-white/5">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    <!-- Security Info -->
    <div class="mt-12 p-8 bg-indigo-500/5 border border-indigo-500/10 rounded-[2rem] flex flex-col md:flex-row gap-6 items-center">
        <div class="w-16 h-16 bg-indigo-500/10 text-indigo-400 rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div class="text-center md:text-left">
            <h4 class="text-lg font-black text-white tracking-tight">Transparência Financeira</h4>
            <p class="text-sm text-gray-400 mt-1 max-w-2xl">
                Todos os lançamentos são auditados e validados pela gestão central do clube. Caso encontre qualquer inconsistência em seus ganhos ou retiradas, por favor, abra um chamado técnico imediato através do módulo de mensagens.
            </p>
        </div>
    </div>
</div>
@endsection

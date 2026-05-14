@extends('layouts.admin')

@section('title', 'Minhas Faturas')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Minhas Faturas</h1>
            <p class="text-gray-500 mt-2 font-medium tracking-wide">Gerencie seu histórico de mensalidades e pagamentos da plataforma.</p>
        </div>
        
        @if($latestPendingInvoice)
            <a href="{{ route('admin.billing.pay') }}" target="_blank" class="px-8 py-4 bg-orange-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-orange-600/20 hover:bg-orange-700 hover:-translate-y-1 transition-all">
                Pagar Fatura Atual (PIX)
            </a>
        @endif
    </div>

    <!-- Subscription Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="bg-[#0F1423] rounded-[2.5rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Plano Atual</p>
            <h3 class="text-2xl font-black text-white tracking-tight mb-2">{{ $tenant->plan->name ?? 'Plano Profissional' }}</h3>
            <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest">
                {{ $tenant->status === 'active' ? 'Ativo' : 'Pendente' }}
            </span>
        </div>

        <div class="bg-[#0F1423] rounded-[2.5rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-600/10 blur-[50px] rounded-full"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Próximo Vencimento</p>
            <h3 class="text-2xl font-black text-white tracking-tight mb-2">
                {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : 'Não definido' }}
            </h3>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Renovação Automática</p>
        </div>

        <div class="bg-[#0F1423] rounded-[2.5rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-orange-600/10 blur-[50px] rounded-full"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-4">Valor Mensal</p>
            <h3 class="text-2xl font-black text-white tracking-tight mb-2">
                R$ {{ number_format($tenant->plan->price_monthly ?? 0, 2, ',', '.') }}
            </h3>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Faturamento via Asaas</p>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-lg font-black text-white tracking-tight">Histórico de Cobranças</h3>
            <div class="flex items-center gap-4">
                <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sincronizado em Tempo Real</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Data</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Descrição</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Valor</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-white">{{ \Carbon\Carbon::parse($invoice['dueDate'])->format('d/m/Y') }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">ID: {{ substr($invoice['id'], 0, 12) }}...</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-gray-300">{{ $invoice['description'] ?? 'Mensalidade Plataforma' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ $invoice['billingType'] }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-white">R$ {{ number_format($invoice['value'], 2, ',', '.') }}</p>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $statusMap = [
                                    'RECEIVED' => ['label' => 'Pago', 'class' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'],
                                    'CONFIRMED' => ['label' => 'Confirmado', 'class' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'],
                                    'PENDING' => ['label' => 'Pendente', 'class' => 'bg-orange-500/10 text-orange-500 border-orange-500/20'],
                                    'OVERDUE' => ['label' => 'Atrasado', 'class' => 'bg-rose-500/10 text-rose-500 border-rose-500/20'],
                                    'CANCELLED' => ['label' => 'Cancelado', 'class' => 'bg-white/5 text-gray-500 border-white/5'],
                                ];
                                $status = $statusMap[$invoice['status']] ?? ['label' => $invoice['status'], 'class' => 'bg-white/5 text-gray-500 border-white/5'];
                            @endphp
                            <span class="px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-widest {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            @if(isset($invoice['invoiceUrl']) && ($invoice['status'] === 'PENDING' || $invoice['status'] === 'OVERDUE'))
                                <a href="{{ $invoice['invoiceUrl'] }}" target="_blank" class="px-4 py-2 bg-orange-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-600/20">
                                    Pagar PIX
                                </a>
                            @elseif(isset($invoice['bankSlipUrl']))
                                <a href="{{ $invoice['bankSlipUrl'] }}" target="_blank" class="px-4 py-2 bg-white/5 text-gray-400 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                    Boleto
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-50">
                                <svg class="w-12 h-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm font-black text-gray-500 uppercase tracking-widest">Nenhuma fatura encontrada</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

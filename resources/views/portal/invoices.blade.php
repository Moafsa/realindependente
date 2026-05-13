@extends('layouts.portal')

@section('title', 'Minhas Faturas')
@section('body_class', 'bg-[#0f172a]')
@section('main_class', 'bg-[#0f172a]')
@section('content_padding', 'py-0')
@section('content_container', 'max-w-full')

@section('header_styles')
<style>
    .glass-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
    }
    .premium-gradient-bg {
        background: radial-gradient(circle at top right, #1e293b, #0f172a);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen premium-gradient-bg text-gray-100 font-sans pb-12 w-full">
    <div class="max-w-7xl mx-auto pt-10 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-4xl font-black italic tracking-tighter text-white uppercase">Histórico de <span class="text-green-400">Pagamentos</span></h1>
                <p class="text-gray-400 mt-2">Acompanhe suas faturas e o status de cada transação.</p>
            </div>
            <a href="{{ route('portal.subscriptions') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-bold uppercase tracking-widest transition-all border border-white/10">
                Gerenciar Plano
            </a>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Fatura #</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Data</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Produto</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Valor</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-widest">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($orders as $order)
                        <tr class="hover:bg-white/2 transition-colors group">
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-white">#{{ $order->id }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm text-gray-400">{{ $order->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm text-white font-medium italic uppercase tracking-tight">
                                    {{ $order->orderItems->first()->product->name ?? 'Serviço' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-black text-white">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @if($order->status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-green-400/10 text-green-400 uppercase tracking-widest border border-green-400/20">
                                        PAGO
                                    </span>
                                @elseif($order->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-yellow-500/10 text-yellow-500 uppercase tracking-widest border border-yellow-500/20">
                                        PENDENTE
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-red-500/10 text-red-500 uppercase tracking-widest border border-red-500/20">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if($order->status === 'pending' && $order->asaas_payment_url)
                                    <a href="{{ $order->asaas_payment_url }}" target="_blank" class="px-4 py-2 bg-green-500 hover:bg-green-400 text-black text-[10px] font-black uppercase tracking-widest rounded-lg transition-all shadow-lg shadow-green-500/20">
                                        Pagar Agora
                                    </a>
                                @else
                                    <span class="text-xs text-gray-600 italic">Sem ações</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center text-gray-500 italic">
                                Nenhuma fatura encontrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="px-8 py-6 border-t border-white/5">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

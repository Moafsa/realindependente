@extends('layouts.portal')

@section('title', 'Minha Assinatura')
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
                <h1 class="text-4xl font-black italic tracking-tighter text-white uppercase">Gestão de <span class="text-green-400">Plano</span></h1>
                <p class="text-gray-400 mt-2">Gerencie sua assinatura e acesso aos recursos de IA.</p>
            </div>
            <a href="{{ route('portal.invoices') }}" class="px-6 py-3 bg-white/5 hover:bg-white/10 rounded-xl text-xs font-bold uppercase tracking-widest transition-all border border-white/10">
                Ver Faturas
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Current Plan Status -->
            <div class="lg:col-span-1 space-y-6">
                <div class="glass-card p-8 border-l-4 border-green-400">
                    <span class="text-[10px] text-gray-500 uppercase font-black tracking-[0.2em] mb-4 block">Plano Atual</span>
                    @php
                        $activeOrder = $orders->where('status', 'paid')->first();
                        $pendingOrder = $orders->where('status', 'pending')->first();
                        $currentPlanId = null;
                        if ($activeOrder && $activeOrder->orderItems->first()) {
                            $currentPlanId = $activeOrder->orderItems->first()->product_id;
                        } elseif ($pendingOrder && $pendingOrder->orderItems->first()) {
                            $currentPlanId = $pendingOrder->orderItems->first()->product_id;
                        }
                    @endphp

                    @if($activeOrder)
                        <h2 class="text-3xl font-black text-white italic uppercase mb-2">{{ $activeOrder->orderItems->first()->product->name ?? 'Plano Profissional' }}</h2>
                        <div class="flex items-center text-green-400 text-xs font-bold bg-green-400/10 px-3 py-1 rounded-full w-fit mb-6">
                            <span class="w-2 h-2 rounded-full bg-green-400 mr-2"></span>
                            ATIVO
                        </div>
                        <p class="text-sm text-gray-400 mb-8 leading-relaxed">Sua assinatura está ativa e sendo renovada automaticamente pelo Asaas.</p>
                    @elseif($pendingOrder)
                        <h2 class="text-3xl font-black text-white italic uppercase mb-2">{{ $pendingOrder->orderItems->first()->product->name ?? 'Aguardando Ativação' }}</h2>
                        <div class="flex items-center text-yellow-500 text-xs font-bold bg-yellow-500/10 px-3 py-1 rounded-full w-fit mb-6">
                            <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span>
                            AGUARDANDO PAGAMENTO
                        </div>
                        @if($pendingOrder->asaas_payment_url)
                            <a href="{{ $pendingOrder->asaas_payment_url }}" target="_blank" class="w-full block text-center py-4 bg-green-500 hover:bg-green-400 text-black font-black uppercase tracking-widest text-xs rounded-xl transition-all shadow-lg shadow-green-500/20 mb-4">
                                PAGAR FATURA AGORA
                            </a>
                        @endif
                    @else
                        <h2 class="text-3xl font-black text-white italic uppercase mb-2">Sem Plano Ativo</h2>
                        <p class="text-sm text-gray-400 mb-8">Escolha um dos planos abaixo para começar sua jornada.</p>
                    @endif
                </div>

                <div class="glass-card p-8">
                    <h3 class="text-sm font-black text-white uppercase mb-6 tracking-widest italic">Benefícios Inclusos</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-300">Treinos Gerados por IA</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-300">Acompanhamento Nutricional</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-sm text-gray-300">Histórico de Performance</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Available Plans (Upgrade/Downgrade) -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($availablePlans as $plan)
                    <div class="glass-card p-8 hover:border-green-400/40 transition-all group relative overflow-hidden">
                        @if($currentPlanId == $plan->id)
                            <div class="absolute top-0 right-0 bg-green-500 text-black text-[10px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-widest">
                                {{ $activeOrder && $activeOrder->orderItems->first()->product_id == $plan->id ? 'Atual' : 'Selecionado' }}
                            </div>
                        @endif
                        
                        <h4 class="text-2xl font-black text-white italic uppercase mb-2">{{ $plan->name }}</h4>
                        <div class="flex items-baseline mb-6">
                            <span class="text-3xl font-black text-white">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                            <span class="text-gray-500 text-xs ml-2 font-bold uppercase">/ Mês</span>
                        </div>
                        
                        <p class="text-xs text-gray-400 leading-relaxed mb-8">{{ $plan->description }}</p>
                        
                        @if($currentPlanId != $plan->id)
                            <a href="{{ route('site.subscribe', $plan->id) }}" class="w-full block text-center py-4 rounded-xl border border-white/10 text-white hover:bg-green-500 hover:text-black hover:border-green-500 font-black text-[10px] uppercase tracking-widest transition-all">
                                {{ $activeOrder ? 'MUDAR PARA ESTE PLANO' : 'MATRICULAR-SE AGORA' }}
                            </a>
                        @else
                            <button disabled class="w-full py-4 rounded-xl border border-green-500/30 text-green-500/50 font-black text-[10px] uppercase tracking-widest cursor-not-allowed">
                                {{ $activeOrder && $activeOrder->orderItems->first()->product_id == $plan->id ? 'SEU PLANO ATUAL' : 'PLANO JÁ SELECIONADO' }}
                            </button>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 glass-card p-6 bg-blue-500/5 border-blue-500/20">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white uppercase tracking-tight italic">Sobre Upgrade/Downgrade</h4>
                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">Ao trocar de plano, sua assinatura anterior será cancelada no Asaas e uma nova fatura será gerada. O acesso aos recursos de IA será atualizado assim que o pagamento for confirmado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

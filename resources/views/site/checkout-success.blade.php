@extends('layouts.site')

@section('title', 'Pedido Confirmado')
@section('description', 'Seu pedido foi confirmado com sucesso')

@section('content')
<!-- Success Section -->
<section class="py-20 bg-gradient-to-br from-green-50 to-blue-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Pedido Confirmado!</h1>
            <p class="text-lg text-gray-600 mb-8">
                Seu pedido foi recebido com sucesso. Você receberá um e-mail de confirmação em breve.
            </p>

            @if($order ?? null)
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalhes do Pedido</h2>
                <dl class="space-y-2 text-left">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Número do Pedido:</dt>
                        <dd class="font-semibold text-gray-900">#{{ $order->id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Total:</dt>
                        <dd class="font-semibold text-gray-900">R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Status:</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($order->status ?? 'Pendente') }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <div class="space-y-4">
                <a href="{{ route('site.store') }}" 
                   class="inline-block w-full md:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    Continuar Comprando
                </a>
                @if($order ?? null)
                <a href="{{ route('site.home') }}" 
                   class="inline-block w-full md:w-auto px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Voltar ao Início
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection


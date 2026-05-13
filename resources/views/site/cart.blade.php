@extends('layouts.site')

@section('title', 'Carrinho de Compras')
@section('description', 'Revise seus itens antes de finalizar a compra')

@section('content')
<!-- Header -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Carrinho de Compras</h1>
        <nav class="text-sm text-gray-600">
            <a href="{{ route('site.store') }}" class="text-blue-600 hover:text-blue-800">← Continuar Comprando</a>
        </nav>
    </div>
</section>

<!-- Cart Content -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($itemsCount > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Itens do Carrinho ({{ $itemsCount }})</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($items as $item)
                        <div class="p-6">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $item['product']->image_url }}" 
                                     alt="{{ $item['product']->name }}" 
                                     class="w-24 h-24 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item['product']->name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">R$ {{ number_format($item['product']->price, 2, ',', '.') }} cada</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <form method="POST" action="{{ route('site.update-cart', $item['product']) }}" class="flex items-center space-x-2">
                                        @csrf
                                        <input type="number" 
                                               name="quantity" 
                                               value="{{ $item['quantity'] }}" 
                                               min="1" 
                                               max="{{ $item['product']->stock_quantity ?? 999 }}"
                                               onchange="this.form.submit()"
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </form>
                                    <div class="text-right min-w-[100px]">
                                        <div class="text-lg font-bold text-gray-900">
                                            R$ {{ number_format($item['product']->price * $item['quantity'], 2, ',', '.') }}
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('site.remove-from-cart', $item['product']) }}">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Deseja remover este item?')"
                                                class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                    <dl class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <dt>Subtotal</dt>
                            <dd>R$ {{ number_format($total, 2, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <dt>Frete</dt>
                            <dd>Calculado no checkout</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <dt>Total</dt>
                                <dd>R$ {{ number_format($total, 2, ',', '.') }}</dd>
                            </div>
                        </div>
                    </dl>
                    <a href="{{ route('site.checkout') }}" 
                       class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition text-center block">
                        Finalizar Compra
                    </a>
                    <a href="{{ route('site.store') }}" 
                       class="w-full mt-3 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition text-center block">
                        Continuar Comprando
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Carrinho vazio</h3>
            <p class="mt-1 text-sm text-gray-500">Adicione produtos ao carrinho para continuar.</p>
            <div class="mt-6">
                <a href="{{ route('site.store') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    Ir para Loja
                </a>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection


@extends('layouts.site')

@section('title', 'Checkout')
@section('description', 'Finalize sua compra')

@section('content')
<!-- Header -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Finalizar Compra</h1>
        <nav class="text-sm text-gray-600">
            <a href="{{ route('site.cart') }}" class="text-blue-600 hover:text-blue-800">← Voltar ao Carrinho</a>
        </nav>
    </div>
</section>

<!-- Checkout Content -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('site.process-checkout') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf
            
            <!-- Customer Information -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="customer_name" 
                                   id="customer_name" 
                                   value="{{ old('customer_name', Auth::user()->name ?? '') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_name') border-red-500 @enderror">
                            @error('customer_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    E-mail <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="customer_email" 
                                       id="customer_email" 
                                       value="{{ old('customer_email', Auth::user()->email ?? '') }}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_email') border-red-500 @enderror">
                                @error('customer_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefone <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="customer_phone" 
                                       id="customer_phone" 
                                       value="{{ old('customer_phone') }}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_phone') border-red-500 @enderror">
                                @error('customer_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Endereço de Entrega</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Endereço Completo <span class="text-red-500">*</span>
                            </label>
                            <textarea name="billing_address" 
                                      id="billing_address" 
                                      rows="4"
                                      required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('billing_address') border-red-500 @enderror">{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Método de Pagamento</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="asaas" checked class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">Pagamento via Asaas</div>
                                <div class="text-sm text-gray-500">Boleto, PIX ou Cartão de Crédito</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                    <div class="space-y-3 mb-6">
                        @foreach($items as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $item['product']->name }}</div>
                                <div class="text-gray-500">Qtd: {{ $item['quantity'] }}</div>
                            </div>
                            <div class="font-semibold text-gray-900">
                                R$ {{ number_format($item['product']->price * $item['quantity'], 2, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <dl class="space-y-3 mb-6 border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-gray-600">
                            <dt>Subtotal</dt>
                            <dd>R$ {{ number_format($total, 2, ',', '.') }}</dd>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <dt>Frete</dt>
                            <dd>Calculado</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <dt>Total</dt>
                                <dd>R$ {{ number_format($total, 2, ',', '.') }}</dd>
                            </div>
                        </div>
                    </dl>
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Finalizar Pedido
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection


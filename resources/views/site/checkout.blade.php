@extends('layouts.site')

@section('title', 'Finalizar Pedido')
@section('description', 'Ambiente seguro para finalização de sua compra')

@section('content')
<!-- Top Bar de Segurança -->
<div class="bg-blue-600 py-3 text-white text-center text-xs font-bold uppercase tracking-widest hidden md:block">
    🔐 Ambiente 100% Seguro • Processado por Asaas
</div>

<section class="py-12 md:py-20 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header minimalista -->
        <div class="mb-12">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Checkout</h1>
            <p class="text-gray-500 font-medium">Falta pouco para você garantir seus itens!</p>
        </div>

        <form method="POST" action="{{ route('site.process-checkout') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            @csrf
            
            <!-- Information Flow (Left) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Etapa 1: Dados Pessoais -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <h2 class="text-xl font-bold text-gray-900">Dados Pessoais</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nome Completo</label>
                            <input type="text" name="customer_name" 
                                   value="{{ old('customer_name', Auth::user()->name ?? '') }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium @error('customer_name') border-red-500 @enderror"
                                   placeholder="Como está no seu documento">
                            @error('customer_name') <p class="mt-2 text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">E-mail</label>
                            <input type="email" name="customer_email" 
                                   value="{{ old('customer_email', Auth::user()->email ?? '') }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium"
                                   placeholder="ex@exemplo.com">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">WhatsApp / Telefone</label>
                            <input type="text" name="customer_phone" id="customer_phone"
                                   value="{{ old('customer_phone', Auth::user()->phone ?? (Auth::user()->athlete->phone ?? '')) }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium"
                                   placeholder="(00) 00000-0000">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">CPF / CNPJ</label>
                            <input type="text" name="customer_document" id="customer_document"
                                   value="{{ old('customer_document', Auth::user()->athlete->document ?? '') }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium"
                                   placeholder="000.000.000-00">
                            <p class="mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">Obrigatório para emissão da fatura</p>
                        </div>
                    </div>
                </div>

                <!-- Etapa 2: Entrega -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <h2 class="text-xl font-bold text-gray-900">Onde entregamos?</h2>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Endereço Completo</label>
                        <textarea name="billing_address" id="billing_address" rows="3" required
                                  class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium"
                                  placeholder="Rua, Número, Complemento, Bairro, Cidade e CEP">@php
                                      $user = Auth::user();
                                      // Tenta buscar o atleta vinculado de forma mais explícita
                                      $athlete = $user->athlete ?? \App\Models\Athlete::where('user_id', $user->id)->first();
                                      $savedAddress = $athlete->address ?? '';
                                      
                                      // Se o endereço salvo for igual ao email (erro de cadastro antigo), ignoramos
                                      if (filter_var($savedAddress, FILTER_VALIDATE_EMAIL)) {
                                          $savedAddress = '';
                                      }
                                      echo old('billing_address', $savedAddress);
                                  @endphp</textarea>
                    </div>
                </div>

                <!-- Etapa 3: Pagamento -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <h2 class="text-xl font-bold text-gray-900">Como você prefere pagar?</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="payment_method" value="asaas" checked class="peer sr-only">
                            <div class="p-6 bg-gray-50 border-2 border-transparent rounded-3xl peer-checked:border-blue-600 peer-checked:bg-blue-50 transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-600 flex items-center justify-center">
                                        <div class="w-2.5 h-2.5 bg-blue-600 rounded-full scale-0 peer-checked:scale-100 transition"></div>
                                    </div>
                                </div>
                                <h4 class="font-bold text-gray-900">Cartão / PIX / Boleto</h4>
                                <p class="text-xs text-gray-500 mt-1">Processamento imediato via Asaas.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Resumo (Right) -->
            <div class="lg:col-span-4 lg:sticky lg:top-12 space-y-6">
                <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl text-white">
                    <h2 class="text-xl font-bold mb-8">Seu Pedido</h2>
                    
                    <div class="space-y-6 mb-10 overflow-y-auto max-h-[300px] custom-scrollbar">
                        @foreach($items as $item)
                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 bg-white/10 rounded-xl flex-shrink-0 flex items-center justify-center font-bold text-xs">
                                {{ $item['quantity'] }}x
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-white line-clamp-1">{{ $item['product']->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">R$ {{ number_format($item['product']->price, 2, ',', '.') }} un.</p>
                            </div>
                            <p class="text-sm font-black">R$ {{ number_format($item['product']->price * $item['quantity'], 2, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="space-y-4 pt-8 border-t border-white/10">
                        <div class="flex justify-between items-center text-sm font-medium text-gray-400">
                            <span>Subtotal</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm font-medium text-gray-400">
                            <span>Frete</span>
                            <span class="text-green-400">Calculado no Próximo Passo</span>
                        </div>
                        <div class="flex justify-between items-center pt-4">
                            <span class="text-2xl font-black">Total</span>
                            <span class="text-2xl font-black text-blue-400">R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" id="submit-button" class="w-full mt-10 py-5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl transition-all transform hover:-translate-y-1 shadow-lg flex items-center justify-center gap-3">
                        <span id="button-text">Pagar Agora</span>
                        <svg id="button-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7M5 12h16"></path></svg>
                        <div id="button-spinner" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>

                    <div class="mt-8 flex items-center justify-center gap-4 opacity-30">
                        <div class="h-1 flex-1 bg-white/20"></div>
                        <span class="text-[10px] font-black uppercase">Segurança Garantida</span>
                        <div class="h-1 flex-1 bg-white/20"></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-500 leading-tight">Suas informações de pagamento são encriptadas de ponta a ponta.</p>
                </div>
            </div>

        </form>
    </div>
</section>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
</style>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const buttonIcon = document.getElementById('button-icon');
        const buttonSpinner = document.getElementById('button-spinner');

        if (form) {
            form.addEventListener('submit', function() {
                // Previne cliques múltiplos
                submitButton.disabled = true;
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                
                // Altera UI para estado de carregamento
                buttonText.innerText = 'Processando...';
                buttonIcon.classList.add('hidden');
                buttonSpinner.classList.remove('hidden');
            });
        }
    });
</script>
@endpush
@endsection


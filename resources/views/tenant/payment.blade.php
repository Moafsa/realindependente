<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full p-6">
        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 text-blue-800 rounded-2xl text-sm font-bold border border-blue-100 animate__animated animate__fadeInDown">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('info') }}
                </div>
            </div>
        @endif
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-blue-600 px-6 py-8 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Quase lá!</h1>
                <p class="text-blue-100 mt-2">Só falta o pagamento para ativar seu clube.</p>
            </div>

            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo da Assinatura</h2>
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Clube:</span>
                            <span class="font-bold text-gray-900">{{ $tenant->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plano:</span>
                            <span class="font-bold text-gray-900">{{ $tenant->plan->name }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <span class="text-gray-900 font-bold text-lg">Total:</span>
                            <span class="text-blue-600 font-bold text-lg">R$ {{ number_format($subscription['value'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-gray-600 text-center text-sm mb-6">
                        Ao clicar no botão abaixo, você será redirecionado para o ambiente seguro do Asaas para realizar o pagamento via PIX, Cartão ou Boleto.
                    </p>

                    @if($payment_url)
                        <a href="{{ $payment_url }}" target="_blank" class="w-full bg-blue-600 text-white py-4 px-6 rounded-xl font-bold flex items-center justify-center hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            Pagar Agora com Asaas
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    @else
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        A URL de pagamento não pôde ser gerada automaticamente. Por favor, aguarde o e-mail de cobrança ou entre em contato com o suporte.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 pt-8 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">
                            Assim que o pagamento for confirmado, você receberá um e-mail com os dados de acesso ao seu painel administrativo.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('marketing.home') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                ← Voltar para o site
            </a>
        </div>
    </div>
</body>
</html>

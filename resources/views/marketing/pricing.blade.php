@extends('layouts.app')

@section('title', 'Preços - Real Independent')

@section('content')
<!-- Header -->
<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
            <div class="flex items-center">
                <a href="{{ route('marketing.home') }}" class="text-2xl font-bold text-blue-600">Real Independent</a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('marketing.home') }}#features" class="text-gray-500 hover:text-gray-900">Funcionalidades</a>
                <a href="{{ route('marketing.home') }}#pricing" class="text-gray-500 hover:text-gray-900">Preços</a>
                <a href="{{ route('marketing.contact') }}" class="text-gray-500 hover:text-gray-900">Contato</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-900">Login</a>
                <a href="{{ route('tenant.register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Começar Grátis</a>
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">Planos que cabem no seu bolso</h1>
        <p class="text-xl text-blue-100">Escolha o plano ideal para o tamanho do seu clube</p>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="bg-white border-2 rounded-lg p-8 {{ $plan->name === 'Profissional' ? 'border-blue-500 ring-2 ring-blue-500 transform scale-105' : 'border-gray-200' }} hover:shadow-lg transition">
                @if($plan->name === 'Profissional')
                <div class="bg-blue-500 text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                    Mais Popular
                </div>
                @endif
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
                
                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</span>
                    <span class="text-gray-600">/mês</span>
                    @if($plan->price_yearly)
                    <p class="text-sm text-gray-500 mt-2">
                        ou R$ {{ number_format($plan->price_yearly, 2, ',', '.') }}/ano
                        <span class="text-green-600 font-semibold">(Economize {{ $plan->yearly_discount }}%)</span>
                    </p>
                    @endif
                </div>
                
                <ul class="space-y-3 mb-8">
                    @if(is_array($plan->features))
                        @foreach($plan->features as $feature)
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                        @endforeach
                    @endif
                    
                    @if($plan->max_athletes)
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Até {{ $plan->max_athletes }} atletas</span>
                    </li>
                    @endif
                    
                    @if($plan->max_branches)
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Até {{ $plan->max_branches }} filiais</span>
                    </li>
                    @endif
                    
                    @if($plan->ai_features)
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Recursos de IA incluídos</span>
                    </li>
                    @endif
                </ul>
                
                <a href="{{ route('tenant.register') }}?plan={{ $plan->id }}" 
                   class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition text-center block">
                    Começar Grátis
                </a>
                
                <p class="text-center text-sm text-gray-500 mt-4">14 dias grátis, sem cartão de crédito</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Perguntas Frequentes</h2>
        
        <div class="space-y-6">
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Posso mudar de plano depois?</h3>
                <p class="text-gray-600">Sim! Você pode fazer upgrade ou downgrade do seu plano a qualquer momento. As alterações serão aplicadas no próximo ciclo de cobrança.</p>
            </div>
            
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">O que acontece no período de teste?</h3>
                <p class="text-gray-600">Durante os 14 dias grátis, você tem acesso completo a todas as funcionalidades do plano escolhido. Não é necessário cartão de crédito para começar.</p>
            </div>
            
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Como funciona o pagamento?</h3>
                <p class="text-gray-600">Aceitamos pagamento via PIX, boleto e cartão de crédito. O pagamento é processado mensalmente ou anualmente, conforme sua escolha.</p>
            </div>
            
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Posso cancelar a qualquer momento?</h3>
                <p class="text-gray-600">Sim, você pode cancelar sua assinatura a qualquer momento sem multas ou taxas. Seu acesso permanecerá ativo até o final do período pago.</p>
            </div>
            
            <div class="pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Preciso de suporte técnico?</h3>
                <p class="text-gray-600">Oferecemos suporte por e-mail e chat para todos os planos. Planos superiores têm prioridade no atendimento.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Pronto para começar?</h2>
        <p class="text-xl mb-8 text-blue-100">Teste grátis por 14 dias. Sem cartão de crédito.</p>
        <a href="{{ route('tenant.register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
            Começar Agora
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">Real Independent</h3>
                <p class="text-gray-400">A plataforma completa para gestão de clubes de futebol.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Produto</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('marketing.home') }}#features" class="hover:text-white">Funcionalidades</a></li>
                    <li><a href="{{ route('marketing.home') }}#pricing" class="hover:text-white">Preços</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Suporte</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('marketing.contact') }}" class="hover:text-white">Contato</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                    <li><a href="#" class="hover:text-white">Privacidade</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Real Independent. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
@endsection


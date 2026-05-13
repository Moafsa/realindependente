@extends('layouts.app')

@section('title', 'Preços - Real Independent')

@section('content')
<!-- Header -->
<header id="main-header" class="fixed w-full top-0 z-50 transition-all duration-300 bg-white/0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6 transition-all duration-300" id="header-container">
            <div class="flex items-center">
                <a href="{{ route('marketing.home') }}" class="text-2xl font-bold text-white transition-colors duration-300" id="header-logo">Real Independent</a>
            </div>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('marketing.home') }}#features" class="text-white/80 hover:text-white transition-colors nav-link">Funcionalidades</a>
                <a href="{{ route('marketing.home') }}#pricing" class="text-white/80 hover:text-white transition-colors nav-link">Preços</a>
                <a href="{{ route('marketing.contact') }}" class="text-white/80 hover:text-white transition-colors nav-link">Contato</a>
            </nav>
            
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-white/80 hover:text-white transition-colors nav-link">Login</a>
                <a href="{{ route('tenant.register') }}" class="bg-white text-blue-600 px-4 py-2 rounded-md hover:bg-gray-100 transition-all duration-300 shadow-lg" id="header-cta">Começar Grátis</a>
            </div>

            <!-- Hamburger Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-white focus:outline-none transition-colors duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-2xl absolute w-full top-full left-0 border-t border-gray-100">
        <div class="px-4 pt-2 pb-6 space-y-2">
            <a href="{{ route('marketing.home') }}#features" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Funcionalidades</a>
            <a href="{{ route('marketing.home') }}#pricing" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Preços</a>
            <a href="{{ route('marketing.contact') }}" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Contato</a>
            <hr class="border-gray-100 my-2">
            <a href="{{ route('login') }}" class="block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Login</a>
            <a href="{{ route('tenant.register') }}" class="block px-4 py-3 bg-blue-600 text-white font-bold text-center rounded-xl shadow-lg shadow-blue-200">Começar Grátis</a>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const header = document.getElementById('main-header');
        const headerContainer = document.getElementById('header-container');
        const logo = document.getElementById('header-logo');
        const links = document.querySelectorAll('.nav-link');
        const cta = document.getElementById('header-cta');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');
        
        // Scroll Effect
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.remove('bg-white/0');
                header.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                headerContainer.classList.replace('py-6', 'py-4');
                logo.classList.replace('text-white', 'text-blue-600');
                mobileMenuButton.classList.replace('text-white', 'text-blue-600');
                links.forEach(link => {
                    link.classList.remove('text-white/80');
                    link.classList.add('text-gray-600');
                });
                cta.classList.remove('bg-white', 'text-blue-600');
                cta.classList.add('bg-blue-600', 'text-white');
            } else {
                header.classList.add('bg-white/0');
                header.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                headerContainer.classList.replace('py-4', 'py-6');
                logo.classList.replace('text-blue-600', 'text-white');
                mobileMenuButton.classList.replace('text-blue-600', 'text-white');
                links.forEach(link => {
                    link.classList.add('text-white/80');
                    link.classList.remove('text-gray-600');
                });
                cta.classList.add('bg-white', 'text-blue-600');
                cta.classList.remove('bg-blue-600', 'text-white');
            }
        });

        // Mobile Menu Toggle
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });

        // Close menu on link click
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    });
</script>

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


@extends('layouts.app')

@section('title', 'Preços - Nexts')

@section('content')
<!-- Header -->
<header id="main-header" class="fixed w-full top-0 z-50 transition-all duration-300 bg-white/0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6 transition-all duration-300" id="header-container">
            <div class="flex items-center">
                <a href="{{ route('marketing.home') }}" class="text-2xl font-bold text-white transition-colors duration-300" id="header-logo">Nexts</a>
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
<section class="py-20 bg-gray-50" x-data="{ frequency: 'monthly' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Frequency Switcher -->
        <div class="flex justify-center mb-16">
            <div class="bg-white p-1.5 rounded-2xl shadow-xl border border-gray-100 flex gap-1 inline-flex">
                <button @click="frequency = 'monthly'" :class="frequency === 'monthly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-xl font-bold transition-all text-sm uppercase tracking-wider">Mensal</button>
                <button @click="frequency = 'quarterly'" :class="frequency === 'quarterly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-xl font-bold transition-all text-sm uppercase tracking-wider">Trimestral</button>
                <button @click="frequency = 'semiannual'" :class="frequency === 'semiannual' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-xl font-bold transition-all text-sm uppercase tracking-wider">Semestral</button>
                <button @click="frequency = 'yearly'" :class="frequency === 'yearly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-50'" class="px-8 py-3 rounded-xl font-bold transition-all text-sm uppercase tracking-wider">Anual</button>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="bg-white border-2 rounded-3xl p-8 {{ $plan->name === 'Profissional' ? 'border-blue-500 ring-4 ring-blue-500/10 transform scale-105 shadow-2xl relative z-10' : 'border-gray-100 shadow-xl' }} hover:shadow-2xl transition-all duration-300">
                @if($plan->name === 'Profissional')
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-xs font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                    Mais Popular
                </div>
                @endif
                
                <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $plan->name }}</h3>
                <p class="text-gray-500 mb-8 text-sm leading-relaxed">{{ $plan->description }}</p>
                
                <div class="mb-8">
                    <!-- Monthly Price -->
                    <template x-if="frequency === 'monthly'">
                        <div>
                            <span class="text-5xl font-black text-gray-900">R$ {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                            <span class="text-gray-400 font-bold">/mês</span>
                        </div>
                    </template>

                    <!-- Quarterly Price -->
                    <template x-if="frequency === 'quarterly'">
                        <div>
                            @php
                                $baseQ = $plan->price_monthly * 3;
                                $priceQ = $plan->price_quarterly ?: ($baseQ * (1 - ($plan->discount_quarterly / 100)));
                            @endphp
                            <span class="text-5xl font-black text-gray-900">R$ {{ number_format($priceQ, 0, ',', '.') }}</span>
                            <span class="text-gray-400 font-bold">/trim.</span>
                            @if($plan->discount_quarterly > 0)
                                <p class="text-xs font-bold text-green-600 mt-2 uppercase tracking-tighter">Economize {{ $plan->discount_quarterly }}%</p>
                            @endif
                        </div>
                    </template>

                    <!-- Semiannual Price -->
                    <template x-if="frequency === 'semiannual'">
                        <div>
                            @php
                                $baseS = $plan->price_monthly * 6;
                                $priceS = $plan->price_semiannual ?: ($baseS * (1 - ($plan->discount_semiannual / 100)));
                            @endphp
                            <span class="text-5xl font-black text-gray-900">R$ {{ number_format($priceS, 0, ',', '.') }}</span>
                            <span class="text-gray-400 font-bold">/sem.</span>
                            @if($plan->discount_semiannual > 0)
                                <p class="text-xs font-bold text-green-600 mt-2 uppercase tracking-tighter">Economize {{ $plan->discount_semiannual }}%</p>
                            @endif
                        </div>
                    </template>

                    <!-- Yearly Price -->
                    <template x-if="frequency === 'yearly'">
                        <div>
                            @php
                                $baseY = $plan->price_monthly * 12;
                                $priceY = $plan->price_yearly ?: ($baseY * (1 - ($plan->discount_yearly / 100)));
                            @endphp
                            <span class="text-5xl font-black text-gray-900">R$ {{ number_format($priceY, 0, ',', '.') }}</span>
                            <span class="text-gray-400 font-bold">/ano</span>
                            @if($plan->discount_yearly > 0)
                                <p class="text-xs font-bold text-green-600 mt-2 uppercase tracking-tighter">Economize {{ $plan->discount_yearly }}%</p>
                            @endif
                        </div>
                    </template>
                </div>
                
                <ul class="space-y-4 mb-10">
                    @if(is_array($plan->features))
                        @foreach($plan->features as $feature)
                        <li class="flex items-start">
                            <div class="mt-1 bg-green-100 rounded-full p-1 mr-3 flex-shrink-0">
                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-600 text-sm font-medium">{{ $feature }}</span>
                        </li>
                        @endforeach
                    @endif
                    
                    @if($plan->max_athletes)
                    <li class="flex items-start">
                        <div class="mt-1 bg-blue-100 rounded-full p-1 mr-3 flex-shrink-0">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <span class="text-gray-600 text-sm font-medium">Até {{ $plan->max_athletes }} atletas</span>
                    </li>
                    @endif

                    @if($plan->max_branches)
                    <li class="flex items-start">
                        <div class="mt-1 bg-blue-100 rounded-full p-1 mr-3 flex-shrink-0">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-gray-600 text-sm font-medium">Até {{ $plan->max_branches }} unidades/filiais</span>
                    </li>
                    @endif
                    
                    @if($plan->admin_fee_percentage > 0)
                    <li class="flex items-start">
                        <div class="mt-1 bg-orange-100 rounded-full p-1 mr-3 flex-shrink-0">
                            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                        </div>
                        <span class="text-gray-600 text-sm font-medium">Taxa de transação: {{ number_format($plan->admin_fee_percentage, 1, ',', '.') }}%</span>
                    </li>
                    @endif

                    @if($plan->ai_features)
                    <li class="flex items-start">
                        <div class="mt-1 bg-purple-100 rounded-full p-1 mr-3 flex-shrink-0">
                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                        </div>
                        <span class="text-gray-600 text-sm font-medium">Inteligência Artificial Inclusa</span>
                    </li>
                    @endif
                </ul>
                
                <a :href="'{{ route('tenant.register') }}?plan={{ $plan->id }}&frequency=' + frequency" 
                   class="w-full bg-gray-900 text-white py-4 px-6 rounded-2xl font-bold hover:bg-blue-600 transition-all duration-300 text-center block shadow-lg shadow-gray-200">
                    Começar Agora
                </a>
                
                <p class="text-center text-[10px] uppercase tracking-widest font-black text-gray-400 mt-6">{{ $plan->trial_days ?: '0' }} dias grátis · sem cartão</p>
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
                <h3 class="text-xl font-bold mb-4">Nexts</h3>
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
            <p>&copy; {{ date('Y') }} Nexts. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
@endsection


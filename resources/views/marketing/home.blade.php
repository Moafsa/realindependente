@extends('layouts.app')

@section('title', 'Nexts - Gestão Completa para Clubes de Futebol')

@section('content')
<!-- Header -->
<header id="main-header" class="fixed w-full top-0 z-50 transition-all duration-300 bg-white/0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6 transition-all duration-300" id="header-container">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-white transition-colors duration-300" id="header-logo">Nexts</h1>
                </div>
            </div>
            
            <!-- Desktop Nav -->
            <nav class="hidden md:flex space-x-8">
                <a href="#features" class="text-white/80 hover:text-white transition-colors nav-link">Funcionalidades</a>
                <a href="#pricing" class="text-white/80 hover:text-white transition-colors nav-link">Preços</a>
                <a href="#contact" class="text-white/80 hover:text-white transition-colors nav-link">Contato</a>
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
            <a href="#features" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Funcionalidades</a>
            <a href="#pricing" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Preços</a>
            <a href="#contact" class="mobile-nav-link block px-4 py-3 text-gray-600 font-medium hover:bg-blue-50 hover:text-blue-600 rounded-xl transition">Contato</a>
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
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                A gestão completa para seu clube de futebol
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                Gerencie atletas, equipes, finanças e muito mais em uma única plataforma
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tenant.register') }}" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Começar Grátis
                </a>
                <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                    Ver Funcionalidades
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Tudo que você precisa para gerenciar seu clube
            </h2>
            <p class="text-xl text-gray-600">
                Uma plataforma completa com recursos de IA e integração financeira
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Gestão de Atletas</h3>
                <p class="text-gray-600">Cadastro completo, acompanhamento de performance e histórico detalhado</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Portal do Atleta</h3>
                <p class="text-gray-600">Área exclusiva para atletas acompanharem seu desenvolvimento</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Site Público</h3>
                <p class="text-gray-600">Site automático com loja virtual integrada para cada clube</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Inteligência Artificial</h3>
                <p class="text-gray-600">Planos de treino e nutrição personalizados para cada atleta</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-white" x-data="{ frequency: 'monthly' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-4 tracking-tight">
                Planos que cabem no seu bolso
            </h2>
            <p class="text-xl text-gray-500 font-medium">
                Escolha o plano ideal para o tamanho do seu clube
            </p>
        </div>

        <!-- Frequency Switcher -->
        <div class="flex justify-center mb-16">
            <div class="bg-gray-50 p-1.5 rounded-2xl border border-gray-100 flex gap-1 inline-flex">
                <button @click="frequency = 'monthly'" :class="frequency === 'monthly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-100'" class="px-6 py-2.5 rounded-xl font-bold transition-all text-[10px] uppercase tracking-widest">Mensal</button>
                <button @click="frequency = 'quarterly'" :class="frequency === 'quarterly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-100'" class="px-6 py-2.5 rounded-xl font-bold transition-all text-[10px] uppercase tracking-widest">Trimestral</button>
                <button @click="frequency = 'semiannual'" :class="frequency === 'semiannual' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-100'" class="px-6 py-2.5 rounded-xl font-bold transition-all text-[10px] uppercase tracking-widest">Semestral</button>
                <button @click="frequency = 'yearly'" :class="frequency === 'yearly' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-500 hover:bg-gray-100'" class="px-6 py-2.5 rounded-xl font-bold transition-all text-[10px] uppercase tracking-widest">Anual</button>
            </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="bg-white border border-gray-100 rounded-[2.5rem] p-10 {{ $plan->name === 'Profissional' ? 'ring-4 ring-blue-500/10 border-blue-100 shadow-2xl relative z-10' : 'shadow-xl' }} transition-all duration-500 hover:-translate-y-2">
                @if($plan->name === 'Profissional')
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                    Mais Popular
                </div>
                @endif
                
                <h3 class="text-2xl font-black text-gray-900 mb-2">{{ $plan->name }}</h3>
                <p class="text-gray-500 mb-8 text-xs leading-relaxed font-medium">{{ $plan->description }}</p>
                
                <div class="mb-10">
                    <!-- Prices -->
                    <div x-show="frequency === 'monthly'" class="animate__animated animate__fadeIn">
                        <span class="text-5xl font-black text-gray-900 tracking-tighter">R$ {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                        <span class="text-gray-400 font-bold text-xs">/mês</span>
                    </div>

                    <div x-show="frequency === 'quarterly'" class="animate__animated animate__fadeIn">
                        @php 
                            $priceQ = $plan->getCalculatedPrice('quarterly');
                            $origQ = $plan->getOriginalPrice('quarterly');
                        @endphp
                        @if($origQ > $priceQ)
                            <div class="flex flex-col mb-1">
                                <span class="text-lg font-bold text-rose-500 line-through opacity-60">R$ {{ number_format($origQ, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <span class="text-5xl font-black text-gray-900 tracking-tighter">R$ {{ number_format($priceQ, 0, ',', '.') }}</span>
                        <span class="text-gray-400 font-bold text-xs">/trim.</span>
                        @if($plan->discount_quarterly > 0)
                            <p class="text-[10px] font-black text-green-500 mt-2 uppercase tracking-widest">Economize {{ $plan->discount_quarterly }}%</p>
                        @endif
                    </div>

                    <div x-show="frequency === 'semiannual'" class="animate__animated animate__fadeIn">
                        @php 
                            $priceS = $plan->getCalculatedPrice('semiannual');
                            $origS = $plan->getOriginalPrice('semiannual');
                        @endphp
                        @if($origS > $priceS)
                            <div class="flex flex-col mb-1">
                                <span class="text-lg font-bold text-rose-500 line-through opacity-60">R$ {{ number_format($origS, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <span class="text-5xl font-black text-gray-900 tracking-tighter">R$ {{ number_format($priceS, 0, ',', '.') }}</span>
                        <span class="text-gray-400 font-bold text-xs">/sem.</span>
                        @if($plan->discount_semiannual > 0)
                            <p class="text-[10px] font-black text-green-500 mt-2 uppercase tracking-widest">Economize {{ $plan->discount_semiannual }}%</p>
                        @endif
                    </div>

                    <div x-show="frequency === 'yearly'" class="animate__animated animate__fadeIn">
                        @php 
                            $priceY = $plan->getCalculatedPrice('yearly');
                            $origY = $plan->getOriginalPrice('yearly');
                        @endphp
                        @if($origY > $priceY)
                            <div class="flex flex-col mb-1">
                                <span class="text-lg font-bold text-rose-500 line-through opacity-60">R$ {{ number_format($origY, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <span class="text-5xl font-black text-gray-900 tracking-tighter">R$ {{ number_format($priceY, 0, ',', '.') }}</span>
                        <span class="text-gray-400 font-bold text-xs">/ano</span>
                        @if($plan->discount_yearly > 0)
                            <p class="text-[10px] font-black text-green-500 mt-2 uppercase tracking-widest">Economize {{ $plan->discount_yearly }}%</p>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-4 mb-10">
                    <div class="p-4 bg-gray-50 rounded-2xl">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Limite de Atletas</span>
                            <span class="text-xs font-black text-gray-900">{{ $plan->max_athletes ?: 'Ilimitado' }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Limite de Unidades</span>
                            <span class="text-xs font-black text-gray-900">{{ $plan->max_branches ?: '1' }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Taxa Admin (Mensalidade)</span>
                            <span class="text-xs font-black text-blue-600">{{ $plan->admin_fee_percentage ?: 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Taxa de Serviço (Loja)</span>
                            <span class="text-xs font-black text-emerald-600">{{ $plan->ecommerce_tax_rate ?: 0 }}%</span>
                        </div>
                    </div>

                    <ul class="space-y-3">
                        @foreach($plan->features as $feature)
                        <li class="flex items-start">
                            <div class="mt-1 bg-blue-500/10 rounded-full p-1 mr-3 flex-shrink-0 text-blue-600">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-gray-600 text-[11px] font-bold leading-tight">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <a :href="'{{ route('tenant.register') }}?plan={{ $plan->id }}&frequency=' + frequency" 
                   class="w-full bg-gray-900 text-white py-4 px-6 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all duration-300 text-center block shadow-lg shadow-gray-200 group-hover:shadow-blue-500/20">
                    Começar Grátis
                </a>
                
                <p class="text-center text-[9px] uppercase tracking-widest font-black text-gray-400 mt-6">{{ $plan->trial_days ?: '0' }} dias grátis · sem cartão</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Pronto para transformar seu clube?
        </h2>
        <p class="text-xl mb-8 text-blue-100">
            Comece hoje mesmo. Teste grátis disponível em todos os planos.
        </p>
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
                    <li><a href="#features" class="hover:text-white">Funcionalidades</a></li>
                    <li><a href="#pricing" class="hover:text-white">Preços</a></li>
                    <li><a href="#" class="hover:text-white">API</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Suporte</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Central de Ajuda</a></li>
                    <li><a href="#" class="hover:text-white">Documentação</a></li>
                    <li><a href="#" class="hover:text-white">Contato</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                    <li><a href="#" class="hover:text-white">Privacidade</a></li>
                    <li><a href="#" class="hover:text-white">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Nexts. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
@endsection

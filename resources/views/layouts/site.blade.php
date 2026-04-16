<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seoTitle = $__env->yieldContent('title');
        $seoDescription = $__env->yieldContent('description');
        $seoImage = $__env->yieldContent('og-image');
        $seoType = $__env->yieldContent('og-type') ?: 'website';
        $seoUrl = $__env->yieldContent('og-url');
    @endphp
    @include('components.seo-meta', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'image' => $seoImage,
        'type' => $seoType,
        'url' => $seoUrl,
    ])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('site.home') }}" class="text-2xl font-bold text-blue-600">
                            {{ $settings['site_name'] ?? 'Real Independent' }}
                        </a>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('site.home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.home') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Início
                        </a>
                        <a href="{{ route('site.about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.about') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Sobre
                        </a>
                        <a href="{{ route('site.teams') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.teams') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Equipes
                        </a>
                        <a href="{{ route('site.athletes') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.athletes') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Atletas
                        </a>
                        <a href="{{ route('site.store') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.store') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Loja
                        </a>
                        <a href="{{ route('site.contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.contact') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Contato
                        </a>
                    </div>
                </div>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Cart -->
                    <a href="{{ route('site.cart') }}" class="relative p-2 text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                        @if(session('cart'))
                        <span class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                            {{ count(session('cart')) }}
                        </span>
                        @endif
                    </a>
                    
                    <!-- Auth Links -->
                    @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                            Sair
                        </button>
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Cadastrar
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="sm:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('site.home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.home') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Início
                </a>
                <a href="{{ route('site.about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.about') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Sobre
                </a>
                <a href="{{ route('site.teams') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.teams') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Equipes
                </a>
                <a href="{{ route('site.athletes') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.athletes') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Atletas
                </a>
                <a href="{{ route('site.store') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.store') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Loja
                </a>
                <a href="{{ route('site.contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.contact') ? 'border-blue-500 text-blue-700 bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Contato
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">{{ $settings['site_name'] ?? 'Real Independent' }}</h3>
                    <p class="text-gray-300 mb-4">{{ $settings['site_description'] ?? 'Clube de futebol dedicado ao desenvolvimento de atletas.' }}</p>
                    <div class="flex space-x-4">
                        @if($settings['facebook_url'] ?? false)
                        <a href="{{ $settings['facebook_url'] }}" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        @endif
                        @if($settings['instagram_url'] ?? false)
                        <a href="{{ $settings['instagram_url'] }}" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.348-1.051-2.348-2.348s1.051-2.348 2.348-2.348 2.348 1.051 2.348 2.348-1.051 2.348-2.348 2.348zm7.718 0c-1.297 0-2.348-1.051-2.348-2.348s1.051-2.348 2.348-2.348 2.348 1.051 2.348 2.348-1.051 2.348-2.348 2.348z"/>
                            </svg>
                        </a>
                        @endif
                        @if($settings['youtube_url'] ?? false)
                        <a href="{{ $settings['youtube_url'] }}" class="text-gray-400 hover:text-white">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Links Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('site.home') }}" class="text-gray-300 hover:text-white">Início</a></li>
                        <li><a href="{{ route('site.about') }}" class="text-gray-300 hover:text-white">Sobre</a></li>
                        <li><a href="{{ route('site.teams') }}" class="text-gray-300 hover:text-white">Equipes</a></li>
                        <li><a href="{{ route('site.athletes') }}" class="text-gray-300 hover:text-white">Atletas</a></li>
                        <li><a href="{{ route('site.store') }}" class="text-gray-300 hover:text-white">Loja</a></li>
                        <li><a href="{{ route('site.contact') }}" class="text-gray-300 hover:text-white">Contato</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contato</h3>
                    <div class="space-y-2 text-gray-300">
                        @if($settings['contact_phone'] ?? false)
                        <p>📞 {{ $settings['contact_phone'] }}</p>
                        @endif
                        @if($settings['contact_email'] ?? false)
                        <p>✉️ {{ $settings['contact_email'] }}</p>
                        @endif
                        @if($settings['contact_address'] ?? false)
                        <p>📍 {{ $settings['contact_address'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-700">
                <p class="text-center text-gray-400">
                    © {{ date('Y') }} {{ $settings['site_name'] ?? 'Real Independent' }}. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.querySelector('[data-menu-button]');
            
            if (menuButton) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
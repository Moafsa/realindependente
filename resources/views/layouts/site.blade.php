<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = $settings ?? [];
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

    <!-- Favicon -->
    @php
        $favicon = $settings['site_logo'] ?? null;
        if (!$favicon) {
            $favicon = \App\Models\SiteSetting::getCentral('site_logo');
        }
        $faviconUrl = $favicon ? \Illuminate\Support\Facades\Storage::url($favicon) . '?v=2' : asset('favicons/nexts_favicon.png');
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --primary-color: {{ $settings['color_primary'] ?? '#2563eb' }};
            --secondary-color: {{ $settings['color_secondary'] ?? '#16a34a' }};
            --footer-color: {{ $settings['color_footer'] ?? '#1f2937' }};
        }
        
        .bg-primary { background-color: var(--primary-color) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .hover\:bg-primary-dark:hover { filter: brightness(0.9); }
        
        .bg-secondary { background-color: var(--secondary-color) !important; }
        .text-secondary { color: var(--secondary-color) !important; }
        .border-secondary { border-color: var(--secondary-color) !important; }

        /* Hover variants */
        .hover\:text-primary:hover { color: var(--primary-color) !important; }
        .hover\:text-secondary:hover { color: var(--secondary-color) !important; }
        .hover\:bg-primary:hover { background-color: var(--primary-color) !important; }
        .hover\:bg-secondary:hover { background-color: var(--secondary-color) !important; }
        .hover\:border-primary:hover { border-color: var(--primary-color) !important; }
        .hover\:border-secondary:hover { border-color: var(--secondary-color) !important; }

        /* Sticky Header */
        .sticky-header {
            transition: all 0.3s ease-in-out;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .sticky-header.scrolled {
            background-color: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky-header" id="main-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="nav-container" class="flex justify-between h-24 transition-all duration-300">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('site.home') }}" class="text-5xl font-bold text-primary flex items-center h-full">
                            @if($settings['site_logo'] ?? false)
                                <img src="{{ Storage::url($settings['site_logo']) }}" alt="{{ $settings['site_name'] ?? 'Nexts' }}" class="h-16 w-auto transition-all duration-300" id="site-logo">
                            @else
                                {{ $settings['site_name'] ?? 'Nexts' }}
                            @endif
                        </a>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('site.home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.home') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Início
                        </a>
                        @if(($settings['enable_about_page'] ?? '1') == '1')
                        <a href="{{ route('site.about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.about') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Sobre
                        </a>
                        @endif
                        @if(($settings['enable_teams_page'] ?? '1') == '1')
                        <a href="{{ route('site.teams') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.teams') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Equipes
                        </a>
                        @endif
                        @if(($settings['enable_coaches_page'] ?? '1') == '1')
                        <a href="{{ route('site.coaches') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.coaches') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Treinadores
                        </a>
                        @endif
                        @if(($settings['enable_athletes_page'] ?? '1') == '1')
                        <a href="{{ route('site.athletes') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.athletes') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Atletas
                        </a>
                        @endif
                        @if(($settings['enable_store_page'] ?? '1') == '1')
                        <a href="{{ route('site.store') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.store') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Loja
                        </a>
                        @endif
                        @if(($settings['enable_plans_page'] ?? '1') == '1')
                        <a href="{{ route('site.plans') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.plans') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Planos
                        </a>
                        @endif
                        @if(($settings['enable_contact_page'] ?? '1') == '1')
                        <a href="{{ route('site.contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.contact') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Contato
                        </a>
                        @endif
                        @if(($settings['enable_blog_page'] ?? '1') == '1')
                        <a href="{{ route('site.blog') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.blog*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Blog
                        </a>
                        @endif
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" data-menu-button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Abrir menu principal</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Cart -->
                    <a href="{{ route('site.cart') }}" class="relative p-2 text-gray-500 hover:text-primary transition-colors group">
                        <svg class="h-6 w-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
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
                    <a href="{{ route('site.register') }}" class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-primary-dark">
                        Matricule-se
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('site.home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.home') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Início
                </a>
                @if(($settings['enable_about_page'] ?? '1') == '1')
                <a href="{{ route('site.about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.about') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Sobre
                </a>
                @endif
                @if(($settings['enable_teams_page'] ?? '1') == '1')
                <a href="{{ route('site.teams') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.teams') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Equipes
                </a>
                @endif
                @if(($settings['enable_coaches_page'] ?? '1') == '1')
                <a href="{{ route('site.coaches') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.coaches') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Treinadores
                </a>
                @endif
                @if(($settings['enable_athletes_page'] ?? '1') == '1')
                <a href="{{ route('site.athletes') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.athletes') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Atletas
                </a>
                @endif
                @if(($settings['enable_store_page'] ?? '1') == '1')
                <a href="{{ route('site.store') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.store') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Loja
                </a>
                @endif
                @if(($settings['enable_plans_page'] ?? '1') == '1')
                <a href="{{ route('site.plans') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.plans') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Planos
                </a>
                @endif
                @if(($settings['enable_contact_page'] ?? '1') == '1')
                <a href="{{ route('site.contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.contact') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Contato
                </a>
                @endif
                @if(($settings['enable_blog_page'] ?? '1') == '1')
                <a href="{{ route('site.blog') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.blog*') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Blog
                </a>
                @endif
            </div>
            
            <!-- Mobile Auth Links -->
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="space-y-1">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                Sair
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                            Entrar
                        </a>
                        <a href="{{ route('site.register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-primary hover:text-primary-dark hover:bg-gray-50 hover:border-gray-300">
                            Matricule-se
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success') || session('error') || $errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                <span class="font-medium">Sucesso!</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <span class="font-medium">Erro!</span> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white" style="background-color: var(--footer-color);">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">{{ $settings['site_name'] ?? 'Nexts' }}</h3>
                    <p class="text-gray-300 mb-4">{{ $settings['site_description'] ?? 'Clube de futebol dedicado ao desenvolvimento de atletas.' }}</p>
                    <div class="flex space-x-4">
                        @if($settings['facebook_url'] ?? false)
                        <a href="{{ $settings['facebook_url'] }}" target="_blank" class="text-gray-400 hover:text-white">
                            <i class="fa-brands fa-facebook text-2xl"></i>
                        </a>
                        @endif
                        @if($settings['instagram_url'] ?? false)
                        <a href="{{ $settings['instagram_url'] }}" target="_blank" class="text-gray-400 hover:text-white">
                            <i class="fa-brands fa-instagram text-2xl"></i>
                        </a>
                        @endif
                        @if($settings['youtube_url'] ?? false)
                        <a href="{{ $settings['youtube_url'] }}" target="_blank" class="text-gray-400 hover:text-white">
                            <i class="fa-brands fa-youtube text-2xl"></i>
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
                        @if(($settings['enable_plans_page'] ?? '1') == '1')
                        <li><a href="{{ route('site.plans') }}" class="text-gray-300 hover:text-white">Planos</a></li>
                        @endif
                        <li><a href="{{ route('site.contact') }}" class="text-gray-300 hover:text-white">Contato</a></li>
                        <li><a href="{{ route('site.blog') }}" class="text-gray-300 hover:text-white">Blog</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contato</h3>
                    <div class="space-y-2 text-gray-300">
                        @if($settings['contact_phone'] ?? false)
                        <p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['contact_phone']) }}" target="_blank" class="hover:text-white transition-colors">
                                📞 {{ $settings['contact_phone'] }}
                            </a>
                        </p>
                        @endif
                        @if($settings['contact_email'] ?? false)
                        <p>
                            <a href="mailto:{{ $settings['contact_email'] }}" class="hover:text-white transition-colors">
                                ✉️ {{ $settings['contact_email'] }}
                            </a>
                        </p>
                        @endif
                        @if($settings['contact_address'] ?? false)
                        <p>📍 {{ $settings['contact_address'] }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-gray-700">
                <p class="text-center text-gray-400">
                    © {{ date('Y') }} {{ $settings['site_name'] ?? 'Nexts' }}. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </footer>

    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.querySelector('[data-menu-button]');
            
            if (menuButton) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Sticky Header Scroll Effect
            const nav = document.getElementById('main-nav');
            const navContainer = document.getElementById('nav-container');
            const logo = document.getElementById('site-logo');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    nav.classList.add('scrolled');
                    if (navContainer) {
                        navContainer.classList.remove('h-24');
                        navContainer.classList.add('h-16');
                    }
                    if (logo) {
                        logo.classList.remove('h-16');
                        logo.classList.add('h-10');
                    }
                } else {
                    nav.classList.remove('scrolled');
                    if (navContainer) {
                        navContainer.classList.remove('h-16');
                        navContainer.classList.add('h-24');
                    }
                    if (logo) {
                        logo.classList.remove('h-10');
                        logo.classList.add('h-16');
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
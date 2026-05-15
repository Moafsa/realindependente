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
        $faviconUrl = $favicon ? \Illuminate\Support\Facades\Storage::url($favicon) : asset('favicons/nexts_favicon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
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
            height: 4rem; /* h-16 */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky-header" id="main-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-24">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('site.home') }}" class="text-5xl font-bold text-primary">
                            @if($settings['site_logo'] ?? false)
                                <img src="{{ Storage::url($settings['site_logo']) }}" alt="{{ $settings['site_name'] ?? 'Nexts' }}" class="h-20 w-auto transition-all" id="site-logo">
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
                        <a href="{{ route('site.about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.about') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Sobre
                        </a>
                        <a href="{{ route('site.teams') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.teams') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Equipes
                        </a>
                        <a href="{{ route('site.coaches') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.coaches') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Treinadores
                        </a>
                        <a href="{{ route('site.athletes') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.athletes') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Atletas
                        </a>
                        <a href="{{ route('site.store') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.store') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Loja
                        </a>
                        <a href="{{ route('site.plans') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.plans') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Planos
                        </a>
                        <a href="{{ route('site.contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.contact') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Contato
                        </a>
                        <a href="{{ route('site.blog') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('site.blog*') ? 'border-primary text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                            Blog
                        </a>
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
                <a href="{{ route('site.about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.about') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Sobre
                </a>
                <a href="{{ route('site.teams') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.teams') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Equipes
                </a>
                <a href="{{ route('site.coaches') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.coaches') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Treinadores
                </a>
                <a href="{{ route('site.athletes') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.athletes') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Atletas
                </a>
                <a href="{{ route('site.store') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.store') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Loja
                </a>
                <a href="{{ route('site.plans') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.plans') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Planos
                </a>
                <a href="{{ route('site.contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.contact') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Contato
                </a>
                <a href="{{ route('site.blog') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('site.blog*') ? 'border-primary text-primary bg-blue-50' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50' }} text-base font-medium">
                    Blog
                </a>
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
                        <li><a href="{{ route('site.plans') }}" class="text-gray-300 hover:text-white">Planos</a></li>
                        <li><a href="{{ route('site.contact') }}" class="text-gray-300 hover:text-white">Contato</a></li>
                        <li><a href="{{ route('site.blog') }}" class="text-gray-300 hover:text-white">Blog</a></li>
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
            const logo = document.getElementById('site-logo');
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    nav.classList.add('scrolled');
                    if (logo) logo.classList.replace('h-20', 'h-14');
                } else {
                    nav.classList.remove('scrolled');
                    if (logo) logo.classList.replace('h-14', 'h-20');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
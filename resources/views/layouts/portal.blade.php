<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Portal do Atleta')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <!-- Scripts -->
    {{-- <script src="{{ asset('js/portal-dashboard.js') }}"></script> --}}

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="apple-touch-icon" href="/icon-512.png">

    <script>
        let deferredPrompt;
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const installBtn = document.getElementById('install-pwa');
            if (installBtn) {
                installBtn.classList.remove('hidden');
                installBtn.classList.add('flex');
            }
        });

        async function installPWA() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    deferredPrompt = null;
                    document.getElementById('install-pwa').classList.add('hidden');
                }
            }
        }
    </script>
    @yield('header_styles')
    <style>
        .sidebar-premium {
            background: #0f172a;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .main-content-premium {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
        }
        .premium-gradient-bg {
            background: #0f172a;
        }
    </style>
</head>

<body class="font-sans antialiased @yield('body_class', 'bg-[#0f172a]') text-gray-100">
    <div class="min-h-screen flex premium-gradient-bg">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0 sidebar-premium w-64 md:flex-col">
            <div class="flex flex-col flex-grow pt-5 sidebar-premium overflow-y-auto border-r border-white/5">
                <div class="flex items-center flex-shrink-0 px-4">
                    @php
                        $siteLogo = \App\Models\SiteSetting::get('site_logo');
                        $siteName = $settings['site_name'] ?? tenant('name') ?? 'Nexts';
                    @endphp
                    @if($siteLogo)
                        <img src="{{ \Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="h-14 w-auto">
                    @else
                        <h1 class="text-2xl font-black italic tracking-tighter text-white uppercase">{{ $siteName }}</h1>
                    @endif
                </div>
                <div class="mt-5 flex-grow flex flex-col">
                    <nav class="flex-1 px-2 space-y-1">
                        <a href="{{ route('portal.dashboard') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            Dashboard
                        </a>
                        
                        <a href="{{ route('portal.profile') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.profile') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Meu Perfil
                        </a>
                        
                        <a href="{{ route('portal.performance') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.performance') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Evolução
                        </a>

                        <a href="{{ route('portal.trainings') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.trainings') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Calendário
                        </a>
                        
                        <a href="{{ route('portal.ai-plans') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.ai-plans') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Mentor Pro IA
                        </a>

                        <a href="{{ route('communication.index') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('communication.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Mensagens
                        </a>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'coach')
                        <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all border border-blue-500/20 text-blue-400 hover:bg-blue-500/10 hover:text-blue-300 mt-4">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Gerenciamento do Clube
                        </a>
                        @endif

                        <div class="pt-4 mt-4 border-t border-white/5 px-2 mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-2">Financeiro</span>
                        </div>

                        <a href="{{ route('portal.subscriptions') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.subscriptions') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Minha Assinatura
                        </a>

                        <a href="{{ route('portal.invoices') }}" class="group flex items-center px-4 py-3 text-xs font-bold uppercase tracking-widest rounded-xl transition-all {{ request()->routeIs('portal.invoices') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Faturas
                        </a>
                    </nav>
                </div>
                <div class="px-4 pb-4">
                    <button id="install-pwa" onclick="installPWA()" class="hidden items-center justify-center w-full px-4 py-3 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all bg-green-600 text-white shadow-lg shadow-green-600/20">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Instalar App
                    </button>
                </div>

                <div class="flex-shrink-0 flex border-t border-white/5 p-4">
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex-shrink-0 w-full group block">
                        <div class="flex items-center">
                            <div>
                                <img class="inline-block h-9 w-9 rounded-full border-2 border-white/10" src="{{ Auth::user()->athlete->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" alt="">
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-black text-white uppercase tracking-widest">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Sair da Conta</p>
                            </div>
                        </div>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-[#0f172a] border-b border-white/5">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button id="mobile-sidebar-toggle" class="md:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-400 hover:text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-2xl font-semibold text-white ml-2 md:ml-0">@yield('title', 'Portal do Atleta')</h1>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <!-- Notifications Bell -->
                        <div class="relative">
                            <button id="portal-notification-button" class="p-2 text-gray-400 hover:text-white transition-colors relative focus:outline-none">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @php
                                    // Variables are now provided by AppServiceProvider's View Composer
                                    // $pendingCount, $unreadMessagesCount, $muralCount, $totalNotifications
                                @endphp
                                <span id="notification-badge" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center border-2 border-[#0f172a] {{ $totalNotifications > 0 ? '' : 'hidden' }}">
                                    {{ $totalNotifications }}
                                </span>
                            </button>

                            <!-- Dropdown -->
                            <div id="portal-notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-[#1e293b] rounded-2xl shadow-2xl border border-white/10 z-50 overflow-hidden backdrop-blur-xl">
                                <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/5">
                                    <h3 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Notificações</h3>
                                    <span id="total-notifications-text" class="text-[9px] bg-blue-500/20 text-blue-400 px-2 py-0.5 rounded-full font-black uppercase tracking-widest {{ $totalNotifications > 0 ? '' : 'hidden' }}">{{ $totalNotifications }} Novas</span>
                                </div>
                                <div id="notification-list" class="max-h-96 overflow-y-auto p-2 space-y-2">
                                    <div class="p-8 text-center text-gray-500">
                                        <div class="animate-spin h-5 w-5 border-2 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest">Carregando...</p>
                                    </div>
                                </div>
                                <div class="p-3 border-t border-white/5 bg-white/5 text-center">
                                    <a href="{{ Route::has('communication.index') ? route('communication.index') : '#' }}" class="text-[9px] font-black text-gray-400 hover:text-white uppercase tracking-[0.2em] transition-colors">Ver todas as mensagens</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User menu -->
                        <div class="relative">
                            <div class="flex items-center text-sm">
                                <img class="h-8 w-8 rounded-full border border-white/10" src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                                <span class="ml-2 text-gray-200 hidden sm:block">{{ auth()->user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none main-content-premium @yield('main_class')">
                <div class="@yield('content_padding', 'py-6')">
                    <div class="@yield('content_container', 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8')">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const sidebar = document.querySelector('.min-h-screen > div:first-child');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                    sidebar.classList.toggle('fixed');
                    sidebar.classList.toggle('inset-0');
                    sidebar.classList.toggle('z-50');
                });
            }

            // Notifications Dropdown Toggle
            const notifButton = document.getElementById('portal-notification-button');
            const notifDropdown = document.getElementById('portal-notification-dropdown');
            
            if (notifButton && notifDropdown) {
                notifButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                    if (!notifDropdown.classList.contains('hidden')) {
                        fetchNotifications();
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!notifDropdown.contains(e.target) && !notifButton.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });
            }

            // Notifications logic
            const fetchNotifications = async () => {
                try {
                    // Update counts
                    @if(Route::has('communication.notifications.counts'))
                    const countsRes = await fetch('{{ route('communication.notifications.counts') }}');
                    if (!countsRes.ok) throw new Error('Counts API failed');
                    const countsData = await countsRes.json();
                    
                    if (countsData.success) {
                        const badgeEl = document.getElementById('notification-badge');
                        const totalEl = document.getElementById('total-notifications-text');
                        
                        if (countsData.totalNotifications > 0) {
                            if (badgeEl) {
                                badgeEl.textContent = countsData.totalNotifications;
                                badgeEl.classList.remove('hidden');
                            }
                            if (totalEl) {
                                totalEl.textContent = `${countsData.totalNotifications} Novas`;
                                totalEl.classList.remove('hidden');
                            }
                        } else {
                            if (badgeEl) badgeEl.classList.add('hidden');
                            if (totalEl) totalEl.classList.add('hidden');
                        }
                    }

                    // Fetch list for dropdown
                    @if(Route::has('portal.notifications'))
                    const response = await fetch('{{ route('portal.notifications') }}');
                    if (!response.ok) throw new Error('Notifications API failed');
                    const data = await response.json();
                    if (data.success) {
                        const listEl = document.getElementById('notification-list');
                        
                        if (data.count > 0) {
                            // Update list
                            if (listEl) {
                                listEl.innerHTML = data.notifications.map(n => `
                                    <a href="${n.url || (Route::has('communication.index') ? '{{ route('communication.index') }}' : '#')}" class="block p-3 bg-white/5 hover:bg-white/10 rounded-xl border border-white/5 transition-all group">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-[10px] font-black text-white uppercase tracking-tight group-hover:text-blue-400 transition-colors">${n.title}</h4>
                                            <span class="text-[8px] text-gray-500 font-bold uppercase">${n.created_at}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400 leading-tight">${n.message}</p>
                                    </a>
                                `).join('');
                            }
                        } else {
                            if (listEl) {
                                listEl.innerHTML = `
                                    <div class="p-8 text-center text-gray-500 opacity-40">
                                        <svg class="h-10 w-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-[10px] font-bold uppercase tracking-widest">Sem novas notificações</p>
                                    </div>
                                `;
                            }
                        }
                    }
                    @endif
                    @endif
                } catch (e) {
                    console.error('Error fetching notifications:', e);
                }
            };

            fetchNotifications();
            setInterval(fetchNotifications, 10000); // Check every 10 seconds
        });
    </script>
    @yield('scripts')
    <!-- Toast System -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-600' : (type === 'error' ? 'bg-red-600' : 'bg-blue-600');
            
            toast.className = `${bgColor} text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-y-10 opacity-0`;
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="hover:bg-white/20 rounded-lg p-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Auto show flashes
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ tenant('name') ?? config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    @php
        $favicon = null;
        if (tenancy()->initialized) {
            $favicon = \App\Models\SiteSetting::get('site_logo') ?? tenant('logo');
        } else {
            $favicon = \App\Models\SiteSetting::getCentral('site_logo');
        }
        
        $faviconUrl = asset('favicons/nexts_favicon.png');
        if ($favicon) {
            if (str_starts_with($favicon, 'http')) {
                $faviconUrl = $favicon;
            } elseif (tenancy()->initialized) {
                $faviconUrl = route('tenant.assets', ['path' => $favicon]);
            } else {
                $faviconUrl = \Illuminate\Support\Facades\Storage::url($favicon);
            }
        }
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            base: '#0B0F1A',
                            surface: '#0F1423',
                            accent: '#6366F1'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-sidebar { background: rgba(15, 20, 35, 0.8); backdrop-filter: blur(20px); border-right: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-header { background: rgba(11, 15, 26, 0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#0B0F1A] text-gray-200 antialiased overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar sidebar-transition lg:relative lg:translate-x-0 -translate-x-full">
            <div class="flex flex-col h-full">
                <!-- Brand Logo -->
                <div class="h-24 flex items-center px-8 border-b border-white/5">
                    <div class="flex items-center gap-4">
                        @php
                            $siteLogo = \App\Models\SiteSetting::get('site_logo') ?? tenant('logo');
                            $siteName = \App\Models\SiteSetting::get('site_name') ?? tenant('name') ?? 'Nexts';
                        @endphp
                        
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                            @if($siteLogo)
                                <img src="{{ str_starts_with($siteLogo, 'http') ? $siteLogo : route('tenant.assets', ['path' => $siteLogo]) }}" alt="{{ $siteName }}" class="relative h-16 w-auto rounded-xl shadow-2xl">
                            @else
                                <div class="relative h-16 w-16 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-black text-2xl shadow-2xl border border-white/10 group-hover:scale-105 transition-transform duration-300">
                                    {{ substr($siteName, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="overflow-hidden">
                            <h1 class="text-sm font-black text-white tracking-tight truncate">{{ $siteName }}</h1>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Painel Operacional</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex-1 overflow-y-auto custom-scrollbar px-4 py-8">
                    @if(tenancy()->initialized)
                        <x-sidebar.tenant />
                    @else
                        <x-sidebar.super-admin />
                    @endif
                </div>

                <!-- User Profile / Logout -->
                <div class="p-6 border-t border-white/5">
                    <div class="px-4 py-4 rounded-2xl bg-white/[0.02] border border-white/5 mb-4 group hover:bg-white/5 transition-all">
                        <div class="flex items-center gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-xl object-cover ring-2 ring-white/10 group-hover:ring-indigo-500 transition-all">
                            <div class="overflow-hidden">
                                <p class="text-xs font-black text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mt-0.5">{{ auth()->user()->role === 'admin' ? 'Administrador' : 'Treinador' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest hover:text-rose-500 transition-all group">
                            <svg class="w-4 h-4 mr-3 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Encerrar Sessão
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#0B0F1A] relative">
            
            <!-- Top Header -->
            <header class="h-24 px-8 flex items-center justify-between glass-header sticky top-0 z-40">
                <div class="flex items-center gap-6">
                    <button id="mobile-toggle" class="lg:hidden p-3 bg-white/5 text-gray-400 hover:text-white rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <h2 class="text-xl font-black text-white tracking-tight">@yield('title')</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Nextse</span>
                            <span class="w-1 h-1 bg-white/10 rounded-full"></span>
                            <span class="text-[10px] text-indigo-400 font-bold uppercase tracking-widest">{{ now()->translatedFormat('l, d \d\e F') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative">
                        <button id="notification-button" class="relative p-3 bg-white/5 text-gray-400 hover:text-white rounded-xl transition-all group">
                            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span id="notification-badge" class="absolute top-2.5 right-2.5 w-4 h-4 bg-rose-600 border-2 border-[#0B0F1A] rounded-full text-[8px] font-black text-white flex items-center justify-center {{ ($totalNotifications ?? 0) > 0 ? '' : 'hidden' }}">{{ $totalNotifications ?? 0 }}</span>
                        </button>

                        <div id="notification-dropdown" class="hidden absolute right-0 mt-4 w-80 bg-[#0F1423] rounded-[2rem] shadow-2xl border border-white/10 z-50 overflow-hidden backdrop-blur-xl animate__animated animate__fadeInUp animate__faster">
                            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Notificações</h3>
                                <span id="total-notifications-text" class="text-[9px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded-full font-black uppercase tracking-widest">{{ $totalNotifications ?? 0 }} Novas</span>
                            </div>
                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                <a id="pending-plans-notification" href="{{ ($pendingCount ?? 0) === 1 && isset($lastPendingAthleteId) ? route('admin.athletes.show', $lastPendingAthleteId) . '#ai-plans' : (Route::has('admin.athletes.index') ? route('admin.athletes.index') : '#') }}" class="flex p-5 hover:bg-white/5 border-b border-white/5 transition-colors {{ ($pendingCount ?? 0) > 0 ? '' : 'hidden' }}">
                                    <div class="h-10 w-10 rounded-xl bg-orange-500/10 flex-shrink-0 flex items-center justify-center text-orange-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-xs font-black text-white uppercase tracking-tight">Planos Pendentes</p>
                                        <p class="text-[10px] text-gray-500 font-medium mt-1 leading-relaxed">Existem {{ $pendingCount ?? 0 }} solicitações de IA aguardando sua revisão técnica.</p>
                                    </div>
                                </a>

                                <a id="new-messages-notification" href="{{ Route::has('communication.index') ? route('communication.index') : '#' }}" class="flex p-5 hover:bg-white/5 border-b border-white/5 transition-colors {{ ($unreadMessagesCount ?? 0) > 0 ? '' : 'hidden' }}">
                                    <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex-shrink-0 flex items-center justify-center text-indigo-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-xs font-black text-white uppercase tracking-tight">Mensagens Diretas</p>
                                        <p id="unread-messages-count" class="text-[10px] text-gray-500 font-medium mt-1 leading-relaxed">Você recebeu {{ $unreadMessagesCount ?? 0 }} novas mensagens de atletas.</p>
                                    </div>
                                </a>

                                <div id="empty-notifications-state" class="p-10 text-center text-gray-600 {{ ($totalNotifications ?? 0) == 0 ? '' : 'hidden' }}">
                                    <svg class="h-10 w-10 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em]">Nada por enquanto</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('site.home') }}" target="_blank" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-600/20 hidden md:flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Ver Site Público
                    </a>
                </div>
            </header>

            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-8">
                <div class="max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden transition-opacity duration-300"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mobileToggle = document.getElementById('mobile-toggle');
            const overlay = document.getElementById('overlay');
            const notifButton = document.getElementById('notification-button');
            const notifDropdown = document.getElementById('notification-dropdown');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            if (mobileToggle) mobileToggle.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);

            // Notifications Logic
            if (notifButton && notifDropdown) {
                notifButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!notifDropdown.contains(e.target) && !notifButton.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                    }
                });

                const pollNotificationCounts = () => {
                    @if(Route::has('communication.notifications.counts'))
                    fetch('{{ route('communication.notifications.counts') }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.success) {
                                const badgeEl = document.getElementById('notification-badge');
                                const totalEl = document.getElementById('total-notifications-text');
                                const msgCountEl = document.getElementById('unread-messages-count');
                                
                                if (data.totalNotifications > 0) {
                                    if (badgeEl) {
                                        badgeEl.textContent = data.totalNotifications;
                                        badgeEl.classList.remove('hidden');
                                    }
                                    if (totalEl) totalEl.textContent = `${data.totalNotifications} Novas`;
                                } else {
                                    if (badgeEl) badgeEl.classList.add('hidden');
                                    if (totalEl) totalEl.textContent = `0 Novas`;
                                }

                                const pendingListEl = document.getElementById('pending-plans-notification');
                                const messagesListEl = document.getElementById('new-messages-notification');
                                const emptyListEl = document.getElementById('empty-notifications-state');

                                if (data.pendingCount > 0) {
                                    if (pendingListEl) {
                                        pendingListEl.classList.remove('hidden');
                                        const textEl = pendingListEl.querySelector('p:last-child');
                                        if (textEl) textEl.textContent = `Existem ${data.pendingCount} solicitações de IA aguardando sua revisão técnica.`;
                                    }
                                } else {
                                    if (pendingListEl) pendingListEl.classList.add('hidden');
                                }

                                if (data.unreadMessagesCount > 0) {
                                    if (messagesListEl) {
                                        messagesListEl.classList.remove('hidden');
                                        if (msgCountEl) {
                                            const snippetText = data.lastSnippet ? `<br><span class="text-[9px] text-indigo-400 font-bold">${data.lastSnippet}</span>` : '';
                                            msgCountEl.innerHTML = `Você recebeu ${data.unreadMessagesCount} novas mensagens de atletas.${snippetText}`;
                                        }
                                    }
                                } else {
                                    if (messagesListEl) messagesListEl.classList.add('hidden');
                                }

                                if (data.totalNotifications === 0) {
                                    if (emptyListEl) emptyListEl.classList.remove('hidden');
                                } else {
                                    if (emptyListEl) emptyListEl.classList.add('hidden');
                                }
                            }
                        })
                        .catch(err => console.error('Notification error:', err));
                    @endif
                };

                setInterval(pollNotificationCounts, 10000);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

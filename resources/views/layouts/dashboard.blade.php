<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    @php
        $favicon = null;
        if (tenant()) {
            $favicon = \App\Models\SiteSetting::get('site_logo');
        } else {
            $favicon = \App\Models\SiteSetting::getCentral('site_logo');
        }
        $faviconUrl = $favicon ? \Illuminate\Support\Facades\Storage::url($favicon) : asset('favicons/nexts_favicon.png');
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">

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
    <script src="{{ global_asset('js/dashboard.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @yield('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-grow pt-5 bg-gray-900 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4">
                    @php
                        $siteLogo = \App\Models\SiteSetting::get('site_logo');
                        $siteName = $settings['site_name'] ?? tenant('name') ?? 'Nexts';
                    @endphp
                    @if($siteLogo)
                        <img src="{{ \Storage::url($siteLogo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                    @else
                        <h1 class="text-xl font-bold text-blue-600">{{ $siteName }}</h1>
                    @endif
                </div>
                <div class="mt-5 flex-grow flex flex-col">
                    @if(!tenant())
                        {{-- Context: Central (SaaS Admin) --}}
                        @include('components.sidebar.super-admin')
                    @elseif(auth()->user()->role === 'athlete' || auth()->user()->role === 'guardian')
                        {{-- Context: Tenant (Athlete Portal) --}}
                        @include('components.sidebar.athlete')
                    @else
                        {{-- Context: Tenant (Club Manager/Coach) --}}
                        @include('components.sidebar.tenant')
                    @endif
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
                    <div class="flex items-center overflow-hidden w-full mr-4">
                        <button id="mobile-sidebar-toggle" class="md:hidden -ml-0.5 -mt-0.5 h-12 w-12 flex-shrink-0 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h1 class="text-lg sm:text-2xl font-semibold text-gray-900 truncate">@yield('title', 'Dashboard')</h1>
                        
                        <div class="ml-2 sm:ml-4 flex items-center flex-shrink-0">
                            @if(!tenant())
                                <span class="inline-flex items-center px-2 py-0.5 sm:px-3 rounded-full text-xs sm:text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200" title="Gestão Global (Super Admin)">
                                    <svg class="h-2 w-2 text-purple-400 sm:mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    <span class="hidden sm:inline">Gestão Global</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 sm:px-3 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200" title="Gestão de Clube (Tenant)">
                                    <svg class="h-2 w-2 text-blue-400 sm:mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    <span class="hidden sm:inline">Clube</span>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4 flex-shrink-0">
                        <!-- Notifications Bell -->
                        <div class="relative" id="admin-notifications">
                            <button id="notification-button" class="p-2 text-gray-400 hover:text-gray-500 relative focus:outline-none">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @php
                                    $user = auth()->user();
                                    $isAdmin = $user->role === 'admin';
                                    $isCoach = $user->role === 'coach';
                                    
                                    // Pending AI Plans Count
                                    $pendingQuery = \App\Models\AiGeneratedContent::where('status', 'pending');
                                    if ($isCoach) {
                                        $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                                        $pendingQuery->whereHas('athlete', function($q) use ($coachTeams) {
                                            $q->whereIn('team_id', $coachTeams);
                                        });
                                    }
                                    $pendingCount = $pendingQuery->count();
                                    
                                    // Unread Messages Count
                                    $msgQuery = \App\Models\Message::whereNull('read_at');
                                    if ($isAdmin) {
                                        $adminIds = \App\Models\User::where('role', 'admin')->pluck('id')->toArray();
                                        $msgQuery->whereIn('receiver_id', $adminIds);
                                    } elseif ($isCoach) {
                                        // Messages specifically for the coach OR from their athletes to the club
                                        $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                                        $msgQuery->where(function($q) use ($user, $coachTeams) {
                                            $q->where('receiver_id', $user->id)
                                              ->orWhereHas('sender.athlete', function($aq) use ($coachTeams) {
                                                  $aq->whereIn('team_id', $coachTeams);
                                              });
                                        });
                                    } else {
                                        $msgQuery->where('receiver_id', $user->id);
                                    }
                                    
                                    $unreadMessagesCount = $msgQuery->count();
                                    $totalNotifications = $pendingCount + $unreadMessagesCount;
                                @endphp
                                    <span id="notification-badge" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center border-2 border-white {{ $totalNotifications > 0 ? '' : 'hidden' }}">
                                        {{ $totalNotifications }}
                                    </span>
                            </button>

                            <!-- Dropdown -->
                            <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 z-50 overflow-hidden">
                                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Notificações</h3>
                                    <span id="total-notifications-text" class="text-[10px] bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full font-bold">{{ $totalNotifications }} Novas</span>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a id="pending-plans-notification" href="{{ Route::has('admin.athletes.index') ? route('admin.athletes.index') : '#' }}" class="flex p-4 hover:bg-gray-50 border-b border-gray-50 transition-colors {{ $pendingCount > 0 ? '' : 'hidden' }}">
                                        <div class="h-10 w-10 rounded-full bg-orange-100 flex-shrink-0 flex items-center justify-center text-orange-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-900">Planos Pendentes</p>
                                            <p class="text-xs text-gray-500">Existem {{ $pendingCount }} solicitações de IA aguardando revisão.</p>
                                        </div>
                                    </a>

                                    @php
                                        $lastAthleteId = null;
                                        if ($isAdmin && $unreadMessagesCount > 0) {
                                            $lastMessage = \App\Models\Message::whereIn('receiver_id', $adminIds)
                                                ->whereNull('read_at')
                                                ->latest()
                                                ->first();
                                            
                                            if ($lastMessage && $lastMessage->sender && $lastMessage->sender->athlete) {
                                                $lastAthleteId = $lastMessage->sender->athlete->id;
                                            }
                                        }
                                    @endphp
                                    <a id="new-messages-notification" href="{{ $lastAthleteId ? route('communication.index', ['athlete_id' => $lastAthleteId]) : route('communication.index') }}" class="flex p-4 hover:bg-gray-50 border-b border-gray-50 transition-colors {{ $unreadMessagesCount > 0 ? '' : 'hidden' }}">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-bold text-gray-900">Novas Mensagens</p>
                                            <p id="unread-messages-count" class="text-xs text-gray-500">Você recebeu {{ $unreadMessagesCount }} novas mensagens de atletas.</p>
                                        </div>
                                    </a>

                                    <div id="empty-notifications-state" class="p-8 text-center text-gray-400 {{ $totalNotifications == 0 ? '' : 'hidden' }}">
                                        <svg class="h-10 w-10 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-xs font-semibold uppercase tracking-widest">Sem novas notificações</p>
                                    </div>

                                    @if($totalNotifications == 0)
                                    <div class="p-8 text-center text-gray-400">
                                        <svg class="mx-auto h-12 w-12 opacity-20 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                        <p class="text-sm font-medium">Tudo em dia!</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative" id="user-menu">
                            <button id="user-menu-button" class="flex items-center space-x-3 p-1 pr-3 bg-gray-50 hover:bg-gray-100 rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="w-8 h-8 rounded-full object-cover border border-white shadow-sm">
                                <div class="hidden md:block text-left">
                                    <p class="text-xs font-bold text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">{{ auth()->user()->role }}</p>
                                </div>
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown menu -->
                            <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 hidden z-50 border border-gray-100">
                                <div class="px-4 py-2 border-b border-gray-50 mb-1">
                                    <p class="text-xs font-semibold text-gray-400 uppercase">Minha Conta</p>
                                </div>
                                @if(auth()->user()->role === 'coach')
                                <a href="{{ route('admin.coach.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Meu Perfil
                                </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700 shadow-sm rounded-r-lg flex items-center">
                                <svg class="h-5 w-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 shadow-sm rounded-r-lg flex items-center">
                                <svg class="h-5 w-5 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 shadow-sm rounded-r-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="h-5 w-5 mr-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span class="font-bold uppercase tracking-tight text-xs">Existem erros no formulário:</span>
                                </div>
                                <ul class="list-disc list-inside text-sm space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // User menu dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');
            
            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.add('hidden');
                    }
                });
            }
        });

        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const sidebar = document.querySelector('.min-h-screen > div:first-child'); // The sidebar div
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                    sidebar.classList.toggle('fixed');
                    sidebar.classList.toggle('inset-0');
                    sidebar.classList.toggle('z-50');
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifButton = document.getElementById('notification-button');
            const notifDropdown = document.getElementById('notification-dropdown');
            
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
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
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

                                // Update list visibility
                                const pendingListEl = document.getElementById('pending-plans-notification');
                                const messagesListEl = document.getElementById('new-messages-notification');
                                const emptyListEl = document.getElementById('empty-notifications-state');

                                if (data.pendingCount > 0) {
                                    if (pendingListEl) {
                                        pendingListEl.classList.remove('hidden');
                                        const textEl = pendingListEl.querySelector('.text-xs');
                                        if (textEl) textEl.textContent = `Existem ${data.pendingCount} solicitações de IA aguardando revisão.`;
                                    }
                                } else {
                                    if (pendingListEl) pendingListEl.classList.add('hidden');
                                }

                                if (data.unreadMessagesCount > 0) {
                                    if (messagesListEl) {
                                        messagesListEl.classList.remove('hidden');
                                        if (msgCountEl) {
                                            const snippetText = data.lastSnippet ? `<br><span class="text-[10px] text-blue-500 font-bold">${data.lastSnippet}</span>` : '';
                                            msgCountEl.innerHTML = `Você recebeu ${data.unreadMessagesCount} novas mensagens de atletas.${snippetText}`;
                                        }
                                        
                                        // Update link with last sender ID
                                        @if(Route::has('communication.index'))
                                        if (data.lastAthleteId) {
                                            messagesListEl.href = `{{ route('communication.index') }}?athlete_id=${data.lastAthleteId}`;
                                        } else {
                                            messagesListEl.href = `{{ route('communication.index') }}`;
                                        }
                                        @endif
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
                        });
                    @endif
                };

                setInterval(pollNotificationCounts, 10000); // Check every 10 seconds
            }
        });
    </script>
    @yield('scripts')
    <!-- Toast System -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-600' : (type === 'error' ? 'bg-red-600' : 'bg-indigo-600');
            
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

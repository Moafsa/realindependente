<!-- Super Admin Sidebar (Central) -->
<nav class="space-y-1">
    <div class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Administração Global</div>
    
    <a href="{{ route('dashboard') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </div>
        <span>Dashboard Global</span>
    </a>
    
    <a href="{{ route('admin.tenants.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.tenants.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.tenants.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <span>Clubes (Tenants)</span>
    </a>

    <a href="{{ route('admin.plans.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.plans.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.plans.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
        </div>
        <span>Planos & Preços</span>
    </a>

    <a href="{{ route('admin.ai.monitoring') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.ai.monitoring') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.ai.monitoring') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
        </div>
        <span>Custos de IA</span>
    </a>

    <div class="px-6 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Sistema</div>

    <a href="{{ route('admin.settings.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.settings.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <span>Configurações Gerais</span>
    </a>

    <a href="{{ route('admin.legal.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.legal.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.legal.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <span>Jurídico & Termos</span>
    </a>

    <a href="{{ route('admin.whatsapp.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.whatsapp.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.whatsapp.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
        </div>
        <span>WhatsApp Global</span>
    </a>
</nav>

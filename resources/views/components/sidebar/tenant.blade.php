<!-- Tenant Admin Sidebar (Club) -->
<nav class="space-y-1">
    <div class="px-6 py-4 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Navegação Principal</div>
    
    <a href="{{ route('dashboard') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </div>
        <span>Painel de Controle</span>
    </a>
    
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'coach')
    <a href="{{ route('admin.athletes.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.athletes.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.athletes.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <span>Gestão de Atletas</span>
    </a>
    
    <a href="{{ route('admin.teams.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.teams.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.teams.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <span>Equipes & Grupos</span>
    </a>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.coaches.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.coaches.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.coaches.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <span>Corpo Técnico</span>
    </a>
    @endif
    @endif

    <a href="{{ route('trainings.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('trainings.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('trainings.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <span>Agenda de Treinos</span>
    </a>
    
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'coach')
    <a href="{{ route('communication.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('communication.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('communication.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </div>
        <span>Comunicação</span>
    </a>
    @endif

    @if(auth()->user()->role === 'coach')
    <div class="px-6 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Área do Treinador</div>
    
    <a href="{{ route('admin.coach.profile') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.coach.profile') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.coach.profile') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <span>Meu Perfil Profissional</span>
    </a>
    
    <a href="{{ route('admin.coaches.extract') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.coaches.extract') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.coaches.extract') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
        </div>
        <span>Extrato Financeiro</span>
    </a>
    @endif

    @if(auth()->user()->role === 'admin')
    <div class="px-6 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Gestão de Negócios</div>

    <a href="{{ route('financial.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('financial.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('financial.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
        </div>
        <span>Mensalidades Atletas</span>
    </a>

    <a href="{{ route('admin.cash-flow.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.cash-flow.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.cash-flow.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
        </div>
        <span>Fluxo de Caixa</span>
    </a>
    
    <a href="{{ route('products.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('products.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <span>Loja Oficial Clube</span>
    </a>

    <a href="{{ route('admin.subscription-plans.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.subscription-plans.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <span>Planos de Assinatura</span>
    </a>

    <a href="{{ route('site.editor') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('site.editor') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('site.editor') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </div>
        <span>Gestão de Website</span>
    </a>

    <div class="px-6 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] opacity-50">Configurações & IA</div>

    <a href="{{ route('admin.billing.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.billing.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.billing.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <span>Minhas Faturas</span>
    </a>

    <a href="{{ route('admin.whatsapp.index') }}" class="group flex items-center px-6 py-4 text-sm font-bold rounded-2xl {{ request()->routeIs('admin.whatsapp.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} transition-all duration-300">
        <div class="p-2 rounded-xl {{ request()->routeIs('admin.whatsapp.*') ? 'bg-white/20' : 'bg-white/5 group-hover:bg-white/10' }} mr-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
        </div>
        <span>Conectar WhatsApp</span>
    </a>
    @endif
</nav>

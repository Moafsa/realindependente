@extends('layouts.portal')

@section('title', 'Planos de IA')

@section('header_styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white uppercase italic tracking-tighter">Mentor <span class="text-blue-500">Pro IA</span></h1>
            <p class="text-gray-400">Solicite seus planos personalizados de treino e nutrição</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="requestPlan('workout_plan')" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                Solicitar Treino
            </button>
            <button onclick="requestPlan('meal_plan')" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                Solicitar Nutrição
            </button>
            <button onclick="document.getElementById('mealPhotoInput').click()" class="bg-green-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Analisar Prato
            </button>
            <input type="file" id="mealPhotoInput" class="hidden" accept="image/*" onchange="uploadMealPhoto(this)">
        </div>
    </div>

    <!-- Tab Switcher -->
    <div class="flex border-b border-white/10 mb-6">
        <button onclick="switchMainTab('plans')" id="main-tab-plans" class="px-6 py-4 text-sm font-black uppercase tracking-widest text-blue-500 border-b-2 border-blue-500 transition-all">
            Meus Planos
        </button>
        <button onclick="switchMainTab('nutrition')" id="main-tab-nutrition" class="px-6 py-4 text-sm font-black uppercase tracking-widest text-gray-400 border-b-2 border-transparent hover:text-white transition-all">
            Dashboard Nutricional
        </button>
    </div>

    <div id="plans-view">
        <!-- Filters -->
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 rounded-2xl shadow-xl">
        <div class="flex space-x-2">
            <button onclick="filterContent('all')" class="filter-btn active px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white transition-all">
                Todos
            </button>
            <button onclick="filterContent('workout_plan')" class="filter-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-all">
                Treinos
            </button>
            <button onclick="filterContent('meal_plan')" class="filter-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-all">
                Nutrição
            </button>
            <button onclick="filterContent('favorites')" class="filter-btn px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-white transition-all">
                Favoritos
            </button>
        </div>
    </div>

    <!-- AI Content Grid -->
    <div id="ai-content-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($aiContent as $content)
        <div class="ai-content-item bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden group hover:border-blue-500/50 transition-all" data-type="{{ $content->type }}" data-favorite="{{ $content->is_favorite ? 'true' : 'false' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-12 w-12 rounded-xl flex items-center justify-center {{ $content->type === 'workout_plan' ? 'bg-blue-500/10 text-blue-400' : 'bg-indigo-500/10 text-indigo-400' }}">
                            @if($content->type === 'workout_plan')
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            @else
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white uppercase italic tracking-tight">
                                @if($content->status === 'pending')
                                    Solicitação Pendente
                                @elseif($content->status === 'waiting_acceptance')
                                    Aguardando seu Aceite
                                @elseif($content->status === 'suspended')
                                    Plano Suspenso
                                @else
                                    {{ $content->title }}
                                @endif
                            </h3>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                Criado em {{ $content->generated_at->format('d/m/Y H:i') }}
                                @if($content->accepted_at)
                                    <span class="text-green-500 ml-2">✓ Aceito em {{ $content->accepted_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($content->status !== 'pending')
                    <button onclick="toggleFavorite({{ $content->id }})" class="favorite-btn p-2 text-gray-500 hover:text-yellow-500 transition-colors {{ $content->is_favorite ? 'text-yellow-500' : '' }}">
                        <svg class="h-5 w-5" fill="{{ $content->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </button>
                    @endif
                </div>
                
                @if($content->status === 'pending')
                <div class="py-4 px-3 bg-blue-500/5 rounded-xl border border-blue-500/10 mb-4">
                    <p class="text-xs text-blue-400 font-bold uppercase tracking-widest text-center">Aguardando Avaliação do Coach</p>
                    <p class="text-[11px] text-gray-500 mt-2 text-center">Sua solicitação foi enviada e está sendo analisada pela equipe técnica.</p>
                </div>
                @elseif($content->status === 'waiting_acceptance')
                <div class="py-4 px-3 bg-yellow-500/5 rounded-xl border border-yellow-500/10 mb-4">
                    <p class="text-xs text-yellow-400 font-bold uppercase tracking-widest text-center">Novo Plano Disponível!</p>
                    <p class="text-[11px] text-gray-500 mt-2 text-center">O treinador enviou seu novo protocolo. Aceite abaixo para começar.</p>
                    <button onclick="acceptPlan({{ $content->id }})" class="w-full mt-4 bg-yellow-500 text-white py-2 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-yellow-600 transition-all shadow-lg">
                        Aceitar e Iniciar
                    </button>
                </div>
                @elseif($content->status === 'suspended')
                <div class="py-4 px-3 bg-red-500/5 rounded-xl border border-red-500/10 mb-4 text-center">
                    <p class="text-xs text-red-400 font-bold uppercase tracking-widest">Plano Suspenso</p>
                    <p class="text-[11px] text-gray-500 mt-1">Este protocolo foi temporariamente interrompido pelo treinador.</p>
                </div>
                @else
                <p class="text-sm text-gray-400 mb-4 line-clamp-2 leading-relaxed">{{ $content->summary }}</p>
                
                <div class="flex items-center space-x-4 text-[10px] font-black uppercase tracking-widest text-gray-500 mb-4">
                    @if($content->type === 'workout_plan')
                    <span class="flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ $content->duration ?? 'N/A' }}</span>
                    <span class="flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>{{ $content->difficulty ?? 'N/A' }}</span>
                    @else
                    <span class="flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.99 7.99 0 0120 13a7.98 7.98 0 01-2.343 5.657z"></path></svg>{{ $content->calories ?? 'N/A' }} kcal</span>
                    @endif
                </div>
                @endif
                
                <div class="flex justify-between items-center mt-auto">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $content->type === 'workout_plan' ? 'bg-blue-500/20 text-blue-400' : 'bg-indigo-500/20 text-indigo-400' }}">
                        {{ $content->type === 'workout_plan' ? 'Treino' : 'Nutrição' }}
                    </span>
                    @if($content->status !== 'pending')
                    <button onclick="viewContent({{ $content->id }})" class="px-4 py-2 rounded-lg bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest transition-all">
                        Detalhes
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20 bg-white/5 rounded-3xl border border-dashed border-white/10">
            <svg class="mx-auto h-16 w-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="text-xl font-black text-white uppercase italic tracking-tight">Nenhum plano encontrado</h3>
            <p class="text-sm text-gray-500 mt-2">Comece solicitando seu primeiro plano personalizado.</p>
            <div class="mt-8 flex justify-center space-x-4">
                <button onclick="requestPlan('workout_plan')" class="bg-blue-600 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                    Solicitar Treino
                </button>
                <button onclick="requestPlan('meal_plan')" class="bg-indigo-600 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                    Solicitar Nutrição
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Nutrition Dashboard (Comparative) -->
    <div id="nutrition-view" class="hidden space-y-6">
        @php
            $today = now()->format('Y-m-d');
            $todayTotals = $nutritionDailyTotals->get($today) ?? ['calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
            $targets = $activeMealPlan ? ($activeMealPlan->content['total'] ?? []) : [];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Calorie Card -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Calorias</span>
                    <span class="text-[9px] font-bold text-blue-400 bg-blue-400/10 px-2 py-1 rounded">Meta: {{ $targets['calories'] ?? '?' }}</span>
                </div>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-black text-white leading-none">{{ number_format($todayTotals['calories']) }}</span>
                    <span class="text-[10px] text-gray-500 font-bold uppercase mb-1">kcal</span>
                </div>
                <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    @php $pct = isset($targets['calories']) && $targets['calories'] > 0 ? ($todayTotals['calories'] / $targets['calories']) * 100 : 0; @endphp
                    <div class="h-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>

            <!-- Protein Card -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Proteínas</span>
                    <span class="text-[9px] font-bold text-green-400 bg-green-400/10 px-2 py-1 rounded">Meta: {{ $targets['protein'] ?? '?' }}g</span>
                </div>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-black text-white leading-none">{{ number_format($todayTotals['protein']) }}</span>
                    <span class="text-[10px] text-gray-500 font-bold uppercase mb-1">g</span>
                </div>
                <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    @php $pct = isset($targets['protein']) && $targets['protein'] > 0 ? ($todayTotals['protein'] / $targets['protein']) * 100 : 0; @endphp
                    <div class="h-full bg-green-500 shadow-[0_0_10px_rgba(74,222,128,0.5)] transition-all duration-1000" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>

            <!-- Carbs Card -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Carbos</span>
                    <span class="text-[9px] font-bold text-yellow-400 bg-yellow-400/10 px-2 py-1 rounded">Meta: {{ $targets['carbs'] ?? '?' }}g</span>
                </div>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-black text-white leading-none">{{ number_format($todayTotals['carbs']) }}</span>
                    <span class="text-[10px] text-gray-500 font-bold uppercase mb-1">g</span>
                </div>
                <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    @php $pct = isset($targets['carbs']) && $targets['carbs'] > 0 ? ($todayTotals['carbs'] / $targets['carbs']) * 100 : 0; @endphp
                    <div class="h-full bg-yellow-500 shadow-[0_0_10px_rgba(250,204,21,0.5)] transition-all duration-1000" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>

            <!-- Fat Card -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-5 rounded-2xl shadow-xl">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Gorduras</span>
                    <span class="text-[9px] font-bold text-red-400 bg-red-400/10 px-2 py-1 rounded">Meta: {{ $targets['fat'] ?? '?' }}g</span>
                </div>
                <div class="flex items-end space-x-2">
                    <span class="text-3xl font-black text-white leading-none">{{ number_format($todayTotals['fat']) }}</span>
                    <span class="text-[10px] text-gray-500 font-bold uppercase mb-1">g</span>
                </div>
                <div class="mt-4 h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                    @php $pct = isset($targets['fat']) && $targets['fat'] > 0 ? ($todayTotals['fat'] / $targets['fat']) * 100 : 0; @endphp
                    <div class="h-full bg-red-500 shadow-[0_0_10px_rgba(248,113,113,0.5)] transition-all duration-1000" style="width: {{ min($pct, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Weekly Chart -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-xl">
            <h3 class="text-sm font-black text-white uppercase italic tracking-tight mb-8">Evolução Nutricional (7 Dias)</h3>
            <div class="h-[300px]">
                <canvas id="portalNutritionChart"></canvas>
            </div>
        </div>

        <!-- Meal Logs List -->
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-sm font-black text-white uppercase italic tracking-tight">Histórico de Refeições</h3>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($mealLogs as $log)
                    <div class="p-6 hover:bg-white/5 transition-all group">
                        <div class="flex gap-6">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-2xl border border-white/10 group-hover:border-blue-500/50 transition-all flex-shrink-0">
                                @if($log->photo_path)
                                    <img src="{{ Storage::url($log->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-white/5 flex items-center justify-center text-gray-600">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $log->consumed_at->translatedFormat('d M Y') }}</span>
                                        <h4 class="text-white font-bold">{{ $log->ai_analysis['main_food'] ?? 'Refeição Analisada' }}</h4>
                                    </div>
                                    <span class="text-[10px] font-black text-blue-400 uppercase">{{ $log->consumed_at->format('H:i') }}</span>
                                </div>
                                <div class="flex gap-3 mb-4">
                                    <span class="px-2 py-1 bg-white/5 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest border border-white/5">{{ $log->calories }} kcal</span>
                                    <span class="px-2 py-1 bg-white/5 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest border border-white/5">{{ $log->proteins }}g P</span>
                                    <span class="px-2 py-1 bg-white/5 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest border border-white/5">{{ $log->carbs }}g C</span>
                                </div>
                                <p class="text-[11px] text-gray-400 italic">"{{ $log->ai_analysis['coach_notes'] ?? 'Analisado via Mentor Pro IA Vision' }}"</p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                                    <span class="text-sm font-black text-blue-400">{{ $log->ai_analysis['health_score'] ?? '?' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-sm text-gray-500 italic font-bold uppercase tracking-widest">Nenhuma refeição registrada</p>
                        <button onclick="document.getElementById('mealPhotoInput').click()" class="mt-4 text-xs font-black text-blue-500 uppercase tracking-widest hover:text-blue-400 transition-all">Começar Agora</button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Content Modal -->
<div id="contentModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden">
    <div class="bg-[#0f172a] border border-white/10 w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
        <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/5">
            <h3 class="text-white font-black uppercase text-xs tracking-widest">Detalhes do Plano</h3>
            <button onclick="closeContentModal()" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div id="contentDetails" class="p-8 max-h-[70vh] overflow-y-auto text-gray-300 custom-scrollbar">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
let currentFilter = 'all';

function requestPlan(type) {
    const goal = prompt("Qual o seu objetivo com este novo plano?");
    if (goal === null) return; // Cancelled

    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Enviando...';
    button.disabled = true;

    fetch(`/portal/ai-plans/request`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: type, goal: goal })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao solicitar plano');
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}

function filterContent(filter) {
    currentFilter = filter;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('text-gray-400', 'hover:text-white');
    });
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('text-gray-400', 'hover:text-white');
    
    // Filter content
    const items = document.querySelectorAll('.ai-content-item');
    items.forEach(item => {
        const type = item.dataset.type;
        const isFavorite = item.dataset.favorite === 'true';
        
        let show = false;
        if (filter === 'all') {
            show = true;
        } else if (filter === 'workout_plan' && type === 'workout_plan') {
            show = true;
        } else if (filter === 'meal_plan' && type === 'meal_plan') {
            show = true;
        } else if (filter === 'favorites' && isFavorite) {
            show = true;
        }
        
        item.style.display = show ? 'block' : 'none';
    });
}

function toggleFavorite(contentId) {
    const btn = event.currentTarget;
    fetch(`/portal/ai-plans/${contentId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const svg = btn.querySelector('svg');
            const path = svg.querySelector('path');
            
            if (data.is_favorite) {
                btn.classList.add('text-yellow-500');
                path.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('text-yellow-500');
                path.setAttribute('fill', 'none');
            }
            
            // Update data attribute on card
            btn.closest('.ai-content-item').dataset.favorite = data.is_favorite ? 'true' : 'false';
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function viewContent(contentId) {
    fetch(`/portal/ai-plans/${contentId}/json`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('contentDetails').innerHTML = formatContent(data.data);
            document.getElementById('contentModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao carregar detalhes do plano.');
    });
}

function closeContentModal() {
    document.getElementById('contentModal').classList.add('hidden');
}

function uploadMealPhoto(input) {
    if (!input.files || !input.files[0]) return;

    const formData = new FormData();
    formData.append('photo', input.files[0]);

    // Show loading state
    const btn = document.querySelector('button[onclick*="mealPhotoInput"]');
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24">...</svg> Analisando...';
    btn.disabled = true;

    fetch(`/portal/ai-plans/log-meal`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const analysis = data.data;
            let resultHtml = `
                <div class="space-y-4">
                    <div class="grid grid-cols-4 gap-4">
                        <div class="bg-blue-500/10 p-3 rounded-xl text-center">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">Calorias</span>
                            <span class="text-xl font-black text-blue-400">${analysis.total.calories}</span>
                        </div>
                        <div class="bg-green-500/10 p-3 rounded-xl text-center">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">Proteínas</span>
                            <span class="text-xl font-black text-green-400">${analysis.total.protein}g</span>
                        </div>
                        <div class="bg-yellow-500/10 p-3 rounded-xl text-center">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">Carbos</span>
                            <span class="text-xl font-black text-yellow-400">${analysis.total.carbs}g</span>
                        </div>
                        <div class="bg-red-500/10 p-3 rounded-xl text-center">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase">Gorduras</span>
                            <span class="text-xl font-black text-red-400">${analysis.total.fat}g</span>
                        </div>
                    </div>
                    <div class="bg-white/5 p-4 rounded-xl">
                        <h4 class="text-xs font-black text-white uppercase mb-2">Alimentos Identificados</h4>
                        <ul class="text-xs text-gray-400 space-y-1">
                            ${analysis.food_items.map(item => `<li>• ${item.name} (${item.amount})</li>`).join('')}
                        </ul>
                    </div>
                    <div class="p-4 bg-blue-500/5 rounded-xl border border-blue-500/10">
                        <p class="text-xs text-blue-400 italic">"${analysis.coach_notes}"</p>
                    </div>
                </div>
            `;
            document.getElementById('contentDetails').innerHTML = resultHtml;
            document.getElementById('contentModal').classList.remove('hidden');
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao enviar foto');
    })
    .finally(() => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
        input.value = ''; // Reset input
    });
}

function viewContent(contentId) {
    const modal = document.getElementById('viewModal');
    const container = document.getElementById('view-modal-content');
    
    // Show loading or clear previous
    container.innerHTML = `
        <div class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
            <p class="text-xs font-black text-gray-500 uppercase tracking-widest">Carregando Protocolo...</p>
        </div>
    `;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    fetch(`/portal/ai-plans/${contentId}/json`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = formatContent(data);
            } else {
                alert('Erro ao carregar conteúdo');
                closeViewModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro de conexão');
            closeViewModal();
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function toggleFavorite(contentId) {
    fetch(`/portal/ai-plans/${contentId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const item = document.querySelector(`.ai-content-item[onclick*="viewContent(${contentId})"]`) || 
                         document.querySelector(`button[onclick="toggleFavorite(${contentId})"]`).closest('.ai-content-item');
            
            if (item) {
                item.setAttribute('data-favorite', data.is_favorite ? 'true' : 'false');
                const btn = item.querySelector('.favorite-btn');
                const svg = btn.querySelector('svg');
                if (data.is_favorite) {
                    btn.classList.add('text-yellow-500');
                    svg.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('text-yellow-500');
                    svg.setAttribute('fill', 'none');
                }
            }
        }
    });
}

function acceptPlan(contentId) {
    if (!confirm('Deseja aceitar este plano e começar seu protocolo agora?')) return;

    fetch(`/portal/ai-plans/${contentId}/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao aceitar plano');
    });
}

function formatContent(data) {
    const content = data.content;
    let html = `
        <div class="mb-8">
            <div class="flex justify-between items-start mb-2">
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tight">${data.title || 'Plano Personalizado'}</h2>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${data.status === 'active' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-gray-500/20 text-gray-400 border border-gray-500/30'}">
                    ${data.status === 'active' ? 'Ativo' : 'Concluído'}
                </span>
            </div>
            <div class="flex flex-wrap gap-3 text-[10px] font-black uppercase tracking-widest text-gray-500">
                <span class="px-2 py-1 bg-white/5 rounded border border-white/5">${data.type === 'workout_plan' ? 'Treino' : 'Nutrição'}</span>
                <span>•</span>
                <span>${new Date(data.generated_at).toLocaleDateString('pt-BR')}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                <span class="block text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Objetivo</span>
                <span class="text-xs font-bold text-white">${data.goal || 'Performance'}</span>
            </div>
            <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                <span class="block text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Duração</span>
                <span class="text-xs font-bold text-white">${data.duration || '30 Dias'}</span>
            </div>
            <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                <span class="block text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Frequência</span>
                <span class="text-xs font-bold text-white">${data.frequency || 'N/A'}</span>
            </div>
            <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                <span class="block text-[8px] text-gray-500 font-black uppercase tracking-widest mb-1">Lembretes</span>
                <span class="text-xs font-bold text-blue-400">${(data.notification_settings || []).join(', ') || 'N/A'}</span>
            </div>
        </div>

        <div class="mb-8 p-6 bg-blue-500/5 rounded-2xl border border-blue-500/10">
            <h3 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-3">Análise do Especialista</h3>
            <p class="text-xs text-gray-300 leading-relaxed italic">"${data.content.description || ''}"</p>
        </div>
    `;
    
    if (data.type === 'workout_plan') {
        html += `
            <div class="space-y-6">
                <h3 class="text-sm font-black text-white uppercase tracking-widest border-l-4 border-blue-500 pl-4">Protocolo de Exercícios</h3>
                <div class="space-y-4">
                    ${(content.exercises || []).map(ex => `
                        <div class="bg-white/5 p-5 rounded-2xl border border-white/10 hover:border-blue-500/30 transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-white">${ex.name}</h4>
                                <div class="flex gap-2">
                                    <span class="px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded text-[9px] font-black uppercase">${ex.sets || ''} Séries</span>
                                    <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-400 rounded text-[9px] font-black uppercase">${ex.reps || ''} Reps</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed mb-2">${ex.description || ex.notes || ''}</p>
                            <div class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Descanso: ${ex.rest || 'N/A'}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        html += `
            <div class="grid grid-cols-4 gap-3 mb-8">
                <div class="bg-blue-500/10 p-3 rounded-xl border border-blue-500/20 text-center">
                    <span class="block text-[8px] text-blue-400 font-black uppercase mb-1">Calorias</span>
                    <span class="text-sm font-black text-white">${content.total?.calories || content.calories || 'N/A'}</span>
                </div>
                <div class="bg-green-500/10 p-3 rounded-xl border border-green-500/20 text-center">
                    <span class="block text-[8px] text-green-400 font-black uppercase mb-1">Proteína</span>
                    <span class="text-sm font-black text-white">${content.total?.protein || 'N/A'}g</span>
                </div>
                <div class="bg-yellow-500/10 p-3 rounded-xl border border-yellow-500/20 text-center">
                    <span class="block text-[8px] text-yellow-400 font-black uppercase mb-1">Carbos</span>
                    <span class="text-sm font-black text-white">${content.total?.carbs || 'N/A'}g</span>
                </div>
                <div class="bg-red-500/10 p-3 rounded-xl border border-red-500/20 text-center">
                    <span class="block text-[8px] text-red-400 font-black uppercase mb-1">Gordura</span>
                    <span class="text-sm font-black text-white">${content.total?.fat || 'N/A'}g</span>
                </div>
            </div>
            
            <div class="space-y-6">
                <h3 class="text-sm font-black text-white uppercase tracking-widest border-l-4 border-indigo-500 pl-4">Plano Alimentar</h3>
                <div class="space-y-4">
                    ${(content.meals || []).map(meal => `
                        <div class="bg-white/5 p-5 rounded-2xl border border-white/10">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-white">${meal.name}</h4>
                                <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 rounded-lg text-[9px] font-black uppercase">${meal.time || ''}</span>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                ${(meal.foods || []).map(f => `<span class="px-2 py-0.5 bg-white/5 text-gray-400 rounded text-[8px] font-bold uppercase border border-white/5">${f}</span>`).join('')}
                            </div>
                            <p class="text-xs text-gray-400 leading-relaxed mb-3">${meal.description || ''}</p>
                            <div class="flex gap-4 text-[9px] font-bold text-gray-500 uppercase">
                                <span>🔥 ${meal.calories || 0} kcal</span>
                                <span>🥩 ${meal.protein || 0}g</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    if (content.tips && content.tips.length > 0) {
        html += `
            <div class="mt-8 p-6 bg-amber-500/5 rounded-2xl border border-amber-500/10">
                <h3 class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-3">Dicas do Especialista</h3>
                <ul class="space-y-2">
                    ${content.tips.map(tip => `<li class="text-xs text-gray-400 flex items-start"><span class="text-amber-500 mr-2">•</span>${tip}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    return html;
}
function switchMainTab(tab) {
    const plansView = document.getElementById('plans-view');
    const nutritionView = document.getElementById('nutrition-view');
    const plansBtn = document.getElementById('main-tab-plans');
    const nutritionBtn = document.getElementById('main-tab-nutrition');

    if (tab === 'plans') {
        plansView.classList.remove('hidden');
        nutritionView.classList.add('hidden');
        plansBtn.classList.add('text-blue-500', 'border-blue-500');
        plansBtn.classList.remove('text-gray-400', 'border-transparent');
        nutritionBtn.classList.remove('text-blue-500', 'border-blue-500');
        nutritionBtn.classList.add('text-gray-400', 'border-transparent');
    } else {
        plansView.classList.add('hidden');
        nutritionView.classList.remove('hidden');
        nutritionBtn.classList.add('text-blue-500', 'border-blue-500');
        nutritionBtn.classList.remove('text-gray-400', 'border-transparent');
        plansBtn.classList.remove('text-blue-500', 'border-blue-500');
        plansBtn.classList.add('text-gray-400', 'border-transparent');
        
        // Initialize Chart if not already done
        initNutritionChart();
    }
}

let nutritionChart = null;
function initNutritionChart() {
    if (nutritionChart) return;
    
    const ctx = document.getElementById('portalNutritionChart').getContext('2d');
    const labels = @json($nutritionDailyTotals->keys()->toArray());
    const dataAthlete = @json($nutritionDailyTotals->values()->pluck('calories')->toArray());
    const targetValue = {{ $targets['calories'] ?? 0 }};
    const targetData = labels.map(() => targetValue);

    nutritionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.map(d => {
                const date = new Date(d);
                return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            }),
            datasets: [
                {
                    label: 'Consumo (kcal)',
                    data: dataAthlete,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2
                },
                {
                    label: 'Meta IA (kcal)',
                    data: targetData,
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#9ca3af',
                        usePointStyle: true,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: 'bold' }, color: '#9ca3af' }
                }
            }
        }
    });
}
</script>
@endsection

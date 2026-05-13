@extends('layouts.dashboard')

@section('title', 'Equipes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Equipes</h1>
            <p class="text-gray-600">Gerencie todas as equipes do seu clube</p>
        </div>
        <a href="{{ route('admin.teams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Nova Equipe
        </a>
    </div>
    <!-- Category Filter Bar -->
    <div class="flex flex-wrap gap-2 pb-4 border-b border-gray-100">
        <a href="{{ route('admin.teams.index') }}" 
           class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ !request('category') ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
            Todas as Equipes
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.teams.index', ['category' => $cat]) }}" 
               class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all {{ request('category') == $cat ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>
    <!-- Teams Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teams as $team)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        @if($team->logo)
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                        @else
                        <div class="h-12 w-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background-color: {{ $team->color_primary }}">
                            {{ substr($team->name, 0, 1) }}
                        </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $team->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $team->category }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $team->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $team->is_active ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>
                
                @if($team->description)
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($team->description, 100) }}</p>
                @endif
                
                @if($team->coach)
                <div class="flex items-center space-x-2 mb-4">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Treinador: {{ $team->coach->name }}</span>
                </div>
                @endif
                
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">{{ $team->athletes_count }}</span> atletas
                    </div>
                    
                    <div class="flex space-x-2">
                        <button onclick="openCollectivePlanModal({{ $team->id }}, '{{ $team->name }}')" class="text-green-600 hover:text-green-800 text-sm font-medium">
                            Plano IA
                        </button>
                        <a href="{{ route('admin.teams.show', $team) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Ver
                        </a>
                        <a href="{{ route('admin.teams.edit', $team) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma equipe encontrada</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando uma nova equipe.</p>
            <div class="mt-6">
                <a href="{{ route('admin.teams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Nova Equipe
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal de Plano Coletivo -->
<div id="collectivePlanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-80" onclick="closeCollectivePlanModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="collectivePlanForm" action="" method="POST">
                @csrf
                <div class="px-6 py-4 border-b bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Plano Coletivo IA: <span id="team-name-modal" class="text-blue-600"></span></h3>
                        <button type="button" onclick="closeCollectivePlanModal()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-6">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            <strong>Atenção:</strong> Esta ação gerará um plano individualizado para CADA atleta desta equipe simultaneamente, baseado no biotipo e métricas de cada um.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tipo de Plano</label>
                        <select name="type" required class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="workout_plan">Treino de Performance</option>
                            <option value="meal_plan">Protocolo Nutricional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Objetivo da Equipe</label>
                        <textarea name="goal" required rows="3" placeholder="Ex: Preparação para campeonato regional, foco em resistência..." class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button type="button" onclick="closeCollectivePlanModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">CANCELAR</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition-all flex items-center">
                        <span id="submit-text">GERAR PARA TODOS</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCollectivePlanModal(teamId, teamName) {
        const form = document.getElementById('collectivePlanForm');
        form.action = `/teams/${teamId}/ai-plans/generate`;
        document.getElementById('team-name-modal').textContent = teamName;
        document.getElementById('collectivePlanModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCollectivePlanModal() {
        document.getElementById('collectivePlanModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.getElementById('collectivePlanForm').addEventListener('submit', function() {
        document.getElementById('submit-text').textContent = 'IA PROCESSANDO EQUIPE...';
        this.querySelector('button[type="submit"]').disabled = true;
        this.querySelector('button[type="submit"]').classList.add('opacity-50', 'cursor-not-allowed');
    });
</script>
@endsection

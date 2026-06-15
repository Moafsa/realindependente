@extends('layouts.dashboard')

@section('title', 'Perfil do Atleta - ' . $athlete->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0 text-center sm:text-left">
                <div class="flex-shrink-0">
                    <img class="h-24 w-24 rounded-full border-4 border-white object-cover" 
                         src="{{ $athlete->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($athlete->full_name) . '&size=128&background=random' }}" 
                         alt="{{ $athlete->full_name }}">
                </div>
                <div class="flex-1 text-white w-full">
                    <h1 class="text-2xl sm:text-3xl font-bold mb-2 break-words">{{ $athlete->full_name }}</h1>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-blue-100">
                        @if($athlete->team)
                        <span class="flex items-center text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $athlete->team->name }}
                        </span>
                        @endif
                        @if($athlete->position)
                        <span class="flex items-center text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $athlete->position }}
                        </span>
                        @endif
                        @if($athlete->jersey_number)
                        <span class="flex items-center text-sm sm:text-base">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            #{{ $athlete->jersey_number }}
                        </span>
                        @endif
                        <span class="flex items-center mt-2 sm:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium {{ $athlete->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0 mt-4 sm:mt-0">
                    <a href="{{ route('admin.athletes.edit', $athlete) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition text-sm sm:text-base">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                    <button onclick="toggleEvaluationModal()" 
                       class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition text-sm sm:text-base ml-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Avaliar Atleta
                    </button>
                    <a href="{{ route('communication.index', ['athlete_id' => $athlete->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition text-sm sm:text-base ml-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Chat
                    </a>
                    
                    <form action="{{ route('admin.athletes.toggle-status', $athlete) }}" method="POST" class="inline ml-2">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 {{ $athlete->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg font-semibold transition text-sm sm:text-base">
                            {{ $athlete->is_active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.athletes.destroy', $athlete) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Tem certeza que deseja excluir este atleta?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition text-sm sm:text-base">
                            Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('athletes.partials.evaluation-modal', ['athlete' => $athlete])

    <!-- Tabs Navigation -->
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto whitespace-nowrap hide-scrollbar" aria-label="Tabs">
                <button onclick="showTab('profile')" 
                        id="tab-profile"
                        class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                    Perfil
                </button>
                <button onclick="showTab('performance')" 
                        id="tab-performance"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Desempenho
                </button>
                <button onclick="showTab('financial')" 
                        id="tab-financial"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Financeiro
                </button>
                <button onclick="showTab('ai-plans')" 
                        id="tab-ai-plans"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Planos IA
                </button>
                <button onclick="showTab('nutrition')" 
                        id="tab-nutrition"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Saúde & Nutrição
                </button>
                <button onclick="showTab('documents')" 
                        id="tab-documents"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Documentos
                </button>
                <button onclick="showTab('gallery')" 
                        id="tab-gallery"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Galeria
                </button>
                <button onclick="showTab('history')" 
                        id="tab-history"
                        class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Histórico
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Profile Tab -->
            <div id="content-profile" class="tab-content">
                @include('athletes.partials.profile', ['athlete' => $athlete])
            </div>

            <!-- Performance Tab -->
            <div id="content-performance" class="tab-content hidden">
                @include('athletes.partials.performance', ['athlete' => $athlete])
            </div>

            <!-- Financial Tab -->
            <div id="content-financial" class="tab-content hidden">
                @include('athletes.partials.financial', ['athlete' => $athlete])
            </div>

            <!-- AI Plans Tab -->
            <div id="content-ai-plans" class="tab-content hidden">
                @include('athletes.partials.ai-plans', ['athlete' => $athlete])
            </div>

            <!-- Nutrition Tab -->
            <div id="content-nutrition" class="tab-content hidden">
                @include('athletes.partials.nutrition', ['athlete' => $athlete])
            </div>

            <!-- Documents Tab -->
            <div id="content-documents" class="tab-content hidden">
                @include('athletes.partials.documents', ['athlete' => $athlete])
            </div>

            <!-- Gallery Tab -->
            <div id="content-gallery" class="tab-content hidden">
                <x-gallery-manager :galleryItems="$athlete->galleryItems" galleryableType="App\Models\Athlete" :galleryableId="$athlete->id" />
            </div>

            <!-- History Tab -->
            <div id="content-history" class="tab-content hidden">
                @include('athletes.partials.history', ['athlete' => $athlete])
            </div>
        </div>
    </div>
</div>

<script>
    window.athleteId = {{ $athlete->id }};
</script>
<script src="{{ global_asset('js/athlete-profile.js') }}?v={{ time() }}"></script>
@endsection


@extends('layouts.dashboard')

@section('title', 'Perfil do Atleta - ' . $athlete->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <img class="h-24 w-24 rounded-full border-4 border-white object-cover" 
                         src="{{ $athlete->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($athlete->full_name) . '&size=128&background=random' }}" 
                         alt="{{ $athlete->full_name }}">
                </div>
                <div class="flex-1 text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $athlete->full_name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-blue-100">
                        @if($athlete->team)
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $athlete->team->name }}
                        </span>
                        @endif
                        @if($athlete->position)
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $athlete->position }}
                        </span>
                        @endif
                        @if($athlete->jersey_number)
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            #{{ $athlete->jersey_number }}
                        </span>
                        @endif
                        <span class="flex items-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $athlete->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </span>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('admin.athletes.edit', $athlete) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white shadow rounded-lg">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px" aria-label="Tabs">
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
        </div>
    </div>
</div>

<script src="{{ asset('js/athlete-profile.js') }}"></script>
@endsection


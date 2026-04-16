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

    <!-- Teams Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($teams as $team)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        @if($team->logo)
                        <img class="h-12 w-12 rounded-full object-cover" src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}">
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
@endsection

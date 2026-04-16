@extends('layouts.site')

@section('title', 'Equipes')
@section('description', 'Conheça todas as equipes do clube')

@section('content')
<!-- Header -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Nossas Equipes</h1>
            <p class="text-lg text-gray-600">Conheça todas as equipes que representam nosso clube</p>
        </div>
    </div>
</section>

<!-- Teams Grid -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($teams as $team)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($team->logo)
                        <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $team->logo) }}" alt="{{ $team->name }}">
                        @else
                        <div class="h-16 w-16 rounded-full flex items-center justify-center text-white font-bold text-xl" style="background-color: {{ $team->color_primary }}">
                            {{ substr($team->name, 0, 1) }}
                        </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $team->name }}</h3>
                            <p class="text-gray-600">{{ $team->category }}</p>
                        </div>
                    </div>
                    
                    @if($team->description)
                    <p class="text-gray-600 mb-4">{{ Str::limit($team->description, 150) }}</p>
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
                        <a href="{{ route('site.team', $team) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Ver Detalhes →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma equipe encontrada</h3>
                <p class="mt-1 text-sm text-gray-500">Não há equipes cadastradas no momento.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

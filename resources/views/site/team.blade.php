@extends('layouts.site')

@section('title', $team->name)
@section('description', $team->description ?? 'Conheça a equipe ' . $team->name)
@section('og-image', $team->logo ? asset('storage/' . $team->logo) : null)
@section('og-type', 'sports_team')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ $team->name }}</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">{{ $team->category }}</p>
        </div>
    </div>
</section>

<!-- Team Info -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Info -->
            <div class="lg:col-span-2">
                @if($team->description)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Sobre a Equipe</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $team->description }}</p>
                </div>
                @endif

                <!-- Athletes Grid -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Atletas</h2>
                    @if($athletes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($athletes as $athlete)
                        <a href="{{ route('site.athlete', $athlete) }}" class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $athlete->profile_picture_url }}" 
                                     alt="{{ $athlete->full_name }}" 
                                     class="w-16 h-16 rounded-full object-cover">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $athlete->full_name }}</h3>
                                    @if($athlete->position)
                                    <p class="text-sm text-gray-600">{{ ucfirst($athlete->position) }}</p>
                                    @endif
                                    @if($athlete->jersey_number)
                                    <p class="text-sm text-gray-500">#{{ $athlete->jersey_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500">Nenhum atleta cadastrado nesta equipe.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Team Stats -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Atletas</dt>
                            <dd class="font-semibold text-gray-900">{{ $athletes->count() }}</dd>
                        </div>
                        @if($team->coach)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Técnico</dt>
                            <dd class="font-semibold text-gray-900">{{ $team->coach->name }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Quick Links -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Links Rápidos</h3>
                    <div class="space-y-2">
                        <a href="{{ route('site.teams') }}" class="block text-blue-600 hover:text-blue-800">
                            ← Voltar para Equipes
                        </a>
                        <a href="{{ route('site.athletes') }}" class="block text-blue-600 hover:text-blue-800">
                            Ver Todos os Atletas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


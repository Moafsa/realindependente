@extends('layouts.site')

@section('title', $athlete->full_name)
@section('description', $athlete->bio ?? 'Conheça o atleta ' . $athlete->full_name)
@section('og-image', $athlete->profile_picture_url)
@section('og-type', 'profile')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-green-600 via-teal-600 to-cyan-600 text-white">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <div class="mb-6">
                <img src="{{ $athlete->profile_picture_url }}" 
                     alt="{{ $athlete->full_name }}" 
                     class="w-32 h-32 rounded-full border-4 border-white object-cover mx-auto">
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $athlete->full_name }}</h1>
            @if($athlete->position)
            <p class="text-xl md:text-2xl mb-2 opacity-90">{{ ucfirst($athlete->position) }}</p>
            @endif
            @if($athlete->team)
            <p class="text-lg opacity-75">{{ $athlete->team->name }}</p>
            @endif
        </div>
    </div>
</section>

<!-- Athlete Info -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($athlete->bio)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Biografia</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $athlete->bio }}</p>
                </div>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    @if($athlete->age)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $athlete->age }}</div>
                        <div class="text-sm text-gray-600">Anos</div>
                    </div>
                    @endif
                    @if($athlete->height)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($athlete->height, 2, ',', '.') }}</div>
                        <div class="text-sm text-gray-600">Altura (cm)</div>
                    </div>
                    @endif
                    @if($athlete->weight)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($athlete->weight, 2, ',', '.') }}</div>
                        <div class="text-sm text-gray-600">Peso (kg)</div>
                    </div>
                    @endif
                    @if($athlete->jersey_number)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-gray-900">#{{ $athlete->jersey_number }}</div>
                        <div class="text-sm text-gray-600">Camisa</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Team Info -->
                @if($athlete->team)
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipe</h3>
                    <a href="{{ route('site.team', $athlete->team) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        {{ $athlete->team->name }}
                    </a>
                    <p class="text-sm text-gray-600 mt-1">{{ $athlete->team->category }}</p>
                </div>
                @endif

                <!-- Quick Links -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Links Rápidos</h3>
                    <div class="space-y-2">
                        <a href="{{ route('site.athletes') }}" class="block text-blue-600 hover:text-blue-800">
                            ← Voltar para Atletas
                        </a>
                        @if($athlete->team)
                        <a href="{{ route('site.team', $athlete->team) }}" class="block text-blue-600 hover:text-blue-800">
                            Ver Equipe
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


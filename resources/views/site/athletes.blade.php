@extends('layouts.site')

@section('title', 'Atletas')
@section('description', 'Conheça nossos atletas')

@section('content')
<!-- Hero Section -->
<section class="relative text-white" 
         style="background: {{ ($settings['athletes_banner'] ?? false) ? 'url(' . Storage::url($settings['athletes_banner']) . ')' : 'linear-gradient(to right, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                {{ $settings['athletes_title'] ?? 'Nossos Atletas' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                {{ $settings['athletes_subtitle'] ?? 'Conheça os talentos do Nexts' }}
            </p>
            <p class="text-lg mb-12 max-w-3xl mx-auto">
                {{ $settings['athletes_description'] ?? 'Atletas dedicados, comprometidos e em constante evolução. Nossa seleção representa o futuro do futebol brasileiro.' }}
            </p>
        </div>
    </div>
</section>


<!-- Athletes Grid -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Athletes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($athletes as $athlete)
            <a href="{{ route('site.athlete', $athlete) }}" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="relative">
                    <img src="{{ $athlete->profile_picture_url }}" 
                         alt="{{ $athlete->full_name }}" 
                         class="w-full h-64 object-cover">
                    @if($athlete->team)
                    <div class="absolute top-4 right-4">
                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $athlete->team->name }}
                        </span>
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $athlete->full_name }}</h3>
                    @if($athlete->position)
                    <p class="text-gray-600 mb-4">{{ ucfirst($athlete->position) }}</p>
                    @endif
                    <div class="space-y-2">
                        @if($athlete->age)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Idade:</span>
                            <span class="font-semibold">{{ $athlete->age }} anos</span>
                        </div>
                        @endif
                        @if($athlete->height)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Altura:</span>
                            <span class="font-semibold">{{ number_format($athlete->height, 2, ',', '.') }}m</span>
                        </div>
                        @endif
                        @if($athlete->jersey_number)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Camisa:</span>
                            <span class="font-semibold">#{{ $athlete->jersey_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum atleta encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">Não há atletas cadastrados no momento.</p>
            </div>
            @endforelse
        </div>

        @if($athletes->hasPages())
        <div class="mt-12">
            {{ $athletes->links() }}
        </div>
        @endif

    </div>
</section>

@endsection
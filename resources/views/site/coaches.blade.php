@extends('layouts.site')

@section('title', 'Nossa Comissão Técnica')

@section('content')
<!-- Hero Section -->
<section class="relative text-white" 
         style="background: {{ ($settings['coaches_banner'] ?? false) ? 'url(' . Storage::url($settings['coaches_banner']) . ')' : 'linear-gradient(to right, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                {{ $settings['coaches_title'] ?? 'Comissão Técnica' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                {{ $settings['coaches_subtitle'] ?? 'Conheça os profissionais responsáveis por lapidar nossos talentos.' }}
            </p>
            @if(!empty($settings['coaches_description']))
            <p class="text-lg mb-12 max-w-3xl mx-auto">
                {{ $settings['coaches_description'] }}
            </p>
            @endif
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($coaches as $coach)
            <div class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                <div class="relative h-80 overflow-hidden">
                    <img src="{{ $coach->avatar_url }}" alt="{{ $coach->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-60"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-full mb-2">TREINADOR</span>
                        <h2 class="text-2xl font-bold text-white">{{ $coach->name }}</h2>
                    </div>
                </div>
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-2">Especialidades</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2">{{ $coach->specialties ?? 'Profissional dedicado à formação de atletas.' }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach($coach->teams as $team)
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-medium rounded-lg">
                                {{ $team->name }} ({{ $team->category }})
                            </span>
                        @endforeach
                    </div>

                    <a href="{{ route('site.coach', $coach->id) }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-2xl hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white transition-all group/btn">
                        Ver Perfil Completo
                        <svg class="w-5 h-5 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-20">
                <p class="text-gray-500 dark:text-gray-400 text-xl italic">Nenhum treinador cadastrado no momento.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

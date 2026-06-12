@extends('layouts.site')

@section('title', $settings['hero_title'] ?? 'Início')
@section('description', $settings['site_description'] ?? 'Bem-vindo ao nosso clube de futebol')

@section('content')
@php
    $heroImage = '';
    try {
        $heroImage = ($settings['banner_image'] ?? false) ? \Illuminate\Support\Facades\Storage::url($settings['banner_image']) : '';
    } catch (\Throwable $e) {}
@endphp

<!-- Hero Section -->
<section class="relative text-white min-h-[500px] flex items-center" 
         style="background: {{ $heroImage ? 'url(' . $heroImage . ')' : 'linear-gradient(to right, var(--primary-color), var(--secondary-color))' }}; background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                {{ $settings['hero_title'] ?? tenant('name') ?? 'Bem-vindo' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                {{ $settings['hero_subtitle'] ?? 'Treinamento de Excelência e Tecnologia' }}
            </p>
            <p class="text-lg mb-12 max-w-3xl mx-auto">
                {{ $settings['hero_description'] ?? 'Formando campeões através de treinamento de excelência, valores sólidos e tecnologia de ponta. Nossa missão é desenvolver atletas completos.' }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('site.register') }}" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors shadow-lg">
                    Matricule-se Agora
                </a>
                <a href="#teams" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors">
                    Nossas Equipes
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-primary">{{ $stats['athletes'] ?? 0 }}</div>
                <div class="text-gray-600">{{ $settings['stats_athletes_label'] ?? 'Atletas Ativos' }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-green-600">{{ $stats['history_years'] ?? 10 }}</div>
                <div class="text-gray-600">{{ $settings['stats_history_label'] ?? 'Anos de História' }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-purple-600">{{ $stats['teams'] ?? 0 }}</div>
                <div class="text-gray-600">{{ $settings['stats_teams_label'] ?? 'Categorias' }}</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-orange-600">{{ $stats['titles'] ?? 0 }}</div>
                <div class="text-gray-600">{{ $settings['stats_titles_label'] ?? 'Títulos Conquistados' }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Teams Section -->
<section id="teams" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $settings['teams_section_title'] ?? 'Nossas Equipes' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ $settings['teams_section_subtitle'] ?? 'Desenvolvemos atletas em todas as categorias, com metodologia própria e acompanhamento individualizado.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($teams as $team)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow border-t-4 border-primary relative">
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($team->logo_url)
                            <div class="w-16 h-16 bg-gray-50 rounded-full border border-gray-100 flex items-center justify-center p-2 shrink-0">
                                <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-primary shrink-0">
                                <i class="fas fa-users fa-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $team->name }}</h3>
                            <p class="text-primary font-medium">{{ ucfirst($team->category) }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">{{ $team->athletes_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nível:</span>
                            <span class="font-semibold">{{ ucfirst($team->level) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('site.team', $team->id) }}" class="text-primary font-semibold hover:text-secondary flex items-center">
                            Ver detalhes
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 italic">Nenhuma equipe ativa no momento.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ $settings['methodology_title'] ?? 'Nossa Metodologia' }}
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                {{ $settings['methodology_subtitle'] ?? 'Utilizamos tecnologia de ponta e metodologia própria para desenvolver atletas preparados para o alto rendimento.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <!-- IA Training -->
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:rotate-6 transition-transform shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $settings['feature1_title'] ?? 'Treinamento com IA' }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $settings['feature1_text'] ?? 'Algoritmos avançados geram planos de treinamento técnicos e físicos personalizados para cada posição.' }}</p>
            </div>

            <!-- Performance -->
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:-rotate-6 transition-transform shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $settings['feature2_title'] ?? 'Monitoramento Real' }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $settings['feature2_text'] ?? 'Acompanhe estatísticas, mapas de calor e scouts detalhados de cada partida através do portal.' }}</p>
            </div>

            <!-- Nutrition -->
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 transform group-hover:rotate-6 transition-transform shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $settings['feature3_title'] ?? 'Nutrição do Amanhã' }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $settings['feature3_text'] ?? 'Planos alimentares inteligentes integrados com tecnologia para visualização de pratos e metas calóricas.' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Plans Section -->
@if(isset($plans) && $plans->count() > 0)
<section id="planos" class="py-20 bg-gray-50 border-t border-gray-200">
    @include('site.partials.plans_grid', ['showTitle' => true])
</section>
@endif

<!-- Latest News Section -->
@if($latestPosts->count() > 0)
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Blog & Notícias</h2>
                <p class="text-xl text-gray-600">Fique por dentro de tudo o que acontece no clube.</p>
            </div>
            <a href="{{ route('site.blog') }}" class="hidden md:flex items-center text-primary font-bold hover:text-secondary transition-colors">
                Ver todo o blog <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestPosts as $post)
            <div class="bg-gray-50 rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all group border border-gray-100">
                <div class="h-48 bg-primary relative overflow-hidden">
                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white opacity-20">
                            <i class="fas fa-newspaper fa-5x"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded-lg text-xs font-bold text-primary shadow-sm">
                        {{ $post->published_at->format('d/m/Y') }}
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary transition-colors">
                        {{ Str::limit($post->title, 60) }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                        {{ $post->excerpt }}
                    </p>
                    <a href="{{ route('site.blog.show', $post->slug) }}" class="inline-flex items-center text-primary font-bold text-sm uppercase tracking-wider hover:underline">
                        Ler matéria <i class="fas fa-chevron-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-10 md:hidden text-center">
            <a href="{{ route('site.blog') }}" class="inline-flex items-center text-primary font-bold">
                Ver todo o blog <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-50 border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Contact Info -->
            <div class="space-y-12">
                <div>
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-6">{{ $settings['contact_title'] ?? 'Fale Conosco' }}</h2>
                    <p class="text-lg text-gray-600">
                        {{ $settings['contact_subtitle'] ?? 'Interessado em fazer parte da nossa família? Entre em contato e descubra como transformamos potencial em performance.' }}
                    </p>
                </div>

                <div class="space-y-6">
                    @if($settings['contact_address'] ?? false)
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-primary">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 text-lg">{{ $settings['contact_address'] }}</span>
                    </div>
                    @endif

                    @if($settings['contact_phone'] ?? false)
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 text-lg">{{ $settings['contact_phone'] }}</span>
                    </div>
                    @endif

                    @if($settings['contact_email'] ?? false)
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-gray-700 text-lg">{{ $settings['contact_email'] }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-3xl shadow-2xl p-8 lg:p-12 border border-gray-100">
                <form action="{{ route('site.contact.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Seu Nome</label>
                            <input type="text" name="name" required class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary transition-all font-medium" placeholder="Ex: João Silva">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">E-mail</label>
                            <input type="email" name="email" required class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary transition-all font-medium" placeholder="nome@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mensagem</label>
                        <textarea name="message" rows="4" required class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary transition-all font-medium" placeholder="Como podemos ajudar?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-4 px-8 rounded-2xl font-bold text-lg hover:bg-primary-dark shadow-lg shadow-blue-200 transition-all hover:-translate-y-1">
                        Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
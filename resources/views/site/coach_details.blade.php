@extends('layouts.site')

@section('title', $coach->name . ' - Treinador')

@section('content')
<section class="py-20 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
            <!-- Left Column: Photo and Quick Stats -->
            <div class="lg:col-span-5">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[3rem] blur-xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
                    <div class="relative aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-2xl">
                        <img src="{{ $coach->avatar_url }}" alt="{{ $coach->name }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-10 text-white">
                            <h1 class="text-4xl font-extrabold mb-2">{{ $coach->name }}</h1>
                            <p class="text-blue-400 font-bold uppercase tracking-widest text-sm">Comissão Técnica</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 grid grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-black text-gray-900 dark:text-white mb-1">{{ count($coach->teams) }}</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Equipes</span>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 text-center">
                        <span class="block text-3xl font-black text-gray-900 dark:text-white mb-1">{{ $coach->teams->sum(fn($t) => $t->athletes_count) }}</span>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Atletas</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details -->
            <div class="lg:col-span-7 space-y-12">
                <!-- Bio -->
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-blue-600 rounded-full"></span>
                        Sobre o Treinador
                    </h2>
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed italic">
                        {!! nl2br(e($coach->bio ?? 'Profissional dedicado ao desenvolvimento técnico e humano de jovens atletas, com foco em disciplina e excelência esportiva.')) !!}
                    </div>
                </div>

                <!-- Academic and Experience -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">Formação</h3>
                        <div class="text-gray-600 dark:text-gray-400 text-sm space-y-3">
                            {!! nl2br(e($coach->education ?? 'Formação acadêmica em Educação Física.')) !!}
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">Trajetória</h3>
                        <div class="text-gray-600 dark:text-gray-400 text-sm space-y-3">
                            {!! nl2br(e($coach->experience ?? 'Ampla experiência em clubes de formação e categorias de base.')) !!}
                        </div>
                    </div>
                </div>

                <!-- Certificates -->
                @if(!empty($coach->certificates))
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-wider">Licenças e Certificações</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($coach->certificates as $cert)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <div class="w-12 h-12 bg-white dark:bg-gray-900 rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $cert['name'] }}</h4>
                                <p class="text-xs text-gray-500 uppercase tracking-tighter">{{ \Carbon\Carbon::parse($cert['date'] ?? now())->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                </div>
                @endif

                <!-- Coach Gallery -->
                @if($coach->galleryItems->count() > 0)
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-wider">Mídia & Trabalhos</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($coach->galleryItems as $item)
                            <div class="relative group rounded-2xl overflow-hidden aspect-video bg-gray-100 dark:bg-gray-800 shadow-md">
                                @if($item->type === 'image')
                                    <img src="{{ Storage::url($item->url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @elseif($item->type === 'video')
                                    @php
                                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $item->url, $match);
                                        $youtubeId = $match[1] ?? null;
                                    @endphp
                                    @if($youtubeId)
                                        <img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg'" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @endif
                                    <a href="{{ $item->url }}" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition-colors">
                                        <div class="bg-blue-600/90 text-white rounded-full p-4 transform group-hover:scale-110 transition-transform">
                                            <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    </a>
                                @endif
                                
                                @if($item->title)
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 pt-12">
                                        <p class="text-white font-bold text-xs truncate">{{ $item->title }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Teams Assigned -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-wider">Equipes sob Comando</h3>
                    <div class="space-y-4">
                        @foreach($coach->teams as $team)
                        <div class="group flex items-center justify-between p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 hover:border-blue-500 transition-all">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center font-black text-xl">
                                    {{ substr($team->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $team->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $team->category }} • {{ $team->athletes_count }} Atletas</p>
                                </div>
                            </div>
                            <a href="{{ route('site.team', $team->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.site')

@section('title', $athlete->full_name)
@section('description', $athlete->bio ?? 'Perfil oficial do atleta ' . $athlete->full_name . ' no ' . (tenant('name') ?? 'Clube'))
@section('og-image', $athlete->profile_picture_url)

@section('content')
<div class="min-h-screen bg-[#0f172a] text-white">
    <!-- Player Hero Header -->
    <div class="relative pt-32 pb-20 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-blue-600/20 to-[#0f172a] z-10"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center md:items-end space-y-8 md:space-y-0 md:space-x-12">
                <!-- Avatar with Badge -->
                <div class="relative">
                    <div class="h-48 w-48 md:h-64 md:w-64 rounded-3xl overflow-hidden border-4 border-blue-500 shadow-[0_0_50px_rgba(59,130,246,0.3)] bg-gray-800">
                        <img src="{{ $athlete->profile_picture_url }}" alt="{{ $athlete->full_name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-4 -right-4 bg-blue-600 px-6 py-2 rounded-2xl border-4 border-[#0f172a] shadow-xl">
                        <span class="text-2xl font-black italic tracking-tighter">#{{ $athlete->jersey_number ?? '00' }}</span>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-wrap justify-center md:justify-start gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-xs font-black uppercase tracking-widest border border-blue-500/30">
                            {{ $athlete->subcategory ?? 'Categoria' }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-black uppercase tracking-widest border border-green-500/30">
                            {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black uppercase italic tracking-tighter leading-none mb-4">
                        {{ explode(' ', $athlete->full_name)[0] }} <span class="text-blue-500">{{ explode(' ', $athlete->full_name)[1] ?? '' }}</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-400 font-bold uppercase tracking-tight italic">
                        {{ is_array($athlete->positions) && count($athlete->positions) > 0 ? implode(', ', $athlete->positions) : ($athlete->position ?? 'Atleta') }} • {{ $athlete->team->name ?? 'Sem Equipe' }}
                    </p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center min-w-[120px]">
                        <div class="text-3xl font-black text-blue-500 italic">{{ $athlete->age ?? '--' }}</div>
                        <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Idade</div>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center min-w-[120px]">
                        <div class="text-3xl font-black text-blue-500 italic">
                            @if($athlete->height)
                                @if($athlete->height < 3)
                                    {{ number_format($athlete->height, 2, ',', '.') }}<small class="text-xs">m</small>
                                @else
                                    {{ number_format($athlete->height, 0) }}<small class="text-xs">cm</small>
                                @endif
                            @else
                                --
                            @endif
                        </div>
                        <div class="text-[10px] text-gray-500 font-black uppercase tracking-widest">Altura</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left: Biography & Technical Info -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Bio Card -->
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6 flex items-center">
                        <span class="w-8 h-1 bg-blue-500 mr-4"></span> Biografia do Atleta
                    </h2>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        {{ $athlete->bio ?? 'Este atleta ainda não possui uma biografia cadastrada. Fique ligado para futuras atualizações sobre sua carreira e conquistas.' }}
                    </p>
                </section>

                <!-- Athlete Gallery (Photos) -->
                @php $photos = $athlete->galleryItems->where('type', 'image'); @endphp
                @if($photos->count() > 0)
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 mt-8">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6 flex items-center">
                        <span class="w-8 h-1 bg-blue-500 mr-4"></span> Fotos
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($photos as $index => $item)
                            <div class="relative group rounded-2xl overflow-hidden aspect-square bg-gray-800 border border-white/10 cursor-pointer" onclick="openLightbox({{ $index }}, 'image')">
                                <img src="{{ route('tenant.assets', ['path' => $item->url]) }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @if($item->title)
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 pt-12">
                                        <p class="text-white font-bold text-xs truncate">{{ $item->title }}</p>
                                    </div>
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/30 transition-colors">
                                    <div class="bg-blue-600/80 text-white rounded-full p-3 opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Athlete Gallery (Videos) -->
                @php $videos = $athlete->galleryItems->where('type', 'video'); @endphp
                @if($videos->count() > 0)
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8 mt-8">
                    <h2 class="text-2xl font-black uppercase italic tracking-tighter mb-6 flex items-center">
                        <span class="w-8 h-1 bg-red-500 mr-4"></span> Vídeos
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($videos as $index => $item)
                            <div class="relative group rounded-2xl overflow-hidden aspect-video bg-gray-800 border border-white/10 cursor-pointer" onclick="openLightbox({{ $index }}, 'video')">
                                @php
                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $item->url, $match);
                                    $youtubeId = $match[1] ?? null;
                                @endphp
                                @if($youtubeId)
                                    <img src="https://img.youtube.com/vi/{{ $youtubeId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $youtubeId }}/hqdefault.jpg'" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @endif
                                <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/40 transition-colors">
                                    <div class="bg-red-600 text-white rounded-full p-4 transform group-hover:scale-110 transition-transform shadow-[0_0_20px_rgba(220,38,38,0.5)]">
                                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                    </div>
                                </div>
                                @if($item->title)
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 pt-12 pointer-events-none">
                                        <p class="text-white font-bold text-sm truncate">{{ $item->title }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif

                <!-- Evolution Chart -->
                <section class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <div class="flex justify-between items-center mb-8">
                        <h2 class="text-2xl font-black uppercase italic tracking-tighter flex items-center">
                            <span class="w-8 h-1 bg-blue-500 mr-4"></span> Evolução Técnica
                        </h2>
                        <select id="metric-selector" class="bg-white/5 border border-white/10 rounded-xl text-xs font-bold uppercase px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($chartData as $metric => $data)
                            <option value="{{ $metric }}">{{ $metric }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="evolution-chart" class="h-80 w-full">
                        @if($chartData->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-gray-500 opacity-50">
                            <svg class="h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <p class="font-bold uppercase tracking-widest text-xs">Sem dados de evolução disponíveis</p>
                        </div>
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right: Secondary Info & Actions -->
            <div class="space-y-8">
                <!-- Team Card -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <h3 class="text-blue-200 text-xs font-black uppercase tracking-[0.2em] mb-2">Equipe Atual</h3>
                        <div class="text-3xl font-black uppercase italic tracking-tighter mb-4">{{ $athlete->team->name ?? 'Nexts' }}</div>
                        <a href="{{ $athlete->team ? route('site.team', $athlete->team->id) : '#' }}" class="inline-flex items-center text-sm font-bold uppercase tracking-widest bg-white/20 hover:bg-white/30 px-6 py-3 rounded-2xl transition-all">
                            Ver Equipe <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -bottom-4 -right-4 opacity-20 transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="h-32 w-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4.5 20.29L5.21 21L12 18L18.79 21L19.5 20.29L12 2Z"></path></svg>
                    </div>
                </div>

                @php
                    $activeMatch = null;
                    if ($athlete->team_id) {
                        $activeMatch = \App\Models\TournamentMatch::where(function($q) use ($athlete) {
                            $q->where('home_team_id', $athlete->team_id)
                              ->orWhere('away_team_id', $athlete->team_id);
                        })->whereNotNull('stream_url')->where('status', 'scheduled')->orWhere('status', 'ongoing')->latest()->first();
                    }
                @endphp
                
                @if($activeMatch && $activeMatch->stream_url)
                <!-- Live Stream Card -->
                <div class="bg-red-600 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
                            <h3 class="text-white text-xs font-black uppercase tracking-[0.2em]">AO VIVO AGORA</h3>
                        </div>
                        <div class="text-xl font-black uppercase italic tracking-tighter mb-4 text-white">
                            {{ $activeMatch->homeTeam->name ?? 'Home' }} x {{ $activeMatch->awayTeam->name ?? 'Away' }}
                        </div>
                        <a href="{{ $activeMatch->stream_url }}" target="_blank" class="inline-flex items-center text-sm font-bold uppercase tracking-widest bg-white text-red-600 hover:bg-gray-100 px-6 py-3 rounded-2xl transition-all shadow-lg">
                            Assistir Partida <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -bottom-4 -right-4 opacity-20 transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                        <svg class="h-32 w-32 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"></path></svg>
                    </div>
                </div>
                @endif

                <!-- Attributes Card -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Atributos</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Peso</span>
                            <span class="text-sm font-black italic">{{ $athlete->weight ? number_format($athlete->weight, 1) : '--' }} kg</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Gênero</span>
                            <span class="text-sm font-black italic uppercase">{{ $athlete->gender ?? '--' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-white/5">
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-tight">Membro Forte</span>
                            <span class="text-sm font-black italic">{{ $athlete->dominant_limb ?? '--' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline de Clubes -->
                @if($athlete->history && $athlete->history->count() > 0)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-8">
                    <h3 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">Histórico de Clubes</h3>
                    <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-white/20 before:to-transparent">
                        @foreach($athlete->history->sortByDesc('start_date') as $history)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white/20 bg-[#0f172a] text-blue-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 overflow-hidden">
                                @if($history->club_logo_url)
                                    <img src="{{ route('tenant.assets', ['path' => $history->club_logo_url]) }}" alt="Logo {{ $history->club_name }}" class="w-full h-full object-cover bg-white">
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @endif
                            </div>
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-2xl bg-white/5 border border-white/10">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-bold text-white uppercase italic tracking-tighter">{{ $history->club_name }}</h4>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-400">
                                        {{ $history->start_date->format('Y') }} - {{ $history->end_date ? $history->end_date->format('Y') : 'Atual' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Social Media -->
                @if($athlete->instagram_url || $athlete->facebook_url || $athlete->tiktok_url || $athlete->youtube_url || $athlete->x_url)
                <div class="flex gap-4 flex-wrap">
                    @if($athlete->instagram_url)
                    <a href="{{ $athlete->instagram_url }}" target="_blank" class="flex-1 min-w-[3rem] bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-pink-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    @endif
                    @if($athlete->facebook_url)
                    <a href="{{ $athlete->facebook_url }}" target="_blank" class="flex-1 min-w-[3rem] bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    @endif
                    @if($athlete->tiktok_url)
                    <a href="{{ $athlete->tiktok_url }}" target="_blank" class="flex-1 min-w-[3rem] bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93v7.2c0 1.96-.5 3.93-1.62 5.46-1.15 1.54-2.88 2.6-4.8 2.92-2.03.32-4.17.07-5.94-1-1.7-1.02-3.05-2.61-3.62-4.52-.58-1.93-.41-4.08.43-5.88 1.05-2.18 3.25-3.83 5.6-4.3 1.95-.38 4.02-.12 5.76.75v3.95c-1.3-.85-2.95-1.22-4.46-.86-1.47.33-2.7 1.38-3.32 2.72-.65 1.4-.55 3.08.28 4.38.82 1.25 2.27 2.05 3.8 2.15 1.48.1 3-.18 4.22-1.03 1.2-.85 1.97-2.2 2.1-3.65v-11.4c-.01-.01-.01-.01-.02-.01z"/></svg>
                    </a>
                    @endif
                    @if($athlete->youtube_url)
                    <a href="{{ $athlete->youtube_url }}" target="_blank" class="flex-1 min-w-[3rem] bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    @endif
                    @if($athlete->x_url)
                    <a href="{{ $athlete->x_url }}" target="_blank" class="flex-1 min-w-[3rem] bg-white/5 hover:bg-white/10 border border-white/10 p-4 rounded-2xl transition-all flex items-center justify-center group">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-200" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 z-[100] bg-black/95 hidden items-center justify-center p-4 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-blue-500 transition-colors bg-white/10 p-3 rounded-full backdrop-blur-md z-50">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    
    <button id="lightbox-prev" onclick="lightboxNavigate(-1)" class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-blue-500 transition-colors bg-white/10 p-3 rounded-full backdrop-blur-md z-50 hidden">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    
    <button id="lightbox-next" onclick="lightboxNavigate(1)" class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-blue-500 transition-colors bg-white/10 p-3 rounded-full backdrop-blur-md z-50 hidden">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div id="lightbox-content" class="w-full h-full flex items-center justify-center max-w-6xl">
        <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] object-contain rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.5)] transform scale-95 transition-transform duration-300 hidden">
        <iframe id="lightbox-video" src="" class="w-full aspect-video rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.5)] transform scale-95 transition-transform duration-300 hidden" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartData = @json($chartData);
    
    function initChart(metric) {
        const data = chartData[metric] || [];
        
        const options = {
            series: [{
                name: metric,
                data: data.map(d => d.y)
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#3b82f6'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 4,
                lineCap: 'round'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: data.map(d => new Date(d.x).toLocaleDateString('pt-BR')),
                labels: { style: { colors: '#64748b', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#64748b', fontWeight: 600 } }
            },
            grid: {
                borderColor: 'rgba(255, 255, 255, 0.05)',
                strokeDashArray: 4
            },
            theme: { mode: 'dark' },
            tooltip: {
                theme: 'dark',
                x: { format: 'dd/MM/yyyy' },
                y: { formatter: (val) => val.toFixed(1) }
            }
        };

        if (window.chart) {
            window.chart.destroy();
        }
        
        window.chart = new ApexCharts(document.querySelector("#evolution-chart"), options);
        window.chart.render();
    }

    document.getElementById('metric-selector')?.addEventListener('change', (e) => {
        initChart(e.target.value);
    });

    // Iniciar com a primeira métrica disponível
    const firstMetric = Object.keys(chartData)[0];
    if (firstMetric) {
        initChart(firstMetric);
    }

    // Lightbox Logic
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxVideo = document.getElementById('lightbox-video');
    const prevBtn = document.getElementById('lightbox-prev');
    const nextBtn = document.getElementById('lightbox-next');
    
    let currentGallery = [];
    let currentIndex = 0;
    
    const photoGallery = [
        @foreach($photos->values() as $item)
        { type: 'image', url: "{{ route('tenant.assets', ['path' => $item->url]) }}" },
        @endforeach
    ];
    
    const videoGallery = [
        @foreach($videos->values() as $item)
        @php
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $item->url, $match);
            $ytId = $match[1] ?? null;
            $embedUrl = $ytId ? "https://www.youtube.com/embed/{$ytId}?autoplay=1" : $item->url;
        @endphp
        { type: 'video', url: "{{ $embedUrl }}" },
        @endforeach
    ];

    window.openLightbox = function(index, galleryType) {
        currentGallery = galleryType === 'image' ? photoGallery : videoGallery;
        currentIndex = index;
        
        updateLightboxContent();
        
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        
        // Small delay to allow display block to take effect before changing opacity
        setTimeout(() => {
            lightbox.classList.remove('opacity-0');
            if (lightboxImg.classList.contains('scale-95')) lightboxImg.classList.remove('scale-95');
            if (lightboxVideo.classList.contains('scale-95')) lightboxVideo.classList.remove('scale-95');
        }, 10);
        document.body.style.overflow = 'hidden';
    }
    
    function updateLightboxContent() {
        const item = currentGallery[currentIndex];
        
        if (item.type === 'image') {
            lightboxVideo.classList.add('hidden');
            lightboxVideo.src = '';
            lightboxImg.src = item.url;
            lightboxImg.classList.remove('hidden');
        } else {
            lightboxImg.classList.add('hidden');
            lightboxImg.src = '';
            lightboxVideo.src = item.url;
            lightboxVideo.classList.remove('hidden');
        }
        
        if (currentGallery.length > 1) {
            prevBtn.classList.remove('hidden');
            nextBtn.classList.remove('hidden');
        } else {
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        }
    }
    
    window.lightboxNavigate = function(dir) {
        currentIndex += dir;
        if (currentIndex < 0) currentIndex = currentGallery.length - 1;
        if (currentIndex >= currentGallery.length) currentIndex = 0;
        updateLightboxContent();
    }

    window.closeLightbox = function() {
        lightbox.classList.add('opacity-0');
        lightboxImg.classList.add('scale-95');
        lightboxVideo.classList.add('scale-95');
        
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            lightboxImg.src = '';
            lightboxVideo.src = '';
        }, 300); // Wait for transition to complete
        document.body.style.overflow = 'auto';
    }

    // Close on click outside
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox || e.target.id === 'lightbox-content') {
            closeLightbox();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') lightboxNavigate(-1);
            if (e.key === 'ArrowRight') lightboxNavigate(1);
        }
    });
</script>
@endpush

<style>
    body { background-color: #0f172a; }
</style>
@endsection

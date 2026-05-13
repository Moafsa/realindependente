@extends('layouts.site')

@section('title', $team->name)
@section('description', $team->description ?? 'Conheça a equipe ' . $team->name)

@section('content')
<!-- Hero Section com Cores Dinâmicas -->
<section class="relative min-h-[50vh] flex items-center overflow-hidden" style="background-color: {{ $team->primary_color ?? '#1e40af' }};">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-transparent"></div>
        <!-- Elementos decorativos de fundo -->
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full blur-3xl opacity-50" style="background-color: {{ $team->secondary_color ?? '#3b82f6' }};"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-32 h-32 md:w-48 md:h-48 bg-white rounded-2xl shadow-2xl p-4 flex items-center justify-center transform -rotate-3 hover:rotate-0 transition duration-500">
                @if($team->logo)
                    <img src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}" class="max-w-full max-h-full object-contain">
                @else
                    <div class="text-4xl font-black" style="color: {{ $team->primary_color }};">{{ substr($team->name, 0, 2) }}</div>
                @endif
            </div>
            <div class="text-center md:text-left text-white">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 text-white" style="background-color: {{ $team->secondary_color ?? 'rgba(255,255,255,0.2)' }};">
                    {{ $team->category }} • {{ $team->level ?? 'Amador' }}
                </span>
                <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tight drop-shadow-lg">{{ $team->name }}</h1>
                <p class="text-xl md:text-2xl text-blue-50/80 font-medium">{{ $team->branch->name ?? 'Filial Geral' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Conteúdo Principal -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Bio e Elenco -->
            <div class="lg:col-span-8 space-y-12">
                @if($team->description)
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="w-8 h-1 mr-3 rounded-full" style="background-color: {{ $team->primary_color ?? '#2563eb' }};"></span>
                        Sobre a Equipe
                    </h2>
                    <p class="text-gray-600 leading-relaxed text-lg">{{ $team->description }}</p>
                </div>
                @endif

                <!-- Elenco Moderno -->
                <div>
                    <div class="flex justify-between items-end mb-8">
                        <div>
                            <h2 class="text-3xl font-black text-gray-900">Elenco Atual</h2>
                            <p class="text-gray-500">Nossos talentos em campo</p>
                        </div>
                        <span class="text-sm font-bold text-gray-400">{{ $athletes->count() }} Atletas</span>
                    </div>

                    @if($athletes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($athletes as $athlete)
                        <div class="group relative bg-white p-4 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex items-center gap-4 overflow-hidden">
                            <div class="absolute top-0 right-0 w-24 h-24 bg-gray-50 rounded-bl-full -z-10 group-hover:bg-blue-50 transition-colors"></div>
                            
                            <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-md flex-shrink-0">
                                <img src="{{ $athlete->profile_picture_url }}" alt="{{ $athlete->full_name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold uppercase tracking-widest mb-1" style="color: {{ $team->primary_color ?? '#2563eb' }};">{{ $athlete->position ?? 'Posição' }}</div>
                                <h3 class="text-lg font-bold text-gray-900 truncate group-hover:text-blue-600 transition">{{ $athlete->full_name }}</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xl font-black text-gray-200">#{{ $athlete->jersey_number ?? '00' }}</span>
                                    <a href="{{ route('site.athlete', $athlete) }}" class="text-xs font-bold text-gray-400 group-hover:opacity-80 uppercase" style="color: {{ $team->primary_color ?? '#2563eb' }};">Perfil →</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-100 p-12 rounded-3xl text-center">
                        <p class="text-gray-500 font-medium">Nenhum atleta cadastrado nesta equipe.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Informativa -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Staff -->
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 pb-2 border-b">Informações Técnicas</h3>
                    <div class="space-y-6">
                        @if($team->coach)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase">Técnico Principal</p>
                                <p class="font-bold text-gray-900">{{ $team->coach->name }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Próximo Treino / Compromissos -->
                @php
                    $nextTraining = \App\Models\Training::where('team_id', $team->id)
                        ->where('date', '>=', now()->toDateString())
                        ->orderBy('date')
                        ->orderBy('time')
                        ->first();
                @endphp

                @if($nextTraining)
                <div class="bg-gray-900 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full group-hover:scale-150 transition duration-700"></div>
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Compromisso da Agenda
                    </h3>
                    <div class="space-y-4 relative">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase">Próximo Treino</p>
                            <p class="text-xl font-black">{{ \Carbon\Carbon::parse($nextTraining->date)->format('d/m') }} às {{ $nextTraining->time }}</p>
                            <p class="text-sm text-gray-400">{{ $nextTraining->location }}</p>
                        </div>
                        <div class="pt-4 border-t border-white/10">
                            <p class="text-sm font-medium italic opacity-80">{{ $nextTraining->title }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>
@endsection


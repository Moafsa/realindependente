@extends('layouts.dashboard')

@section('title', 'Equipe: ' . $team->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-8 text-white relative" style="background: linear-gradient(to right, {{ $team->primary_color }}, {{ $team->secondary_color }});">
            <div class="flex items-center space-x-6 relative z-10">
                <div class="flex-shrink-0">
                    @if($team->logo)
                        <img class="h-24 w-24 rounded-2xl border-4 border-white/20 object-cover shadow-xl" src="{{ Storage::url($team->logo) }}" alt="{{ $team->name }}">
                    @else
                        <div class="h-24 w-24 rounded-2xl border-4 border-white/20 flex items-center justify-center text-4xl font-bold bg-white/10 backdrop-blur-md">
                            {{ substr($team->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-3xl font-bold">{{ $team->name }}</h1>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase">
                            {{ $team->category }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-6 text-white/80">
                        @if($team->coach)
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Treinador: {{ $team->coach->name }}
                        </span>
                        @endif
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $athletes->count() }} Atletas
                        </span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.teams.edit', $team) }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition shadow-sm">
                        Editar Equipe
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content: Athletes List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Atletas da Equipe</h2>
                    <a href="{{ route('admin.athletes.create', ['team_id' => $team->id]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-500">
                        + Adicionar Atleta
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atleta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($athletes as $athlete)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ $athlete->profile_picture_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($athlete->full_name) }}" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $athlete->full_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $athlete->branch->name ?? 'Geral' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $athlete->position ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    #{{ $athlete->jersey_number ?? '00' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.athletes.show', $athlete) }}" class="text-blue-600 hover:text-blue-900">Ver Perfil</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    Nenhum atleta vinculado a esta equipe.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Info & Stats -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Sobre a Equipe</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    {{ $team->description ?? 'Nenhuma descrição informada para esta equipe.' }}
                </p>
                
                <div class="mt-6 space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status:</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $team->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $team->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Criada em:</span>
                        <span class="font-medium text-gray-900">{{ $team->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

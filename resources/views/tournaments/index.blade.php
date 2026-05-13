@extends('layouts.dashboard')

@section('title', 'Torneios')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Torneios</h1>
            <p class="text-gray-600">Gerencie as competições e campeonatos do clube</p>
        </div>
        <button onclick="document.getElementById('createTournamentModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Novo Torneio
        </button>
    </div>

    <!-- Tournament List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($tournaments as $tournament)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 16v2m3-6v6m3-8v8m9-10v10M9 21h6m3-3H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($tournament->status == 'ongoing') bg-green-100 text-green-800 
                        @elseif($tournament->status == 'finished') bg-gray-100 text-gray-800 
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($tournament->status) }}
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $tournament->name }}</h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $tournament->description ?? 'Sem descrição informada.' }}</p>

                <div class="space-y-2 mb-6">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $tournament->start_date ? \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') : 'TBD' }} 
                        @if($tournament->end_date) - {{ \Carbon\Carbon::parse($tournament->end_date)->format('d/m/Y') }} @endif
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Formato: {{ ucfirst($tournament->format) }}
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        {{ $tournament->matches_count }} Partidas
                    </span>
                    <a href="{{ route('tournaments.show', $tournament) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm inline-flex items-center">
                        Gerenciar
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21h6l-.75-4M9 21h6M9.75 17L9 21h6l-.75-4M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Nenhum torneio agendado</h3>
            <p class="mt-1 text-gray-500">Comece organizando uma nova competição para o clube.</p>
            <button onclick="document.getElementById('createTournamentModal').classList.remove('hidden')" class="mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Criar Primeiro Torneio
            </button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Criar Torneio -->
<div id="createTournamentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Novo Torneio</h3>
            <form action="{{ route('tournaments.store') }}" method="POST" class="mt-4 text-left space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome do Torneio</label>
                    <input type="text" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Formato</label>
                    <select name="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="league">Liga (Pontos Corridos)</option>
                        <option value="knockout">Mata-Mata</option>
                        <option value="groups">Fase de Grupos + Playoffs</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Início</label>
                        <input type="date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fim</label>
                        <input type="date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="document.getElementById('createTournamentModal').classList.add('hidden')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Criar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

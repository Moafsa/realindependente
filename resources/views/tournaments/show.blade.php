@extends('layouts.dashboard')

@section('title', 'Detalhes do Torneio')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Actions -->
    <div class="flex justify-between items-center">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('tournaments.index') }}" class="text-sm text-gray-700 hover:text-blue-600 font-medium">Torneios</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm text-gray-500 md:ml-2">{{ $tournament->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="flex space-x-2">
            <button onclick="document.getElementById('addMatchModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Nova Partida
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Formato</p>
            <p class="text-lg font-bold text-gray-900">{{ ucfirst($tournament->format) }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Status</p>
            <p class="text-lg font-bold text-gray-900">{{ ucfirst($tournament->status) }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Partidas</p>
            <p class="text-lg font-bold text-gray-900">{{ $tournament->matches->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-xs font-semibold text-gray-400 uppercase">Início</p>
            <p class="text-lg font-bold text-gray-900">{{ $tournament->start_date ? \Carbon\Carbon::parse($tournament->start_date)->format('d/m/Y') : 'Não def' }}</p>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Matches List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="font-bold text-gray-900">Agenda de Jogos</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($tournament->matches as $match)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 text-center md:text-right">
                                <span class="font-semibold text-gray-900">{{ $match->homeTeam->name ?? 'TBD' }}</span>
                            </div>
                            
                            <div class="flex flex-col items-center px-6">
                                <div class="bg-gray-900 text-white px-4 py-1 rounded text-xl font-mono flex items-center space-x-3">
                                    <span>{{ $match->home_score ?? '-' }}</span>
                                    <span class="text-gray-500 text-sm">vs</span>
                                    <span>{{ $match->away_score ?? '-' }}</span>
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">{{ $match->match_date->format('d/m H:i') }}</span>
                            </div>

                            <div class="flex-1 text-center md:text-left">
                                <span class="font-semibold text-gray-900">{{ $match->awayTeam->name ?? 'TBD' }}</span>
                            </div>

                            <div class="flex items-center ml-4">
                                <button onclick="openScoreModal({{ $match->id }}, {{ $match->home_score ?? 0 }}, {{ $match->away_score ?? 0 }}, '{{ $match->stream_url ?? '' }}')" 
                                        class="p-2 text-gray-400 hover:text-blue-600 transition">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-500">
                        Nenhuma partida marcada para este torneio.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Standings / Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="font-bold text-gray-900">Classificação Rápida</h2>
                </div>
                <div class="p-4 text-sm text-gray-500 italic">
                    Funcionalidade de tabela será liberada conforme os jogos forem concluídos.
                </div>
            </div>

            <div class="bg-blue-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="font-bold text-lg mb-2">Gerador de Chaves</h3>
                <p class="text-blue-100 text-sm mb-4">Deseja girar todos os jogos automaticamente baseado nas equipes inscritas?</p>
                <form action="{{ route('tournaments.matches.generate', $tournament->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-white text-blue-600 font-bold py-2 rounded-lg hover:bg-blue-50 transition">
                        Gerar Rodadas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Score -->
<div id="scoreModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Atualizar Placar</h3>
            <form id="scoreForm" method="POST" class="mt-4 space-y-4">
                @csrf
                <div class="flex items-center justify-between">
                    <div class="w-20">
                        <label class="block text-xs font-semibold text-gray-400 uppercase">Home</label>
                        <input type="number" name="home_score" id="modal_home_score" min="0" required class="text-center text-2xl font-bold w-full border-gray-300 rounded-md">
                    </div>
                    <span class="text-gray-400 font-bold mt-4">X</span>
                    <div class="w-20">
                        <label class="block text-xs font-semibold text-gray-400 uppercase">Away</label>
                        <input type="number" name="away_score" id="modal_away_score" min="0" required class="text-center text-2xl font-bold w-full border-gray-300 rounded-md">
                    </div>
                </div>
                </div>
                
                <div class="mt-4 text-left">
                    <label class="block text-xs font-semibold text-gray-700 uppercase">URL da Transmissão Ao Vivo (Opcional)</label>
                    <input type="url" name="stream_url" id="modal_stream_url" placeholder="https://youtube.com/watch?v=..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
                    <p class="text-[10px] text-gray-500 mt-1">Se preenchido, aparecerá na tela do atleta/equipe.</p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('scoreModal').classList.add('hidden')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openScoreModal(matchId, home, away, streamUrl = '') {
        const form = document.getElementById('scoreForm');
        form.action = `/matches/${matchId}/update-score`;
        document.getElementById('modal_home_score').value = home;
        document.getElementById('modal_away_score').value = away;
        document.getElementById('modal_stream_url').value = streamUrl;
        document.getElementById('scoreModal').classList.remove('hidden');
    }
</script>
@endsection

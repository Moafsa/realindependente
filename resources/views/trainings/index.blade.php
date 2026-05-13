@extends('layouts.dashboard')

@section('title', 'Atividades e Treinos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Agenda de Atividades</h1>
            <p class="text-gray-600">Gerencie treinos, jogos e eventos do clube</p>
        </div>
        <a href="{{ route('trainings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nova Atividade
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('trainings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Equipe</label>
                <select name="team_id" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Todas as Equipes</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo</label>
                <select name="type" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Todos os Tipos</option>
                    <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>Treino</option>
                    <option value="match" {{ request('type') == 'match' ? 'selected' : '' }}>Jogo</option>
                    <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Evento</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Data</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
            </div>
            <div class="flex items-end">
                <a href="{{ route('trainings.index') }}" class="text-sm text-gray-500 hover:text-blue-600 font-medium">Limpar Filtros</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Atividade</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data / Hora</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Equipe / Local</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($trainings as $training)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center {{ $training->type == 'training' ? 'bg-blue-100 text-blue-600' : ($training->type == 'match' ? 'bg-red-100 text-red-600' : 'bg-purple-100 text-purple-600') }}">
                                @if($training->type == 'training')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                @elseif($training->type == 'match')
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900">{{ $training->title }}</div>
                                <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ $training->type == 'training' ? 'Treino' : ($training->type == 'match' ? 'Jogo' : 'Evento') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($training->date)->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $training->time }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $training->team->name ?? 'Geral' }}</div>
                        <div class="text-xs text-gray-500">{{ $training->location ?? 'Não informado' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-tight {{ $training->status == 'scheduled' ? 'bg-blue-100 text-blue-800' : ($training->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ $training->status == 'scheduled' ? 'Agendado' : ($training->status == 'completed' ? 'Concluído' : 'Cancelado') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('trainings.edit', $training) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                            <form action="{{ route('trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta atividade?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Remover</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-lg font-medium">Nenhuma atividade encontrada</p>
                            <p class="text-sm">Clique em "Nova Atividade" para começar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $trainings->links() }}
    </div>
</div>
@endsection

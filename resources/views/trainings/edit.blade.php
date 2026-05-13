@extends('layouts.dashboard')

@section('title', 'Editar Atividade')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('trainings.index') }}" class="mr-4 p-2 rounded-full hover:bg-gray-100 transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Editar Atividade</h1>
    </div>

    <form action="{{ route('trainings.update', $training) }}" method="POST" class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-8 space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Título da Atividade</label>
                <input type="text" name="title" value="{{ old('title', $training->title) }}" required placeholder="Ex: Treino de Finalização / Jogo Amistoso"
                       class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Tipo</label>
                    <select name="type" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        <option value="training" {{ old('type', $training->type) == 'training' ? 'selected' : '' }}>Treino</option>
                        <option value="match" {{ old('type', $training->type) == 'match' ? 'selected' : '' }}>Jogo</option>
                        <option value="event" {{ old('type', $training->type) == 'event' ? 'selected' : '' }}>Evento</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Status</label>
                    <select name="status" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        <option value="scheduled" {{ old('status', $training->status) == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                        <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="cancelled" {{ old('status', $training->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Team -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Equipe (Opcional)</label>
                    <select name="team_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        <option value="">Geral (Todos os Atletas)</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id', $training->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Data</label>
                    <input type="date" name="date" value="{{ old('date', $training->date) }}" required
                           class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Time -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Hora</label>
                    <input type="time" name="time" value="{{ old('time', $training->time) }}" required
                           class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Localização</label>
                    <input type="text" name="location" value="{{ old('location', $training->location) }}" placeholder="Ex: CT Principal / Estádio Municipal"
                           class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Descrição / Notas</label>
                <textarea name="description" rows="4" placeholder="Detalhes adicionais, requisitos, etc."
                          class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">{{ old('description', $training->description) }}</textarea>
            </div>
        </div>

        <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
            <a href="{{ route('trainings.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest transition">Cancelar</a>
            <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl text-sm font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Detalhes da Atividade')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <a href="{{ route('trainings.index') }}" class="mr-4 p-2 rounded-full hover:bg-gray-100 transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detalhes da Atividade</h1>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('trainings.edit', $training) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold uppercase tracking-widest hover:bg-blue-700 transition">
                Editar
            </a>
            <form action="{{ route('trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta atividade?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold uppercase tracking-widest hover:bg-red-200 transition">
                    Remover
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center space-x-4 mb-6">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center {{ $training->type == 'training' ? 'bg-blue-100 text-blue-600' : ($training->type == 'match' ? 'bg-red-100 text-red-600' : 'bg-purple-100 text-purple-600') }}">
                        @if($training->type == 'training')
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        @elseif($training->type == 'match')
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ $training->title }}</h2>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs font-bold uppercase tracking-[0.2em] {{ $training->type == 'training' ? 'text-blue-500' : ($training->type == 'match' ? 'text-red-500' : 'text-purple-500') }}">
                                {{ $training->type == 'training' ? 'Treino' : ($training->type == 'match' ? 'Jogo' : 'Evento') }}
                            </span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $training->status == 'scheduled' ? 'Agendado' : ($training->status == 'completed' ? 'Concluído' : 'Cancelado') }}</span>
                        </div>
                    </div>
                </div>

                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                    <p class="font-bold text-gray-900 mb-2 uppercase tracking-widest text-[10px]">Descrição / Notas:</p>
                    {!! nl2br(e($training->description)) !!}
                    @if(!$training->description)
                        <p class="italic text-gray-400">Nenhuma descrição informada.</p>
                    @endif
                </div>
            </div>

            <!-- Team Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 uppercase tracking-tight italic">Equipe Vinculada</h3>
                @if($training->team)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xl">
                            {{ substr($training->team->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $training->team->name }}</p>
                            <p class="text-xs text-gray-500">{{ $training->team->category }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.teams.show', $training->team) }}" class="text-sm font-bold text-blue-600 hover:underline">Ver Equipe</a>
                </div>
                @else
                <div class="flex items-center space-x-4 text-gray-500">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="text-sm">Esta atividade é <strong>Geral</strong> e notifica todos os atletas ativos.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-gray-900 rounded-2xl p-8 text-white shadow-xl shadow-gray-200">
                <div class="space-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Data da Atividade</p>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($training->date)->translatedFormat('d \d\e F, Y') }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Horário</p>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xl font-bold">{{ $training->time }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Localização</p>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span class="text-lg font-bold leading-tight">{{ $training->location ?? 'Não informado' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-900 mb-4 uppercase tracking-tight text-xs italic">Ações Rápidas</h4>
                <div class="space-y-3">
                    @if($training->status == 'scheduled')
                    <form action="{{ route('trainings.update', $training) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="title" value="{{ $training->title }}">
                        <input type="hidden" name="type" value="{{ $training->type }}">
                        <input type="hidden" name="date" value="{{ $training->date }}">
                        <input type="hidden" name="time" value="{{ $training->time }}">
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full py-3 bg-green-50 text-green-700 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-green-100 transition">
                            Marcar como Concluído
                        </button>
                    </form>
                    @endif
                    
                    <button onclick="window.print()" class="w-full py-3 bg-gray-50 text-gray-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-100 transition">
                        Imprimir Detalhes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

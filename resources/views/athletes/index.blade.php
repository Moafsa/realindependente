@extends('layouts.dashboard')

@section('title', 'Atletas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Atletas</h1>
            <p class="text-gray-600">Gerencie todos os atletas do seu clube</p>
        </div>
        <a href="{{ route('admin.athletes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Adicionar Atleta
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                       placeholder="Nome do atleta">
            </div>
            
            <div>
                <label for="team_id" class="block text-sm font-medium text-gray-700">Equipe</label>
                <select name="team_id" id="team_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Todas as equipes</option>
                    @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700">Filial</label>
                <select name="branch_id" id="branch_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Todas as filiais</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            
            <div class="md:col-span-4 flex justify-end space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.athletes.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Athletes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($athletes as $athlete)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex items-center space-x-4">
                    <img class="h-16 w-16 rounded-full object-cover" src="{{ $athlete->profile_picture_url }}" alt="{{ $athlete->full_name }}">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $athlete->full_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $athlete->team->name ?? 'Sem equipe' }}</p>
                        <p class="text-sm text-gray-500">{{ $athlete->age }} anos • {{ $athlete->position ?? 'Posição não definida' }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $athlete->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.athletes.show', $athlete) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Ver
                        </a>
                        <a href="{{ route('admin.athletes.edit', $athlete) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            Editar
                        </a>
                    </div>
                </div>
                
                @if($athlete->jersey_number)
                <div class="mt-2">
                    <span class="text-sm text-gray-500">Camisa #{{ $athlete->jersey_number }}</span>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum atleta encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">Comece adicionando um novo atleta.</p>
            <div class="mt-6">
                <a href="{{ route('admin.athletes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Adicionar Atleta
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($athletes->hasPages())
    <div class="flex justify-center">
        {{ $athletes->links() }}
    </div>
    @endif
</div>
@endsection

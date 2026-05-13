@extends('layouts.dashboard')

@section('title', 'Filial: ' . $branch->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium text-gray-500">
                    <li class="inline-flex items-center">
                        <a href="{{ route('branches.index') }}" class="hover:text-blue-600">Filiais</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="text-gray-900">{{ $branch->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">{{ $branch->name }}</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('branches.edit', $branch) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Editar</a>
            <form action="{{ route('branches.toggle-status', $branch) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 {{ $branch->is_active ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} rounded-lg font-semibold transition">
                    {{ $branch->is_active ? 'Desativar' : 'Ativar' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Branch Details Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Informações da Unidade</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Endereço</p>
                                <p class="text-sm text-gray-700 font-medium leading-relaxed mt-1">{{ $branch->address }}</p>
                            </div>
                        </div>

                        @if($branch->phone)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Contato</p>
                                <p class="text-sm text-gray-700 font-medium mt-1">{{ $branch->phone }}</p>
                            </div>
                        </div>
                        @endif

                        @if($branch->email)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">E-mail</p>
                                <p class="text-sm text-gray-700 font-medium mt-1">{{ $branch->email }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="pt-4 border-t border-gray-50">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Status</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $branch->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $branch->is_active ? 'Unidade Ativa' : 'Unidade Inativa' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($branch->latitude && $branch->longitude)
            <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Localização</h2>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $branch->latitude }},{{ $branch->longitude }}" 
                       target="_blank" 
                       class="block w-full py-4 bg-gray-900 text-white text-center rounded-xl font-bold hover:bg-black transition flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Abrir no Google Maps
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Athletes List Table -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Atletas Vinculados</h2>
                        <p class="text-xs text-gray-500 font-medium">Lista de atletas treinando nesta unidade</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold">{{ $athletes->count() }} Atletas</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-400 text-[10px] font-black uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Atleta</th>
                                <th class="px-6 py-4">Equipe</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($athletes as $athlete)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                                            {{ substr($athlete->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $athlete->full_name }}</p>
                                            <p class="text-xs text-gray-400 font-medium">ID #{{ $athlete->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($athlete->team)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold">{{ $athlete->team->name }}</span>
                                    @else
                                    <span class="text-xs text-gray-400 italic">Sem Equipe</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold {{ $athlete->is_active ? 'text-green-500' : 'text-red-500' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $athlete->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $athlete->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('athletes.show', $athlete) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-wider">Ver Perfil</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <p class="text-sm font-bold uppercase tracking-widest">Nenhum atleta encontrado</p>
                                        <p class="text-xs mt-1">Esta unidade ainda não possui atletas vinculados.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

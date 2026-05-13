@extends('layouts.admin')

@section('title', 'Gestão de Atletas')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Atletas do Clube</h1>
            <p class="text-gray-400 mt-2">Gerencie matrículas, categorias e desempenho técnico.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.athletes.create') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 hover:-translate-y-1 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Matricular Atleta
            </a>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-[#0F1423] p-8 rounded-[2rem] shadow-2xl border border-white/5 mb-10">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-6">
            <div class="lg:col-span-1">
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-2">Busca Rápida</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 outline-none transition-all" 
                       placeholder="Nome do atleta...">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-2">Equipe</label>
                <select name="team_id" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all appearance-none">
                    <option value="" class="bg-[#0F1423]">Todas</option>
                    @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }} class="bg-[#0F1423]">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-2">Unidade</label>
                <select name="branch_id" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all appearance-none">
                    <option value="" class="bg-[#0F1423]">Todas</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }} class="bg-[#0F1423]">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 ml-2">Categoria</label>
                <select name="subcategory" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all appearance-none">
                    <option value="" class="bg-[#0F1423]">Todas</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('subcategory') === $category ? 'selected' : '' }} class="bg-[#0F1423]">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-4 bg-white/5 text-white border border-white/10 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-white/10 transition-all">
                    Filtrar
                </button>
                <a href="{{ route('admin.athletes.index') }}" class="p-4 bg-rose-500/10 text-rose-500 rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Athletes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($athletes as $athlete)
        <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden group hover:border-indigo-500/30 transition-all duration-500 relative">
            @if($athlete->pending_requests_count > 0)
                <div class="absolute top-6 right-6 z-10 px-3 py-1 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full animate-pulse shadow-lg shadow-rose-500/20">
                    {{ $athlete->pending_requests_count }} Pendente
                </div>
            @endif

            <div class="p-8">
                <div class="flex items-center gap-6 mb-8">
                    <div class="relative">
                        <img class="w-20 h-20 rounded-[1.5rem] object-cover ring-4 ring-white/5 group-hover:ring-indigo-500 transition-all duration-500" src="{{ $athlete->profile_picture_url }}" alt="{{ $athlete->full_name }}">
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-[#0F1423] rounded-xl flex items-center justify-center border border-white/10">
                            <span class="text-[10px] font-black text-indigo-400">#{{ $athlete->jersey_number ?? '?' }}</span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <h3 class="text-xl font-black text-white truncate leading-tight group-hover:text-indigo-400 transition-colors">
                            {{ $athlete->full_name }}
                        </h3>
                        <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">{{ $athlete->team->name ?? 'Sem Equipe Definida' }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Idade</p>
                        <p class="text-sm font-bold text-gray-200">{{ $athlete->age }} anos</p>
                    </div>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Posição</p>
                        <p class="text-sm font-bold text-gray-200 truncate">{{ $athlete->position ?? 'N/D' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-6 border-t border-white/5">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $athlete->is_active ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                        {{ $athlete->is_active ? 'Regular' : 'Inativo' }}
                    </span>
                    
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.athletes.show', $athlete) }}" class="p-3 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl transition-all" title="Ver Perfil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                        <a href="{{ route('admin.athletes.edit', $athlete) }}" class="p-3 bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white rounded-xl transition-all" title="Editar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        
                        @if(auth()->user()->role === 'admin')
                        <form action="{{ route('admin.athletes.destroy', $athlete) }}" method="POST" class="inline" onsubmit="return confirm('Excluir atleta permanentemente?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-all" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-32 text-center bg-[#0F1423] rounded-[3rem] border-2 border-dashed border-white/5">
            <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center text-gray-700 mx-auto mb-6">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-black text-white uppercase tracking-widest">Nenhum atleta encontrado</h3>
            <p class="text-gray-500 mt-2 font-bold uppercase tracking-tighter text-xs">Tente ajustar seus filtros ou cadastre um novo talento.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($athletes->hasPages())
    <div class="mt-12">
        {{ $athletes->links() }}
    </div>
    @endif
</div>
@endsection

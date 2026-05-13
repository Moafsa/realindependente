@extends('layouts.admin')

@section('title', 'Planos & Preços')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Planos & Preços</h1>
            <p class="text-gray-600 mt-2">Gerencie os níveis de assinatura e limites do sistema.</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
            Criar Novo Plano
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 transition-all hover:shadow-2xl hover:-translate-y-1">
            <div class="p-8 border-b border-gray-50 bg-gray-50/50">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-black text-gray-900">{{ $plan->name }}</h2>
                        <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-indigo-600">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">por mês</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $plan->description }}</p>
            </div>
            
            <div class="p-8 space-y-4">
                <div class="flex items-center text-sm font-bold text-gray-700">
                    <svg class="w-5 h-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Até {{ $plan->max_athletes }} Atletas
                </div>
                <div class="flex items-center text-sm font-bold text-gray-700">
                    <svg class="w-5 h-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Até {{ $plan->max_branches }} Unidades
                </div>
                <div class="flex items-center text-sm font-bold text-gray-700">
                    <svg class="w-5 h-5 {{ $plan->ai_features ? 'text-indigo-500' : 'text-gray-300' }} mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    Acesso a IA: {{ $plan->ai_features ? 'Sim' : 'Não' }}
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50 flex gap-3">
                <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 py-3 bg-white border border-gray-200 text-center text-sm font-bold text-gray-700 rounded-xl hover:bg-gray-50 transition-all">Editar</a>
                <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="flex-1" onsubmit="return confirm('Tem certeza?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-rose-50 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-100 transition-all">Excluir</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

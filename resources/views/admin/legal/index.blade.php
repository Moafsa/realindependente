@extends('layouts.admin')

@section('title', 'Configurações Legais Globais')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Configurações Legais</h1>
        <p class="text-gray-600 mt-2">Gerencie os termos de uso e apólices de seguro que aparecem em todos os clubes.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.legal.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Termos de Uso e Regulamento
                </h2>
                <p class="text-sm text-gray-500 mt-1">Este texto aparecerá no formulário de matrícula de todos os clubes.</p>
            </div>
            <div class="p-6">
                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs text-blue-700 font-medium uppercase tracking-wider mb-2">Variáveis Disponíveis:</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-white border border-blue-200 rounded text-xs font-mono text-blue-600">{school_name}</span>
                        <span class="px-2 py-1 bg-white border border-blue-200 rounded text-xs font-mono text-blue-600">{school_email}</span>
                        <span class="px-2 py-1 bg-white border border-blue-200 rounded text-xs font-mono text-blue-600">{school_phone}</span>
                        <span class="px-2 py-1 bg-white border border-blue-200 rounded text-xs font-mono text-blue-600">{school_address}</span>
                    </div>
                </div>
                <textarea name="global_terms_of_use" rows="15" 
                          class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none text-gray-700 font-sans"
                          placeholder="Digite os termos de uso aqui...">{{ old('global_terms_of_use', $terms) }}</textarea>
                @error('global_terms_of_use')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Apólice de Seguro Atleta
                </h2>
                <p class="text-sm text-gray-500 mt-1">Detalhes sobre a cobertura de seguro que os atletas possuem.</p>
            </div>
            <div class="p-6">
                <textarea name="global_insurance_policy" rows="10" 
                          class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all outline-none text-gray-700 font-sans"
                          placeholder="Digite os detalhes da apólice aqui...">{{ old('global_insurance_policy', $insurance) }}</textarea>
                @error('global_insurance_policy')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-bold text-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all hover:-translate-y-1">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>
@endsection

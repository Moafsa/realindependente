@extends('layouts.admin')

@section('title', 'Configurações Globais')

@section('content')
<div class="max-w-4xl">
    <div class="mb-10">
        <h1 class="text-4xl font-black text-white tracking-tight">Configurações do Sistema</h1>
        <p class="text-gray-500 mt-2 font-medium">Gerencie integrações, IA e credenciais globais da plataforma.</p>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3 text-emerald-400 animate__animated animate__fadeIn">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- AI Configuration -->
        <div class="bg-white/[0.02] border border-white/5 rounded-[2.5rem] p-8 backdrop-blur-xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white">Inteligência Artificial</h2>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">Geração de Planos, Dietas e Análise de Fotos</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">OpenAI API Key</label>
                    <input type="password" name="openai_api_key" value="{{ old('openai_api_key', $openai_api_key) }}" 
                           class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all font-mono text-sm"
                           placeholder="sk-...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Modelo Principal</label>
                    <select name="openai_model" class="w-full px-6 py-4 bg-[#0F1423] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all appearance-none cursor-pointer">
                        <option value="gpt-4o" {{ $openai_model == 'gpt-4o' ? 'selected' : '' }}>GPT-4o (Recomendado)</option>
                        <option value="gpt-4o-mini" {{ $openai_model == 'gpt-4o-mini' ? 'selected' : '' }}>GPT-4o Mini (Rápido/Econômico)</option>
                        <option value="o1-preview" {{ $openai_model == 'o1-preview' ? 'selected' : '' }}>o1 Preview (Lógica Avançada)</option>
                        <option value="gpt-4-turbo" {{ $openai_model == 'gpt-4-turbo' ? 'selected' : '' }}>GPT-4 Turbo</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Base URL (Opcional)</label>
                <input type="text" name="openai_base_url" value="{{ old('openai_base_url', $openai_base_url) }}" 
                       class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all text-sm"
                       placeholder="https://api.openai.com/v1">
            </div>
        </div>


        <!-- Asaas Gateway -->
        <div class="bg-white/[0.02] border border-white/5 rounded-[2.5rem] p-8 backdrop-blur-xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white">Gateway de Pagamento (Asaas)</h2>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">Cobrança de Assinaturas dos Clubes (Super Admin)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Asaas API Key</label>
                    <input type="password" name="asaas_api_key" value="{{ old('asaas_api_key', $asaas_api_key) }}" 
                           class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all font-mono text-sm"
                           placeholder="$... (Sua chave secreta)">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Ambiente</label>
                    <select name="asaas_environment" class="w-full px-6 py-4 bg-[#0F1423] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all appearance-none cursor-pointer">
                        <option value="sandbox" {{ $asaas_environment == 'sandbox' ? 'selected' : '' }}>Sandbox (Testes)</option>
                        <option value="production" {{ $asaas_environment == 'production' ? 'selected' : '' }}>Produção (Real)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Wallet ID (Split)</label>
                    <input type="text" name="asaas_wallet_id" value="{{ old('asaas_wallet_id', $asaas_wallet_id) }}" 
                           class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all text-sm font-mono"
                           placeholder="00000000-0000-0000-0000-000000000000">
                </div>
            </div>
        </div>

        <!-- Geo & Analytics -->
        <div class="bg-white/[0.02] border border-white/5 rounded-[2.5rem] p-8 backdrop-blur-xl">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white">Mapas & Analytics</h2>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-widest mt-1">Geolocalização e Rastreamento</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Mapbox Public Token (pk.xxx)</label>
                    <input type="text" name="mapbox_public_token" value="{{ old('mapbox_public_token', $mapbox_token) }}" 
                           class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all text-sm font-mono"
                           placeholder="pk.ey...">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Google Analytics G-ID</label>
                    <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $google_analytics_id) }}" 
                           class="w-full px-6 py-4 bg-white/[0.03] border border-white/5 rounded-2xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all text-sm"
                           placeholder="G-XXXXXXXXXX">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pb-12">
            <button type="submit" class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-600/20 transition-all hover:-translate-y-1">
                Salvar Configurações
            </button>
        </div>
    </form>
</div>
@endsection

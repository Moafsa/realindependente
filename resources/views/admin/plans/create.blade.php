@extends('layouts.admin')

@section('title', 'Criar Novo Plano')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('admin.plans.index') }}" class="p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:bg-gray-50 transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Novo Plano</h1>
            <p class="text-gray-600 mt-1">Defina as regras e limites da nova assinatura.</p>
        </div>
    </div>

    <form action="{{ route('admin.plans.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Informações Básicas</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nome do Plano</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Descrição</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">{{ old('description') }}</textarea>
                    </div>
            <div class="p-8 border-b border-gray-50">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Preços & Descontos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Mensal (R$)</label>
                        <input type="number" step="0.01" name="price_monthly" value="{{ old('price_monthly') }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Trimestral (R$)</label>
                        <input type="number" step="0.01" name="price_quarterly" value="{{ old('price_quarterly') }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Opcional">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Semestral (R$)</label>
                        <input type="number" step="0.01" name="price_semiannual" value="{{ old('price_semiannual') }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Opcional">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Anual (R$)</label>
                        <input type="number" step="0.01" name="price_yearly" value="{{ old('price_yearly') }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Opcional">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Desconto Trimestral (%)</label>
                        <input type="number" name="discount_quarterly" value="{{ old('discount_quarterly', 0) }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Desconto Semestral (%)</label>
                        <input type="number" name="discount_semiannual" value="{{ old('discount_semiannual', 0) }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Desconto Anual (%)</label>
                        <input type="number" name="discount_yearly" value="{{ old('discount_yearly', 0) }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Taxa Administrativa (%)</label>
                        <input type="number" step="0.01" name="admin_fee_percentage" value="{{ old('admin_fee_percentage', 0) }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <p class="text-[10px] text-gray-500 mt-1">Percentual cobrado sobre tudo o que o clube vende (Split Asaas).</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Taxa E-commerce (%)</label>
                        <input type="number" step="0.01" name="ecommerce_tax_rate" value="{{ old('ecommerce_tax_rate', 0) }}" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <p class="text-[10px] text-gray-500 mt-1">Percentual adicional para vendas na loja do clube.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 border-b border-gray-50 bg-gray-50/30">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Limites & Recursos</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Limite de Atletas</label>
                        <input type="number" name="max_athletes" value="{{ old('max_athletes', 0) }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Limite de Unidades/Filiais</label>
                        <input type="number" name="max_branches" value="{{ old('max_branches', 1) }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Recursos (Separados por vírgula)</label>
                        <input type="text" name="features_raw" value="{{ old('features_raw') }}" placeholder="Ex: Suporte 24h, Relatórios PDF, Treinos IA" class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl">
                    <div>
                        <p class="font-bold text-gray-900">Acesso a Inteligência Artificial</p>
                        <p class="text-xs text-gray-500">Habilita a geração de treinos e dietas via IA.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="ai_features" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Dias de Trial (Teste)</label>
                        <input type="number" name="trial_days" value="{{ old('trial_days', 7) }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ordem de Exibição</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer p-4 bg-gray-50 rounded-2xl w-full">
                            <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-indigo-600 rounded-lg border-gray-300 focus:ring-indigo-500" checked>
                            <span class="font-bold text-gray-900">Plano Ativo</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all hover:-translate-y-1">
                Criar Plano
            </button>
        </div>
    </form>
</div>
@endsection

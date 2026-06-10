@extends('layouts.dashboard')

@section('title', 'Novo Plano de Assinatura')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Novo Plano de Assinatura</h1>
    </div>

    <form action="{{ route('admin.subscription-plans.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white shadow rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nome do Plano <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Ex: Plano Mensal Básico">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Descrição / Benefícios</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Liste o que está incluso no plano...">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-2">Valor da Mensalidade (R$) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                           placeholder="0,00">
                    @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="setup_fee" class="block text-sm font-bold text-gray-700 mb-2">Taxa de Inscrição (R$)</label>
                    <input type="number" step="0.01" name="setup_fee" id="setup_fee" value="{{ old('setup_fee', '0.00') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('setup_fee') border-red-500 @enderror"
                           placeholder="Ex: 50,00">
                    <p class="text-xs text-gray-500 mt-1">Este valor será somado apenas à primeira fatura.</p>
                    @error('setup_fee') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cycle" class="block text-sm font-bold text-gray-700 mb-2">Ciclo de Cobrança <span class="text-red-500">*</span></label>
                    <select name="cycle" id="cycle" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cycle') border-red-500 @enderror">
                        <option value="MONTHLY" {{ old('cycle') == 'MONTHLY' ? 'selected' : '' }}>Mensal</option>
                        <option value="QUARTERLY" {{ old('cycle') == 'QUARTERLY' ? 'selected' : '' }}>Trimestral</option>
                        <option value="SEMIANNUALLY" {{ old('cycle') == 'SEMIANNUALLY' ? 'selected' : '' }}>Semestral</option>
                        <option value="YEARLY" {{ old('cycle') == 'YEARLY' ? 'selected' : '' }}>Anual</option>
                    </select>
                    @error('cycle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-bold text-blue-900 uppercase tracking-widest mb-4">Benefícios do Plano (Checklist)</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[insurance]" value="1" class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Seguro Atleta</span>
                            </label>
                            <div class="flex flex-col space-y-2 p-3 bg-white rounded-xl border border-blue-200 hover:bg-blue-100 transition">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="features[evaluation]" value="1" class="w-5 h-5 text-blue-600 rounded" onclick="document.getElementById('eval_freq_container').classList.toggle('hidden', !this.checked)">
                                    <span class="text-sm font-medium text-gray-700">Avaliação Técnica/Física</span>
                                </label>
                                <div id="eval_freq_container" class="hidden pl-8">
                                    <select name="evaluation_frequency" class="w-full text-sm px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                        <option value="">Selecione a frequência...</option>
                                        <option value="Por Treino">Por Treino</option>
                                        <option value="Semanal">Semanal</option>
                                        <option value="Quinzenal">Quinzenal</option>
                                        <option value="Mensal">Mensal</option>
                                        <option value="Bimestral">Bimestral</option>
                                        <option value="Semestral">Semestral</option>
                                        <option value="Anual">Anual</option>
                                    </select>
                                </div>
                            </div>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[training_plan]" value="1" class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Plano de Treino Personalizado</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[diet_plan]" value="1" class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Dieta Personalizada</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[whatsapp_support]" value="1" class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Suporte via WhatsApp</span>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-blue-200">
                        <h3 class="text-sm font-bold text-blue-900 uppercase tracking-widest mb-4">Cronograma e Detalhes</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Dias de Treino / Semana</label>
                        <input type="number" name="training[days]" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Carga Horária (Ex: 1h30)</label>
                        <input type="text" name="training[hours]" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 1h por dia">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Uniforme / Kit</label>
                        <input type="text" name="training[uniform]" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Incluso Camiseta + Calção">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Outros Detalhes</label>
                        <input type="text" name="training[other]" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Treinos noturnos">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Destacar este plano?</p>
                            <p class="text-xs text-gray-500">Plano será exibido com destaque na vitrine pública.</p>
                        </div>
                    </label>
                </div>

                <input type="hidden" name="is_active" value="1">
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.subscription-plans.index') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                Criar Plano de Assinatura
            </button>
        </div>
    </form>
</div>
@endsection

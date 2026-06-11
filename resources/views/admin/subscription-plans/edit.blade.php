@extends('layouts.dashboard')

@section('title', 'Editar Plano: ' . $plan->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('admin.subscription-plans.index') }}" class="text-gray-500 hover:text-gray-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Editar Plano</h1>
    </div>

    <form action="{{ route('admin.subscription-plans.update', $plan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white shadow rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nome do Plano <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Descrição / Benefícios</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $plan->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 mb-2">Valor da Mensalidade (R$) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $plan->price) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror">
                    @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="setup_fee" class="block text-sm font-bold text-gray-700 mb-2">Taxa de Inscrição (R$)</label>
                    <input type="number" step="0.01" name="setup_fee" id="setup_fee" value="{{ old('setup_fee', $plan->attributes['setup_fee'] ?? '0.00') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('setup_fee') border-red-500 @enderror"
                           placeholder="Ex: 50,00">
                    <p class="text-xs text-gray-500 mt-1">Este valor será somado apenas à primeira fatura.</p>
                    @error('setup_fee') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="cycle" class="block text-sm font-bold text-gray-700 mb-2">Ciclo de Cobrança Padrão <span class="text-red-500">*</span></label>
                    <select name="cycle" id="cycle" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cycle') border-red-500 @enderror">
                        @php $currentCycle = old('cycle', $plan->attributes['cycle'] ?? 'MONTHLY'); @endphp
                        <option value="MONTHLY" {{ $currentCycle == 'MONTHLY' ? 'selected' : '' }}>Mensal</option>
                        <option value="QUARTERLY" {{ $currentCycle == 'QUARTERLY' ? 'selected' : '' }}>Trimestral</option>
                        <option value="SEMIANNUALLY" {{ $currentCycle == 'SEMIANNUALLY' ? 'selected' : '' }}>Semestral</option>
                        <option value="YEARLY" {{ $currentCycle == 'YEARLY' ? 'selected' : '' }}>Anual</option>
                    </select>
                    @error('cycle') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <div class="sm:col-span-3">
                        <h3 class="text-sm font-bold text-gray-700">Descontos por Frequência (%)</h3>
                        <p class="text-xs text-gray-500 mb-2">Permita que o atleta escolha uma frequência diferente e ganhe desconto. Deixe 0 para desativar a frequência.</p>
                    </div>
                    <div>
                        <label for="discount_quarterly" class="block text-xs font-bold text-gray-700 mb-1">Trimestral (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_quarterly" id="discount_quarterly" value="{{ old('discount_quarterly', $plan->attributes['discount_quarterly'] ?? '0') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="discount_semiannually" class="block text-xs font-bold text-gray-700 mb-1">Semestral (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_semiannually" id="discount_semiannually" value="{{ old('discount_semiannually', $plan->attributes['discount_semiannually'] ?? '0') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="discount_yearly" class="block text-xs font-bold text-gray-700 mb-1">Anual (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="discount_yearly" id="discount_yearly" value="{{ old('discount_yearly', $plan->attributes['discount_yearly'] ?? '0') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-blue-50 rounded-2xl border border-blue-100">
                    @php
                        $features = $plan->attributes['features'] ?? [];
                        $training = $plan->attributes['training_details'] ?? [];
                    @endphp
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-bold text-blue-900 uppercase tracking-widest mb-4">Benefícios do Plano (Checklist)</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[insurance]" value="1" {{ ($features['insurance'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Seguro Atleta</span>
                            </label>
                            <div class="flex flex-col space-y-2 p-3 bg-white rounded-xl border border-blue-200 hover:bg-blue-100 transition">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" name="features[evaluation]" value="1" {{ ($features['evaluation'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded" onclick="document.getElementById('eval_freq_container').classList.toggle('hidden', !this.checked)">
                                    <span class="text-sm font-medium text-gray-700">Avaliação Técnica/Física</span>
                                </label>
                                <div id="eval_freq_container" class="{{ ($features['evaluation'] ?? false) ? '' : 'hidden' }} pl-8">
                                    <select name="evaluation_frequency" class="w-full text-sm px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                        <option value="">Selecione a frequência...</option>
                                        @php $currentFreq = old('evaluation_frequency', $plan->attributes['evaluation_frequency'] ?? ''); @endphp
                                        <option value="Por Treino" {{ $currentFreq == 'Por Treino' ? 'selected' : '' }}>Por Treino</option>
                                        <option value="Semanal" {{ $currentFreq == 'Semanal' ? 'selected' : '' }}>Semanal</option>
                                        <option value="Quinzenal" {{ $currentFreq == 'Quinzenal' ? 'selected' : '' }}>Quinzenal</option>
                                        <option value="Mensal" {{ $currentFreq == 'Mensal' ? 'selected' : '' }}>Mensal</option>
                                        <option value="Bimestral" {{ $currentFreq == 'Bimestral' ? 'selected' : '' }}>Bimestral</option>
                                        <option value="Semestral" {{ $currentFreq == 'Semestral' ? 'selected' : '' }}>Semestral</option>
                                        <option value="Anual" {{ $currentFreq == 'Anual' ? 'selected' : '' }}>Anual</option>
                                    </select>
                                </div>
                            </div>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[training_plan]" value="1" {{ ($features['training_plan'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Plano de Treino Personalizado</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[diet_plan]" value="1" {{ ($features['diet_plan'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Dieta Personalizada</span>
                            </label>
                            <label class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-blue-200 cursor-pointer hover:bg-blue-100 transition">
                                <input type="checkbox" name="features[whatsapp_support]" value="1" {{ ($features['whatsapp_support'] ?? false) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded">
                                <span class="text-sm font-medium text-gray-700">Suporte via WhatsApp</span>
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-blue-200">
                        <h3 class="text-sm font-bold text-blue-900 uppercase tracking-widest mb-4">Cronograma e Detalhes</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Dias de Treino / Semana</label>
                        <input type="number" name="training[days]" value="{{ $training['days_per_week'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 3">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Carga Horária (Ex: 1h30)</label>
                        <input type="text" name="training[hours]" value="{{ $training['hours_per_day'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: 1h por dia">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Uniforme / Kit</label>
                        <input type="text" name="training[uniform]" value="{{ $training['uniform'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Incluso Camiseta + Calção">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Outros Detalhes</label>
                        <input type="text" name="training[other]" value="{{ $training['other_details'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ex: Treinos noturnos">
                    </div>
                </div>

                <div>
                    <label class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Ativo?</p>
                            <p class="text-xs text-gray-500">Se desativado, o plano não aparece no site.</p>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div>
                            <p class="text-sm font-bold text-gray-900">Destacar este plano?</p>
                            <p class="text-xs text-gray-500">Plano será exibido com destaque na vitrine pública.</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.subscription-plans.index') }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection

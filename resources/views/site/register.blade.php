@extends('layouts.site')

@section('title', 'Matricule-se')
@section('description', 'Crie seu perfil de atleta e comece sua jornada conosco.')

@section('content')
<div class="min-h-screen py-20 bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-3 bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        
        <!-- Left Side: Branding/Progress (1 column) -->
        <div class="bg-primary p-12 text-white flex flex-col justify-between relative overflow-hidden hidden lg:flex">
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-md flex items-center justify-center mb-8">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-6">Cadastro de Atleta</h1>
                
                <!-- Steps Indicator -->
                <div class="space-y-8 mt-12">
                    <div class="flex items-center gap-4 step-indicator active" data-step="1">
                        <div class="w-10 h-10 rounded-full border-2 border-white/50 flex items-center justify-center font-bold text-sm transition-all">1</div>
                        <div>
                            <p class="font-bold">Dados Pessoais</p>
                            <p class="text-xs text-white/60">Nome, CPF e Contato</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 step-indicator" data-step="2">
                        <div class="w-10 h-10 rounded-full border-2 border-white/20 flex items-center justify-center font-bold text-sm transition-all">2</div>
                        <div>
                            <p class="font-bold">Físico e Saúde</p>
                            <p class="text-xs text-white/60">Peso, altura e alergias</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 step-indicator" data-step="3">
                        <div class="w-10 h-10 rounded-full border-2 border-white/20 flex items-center justify-center font-bold text-sm transition-all">3</div>
                        <div>
                            <p class="font-bold">Responsável</p>
                            <p class="text-xs text-white/60">Dados para faturamento</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 step-indicator" data-step="4">
                        <div class="w-10 h-10 rounded-full border-2 border-white/20 flex items-center justify-center font-bold text-sm transition-all">4</div>
                        <div>
                            <p class="font-bold">Acesso e Termos</p>
                            <p class="text-xs text-white/60">Senha e regulamentos</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="relative z-10 p-6 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                <p class="text-sm font-medium leading-relaxed">
                    <i class="fas fa-shield-alt mr-2"></i> Todos os atletas inscritos e com mensalidade em dia possuem cobertura de seguro pela nossa plataforma.
                </p>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Right Side: Form (2 columns) -->
        <div class="lg:col-span-2 p-8 md:p-12">
            <div class="mb-10 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Seja bem-vindo!</h2>
                    <p class="text-gray-500 font-medium" id="step-title">Preencha seus dados básicos.</p>
                </div>
                <div class="lg:hidden">
                    <span class="text-primary font-bold" id="mobile-step-counter">Passo 1 de 4</span>
                </div>
            </div>

            <form id="registration-form" action="{{ route('site.register.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                @if(isset($plan))
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <div class="mb-8 p-6 bg-blue-50 border border-blue-100 rounded-3xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Plano Selecionado</p>
                        <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500">R$ {{ number_format($plan->price, 2, ',', '.') }} / 
                            @php
                                $cycleLabels = [
                                    'MONTHLY' => 'mensal',
                                    'QUARTERLY' => 'trimestral',
                                    'SEMIANNUALLY' => 'semestral',
                                    'YEARLY' => 'anual'
                                ];
                                echo $cycleLabels[$plan->attributes['cycle'] ?? 'MONTHLY'] ?? 'mensal';
                            @endphp
                        </p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-2xl">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                @endif
                
                @if($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl mb-6">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl mb-6">
                    <p class="text-sm text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</p>
                </div>
                @endif

                <!-- Step 1: Personal Info -->
                <div class="step-content block" data-step="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nome Completo do Atleta</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="Nome completo">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">CPF do Atleta</label>
                            <input type="text" name="document" value="{{ old('document') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium cpf-mask" 
                                   placeholder="000.000.000-00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Gênero</label>
                            <select name="gender" class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium" required>
                                <option value="">Selecione...</option>
                                <option value="masculino" {{ old('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="feminino" {{ old('gender') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Data de Nascimento</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" required
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">WhatsApp/Fone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium phone-mask" 
                                   placeholder="(00) 00000-0000">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Endereço Completo</label>
                            <input type="text" name="address" value="{{ old('address') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="Rua, número, bairro, cidade - UF">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Physical & Health -->
                <div class="step-content hidden" data-step="2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Altura (m)</label>
                            <input type="number" step="0.01" name="height" value="{{ old('height') }}" 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="1.75">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Peso (kg)</label>
                            <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="70.5">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Posições de Atuação (Selecione múltiplas)</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                                @php
                                    $allPositions = [
                                        'Goleiro', 'Lateral Direito', 'Lateral Esquerdo', 
                                        'Zagueiro', 'Volante', 'Meia Central', 
                                        'Meia Atacante', 'Ponta Direita', 'Ponta Esquerda', 'Centroavante'
                                    ];
                                @endphp
                                @foreach($allPositions as $pos)
                                <label class="flex items-center p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-all border border-transparent has-[:checked]:border-primary has-[:checked]:bg-blue-50">
                                    <input type="checkbox" name="positions[]" value="{{ $pos }}" class="hidden" {{ is_array(old('positions')) && in_array($pos, old('positions')) ? 'checked' : '' }}>
                                    <span class="text-sm font-semibold text-gray-700">{{ $pos }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Alergias (Opcional)</label>
                            <input type="text" name="allergies" value="{{ old('allergies') }}" 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="Ex: Amendoim, Lactose, Dipirona...">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Condições Médicas (Opcional)</label>
                            <textarea name="medical_conditions" rows="2" 
                                      class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                      placeholder="Ex: Asma, Diabetes... (Separe por vírgula)">{{ old('medical_conditions') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Atestado Médico (Opcional - PDF ou Imagem)</label>
                            
                            @if($errors->any())
                            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Por segurança, se você já havia selecionado um arquivo de atestado, por favor <strong>selecione-o novamente</strong>.
                            </div>
                            @endif

                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-primary transition-colors bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="medical_certificate" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none">
                                            <span>Fazer upload do arquivo</span>
                                            <input id="medical_certificate" name="medical_certificate" type="file" class="sr-only" accept=".pdf,image/*">
                                        </label>
                                        <p class="pl-1">ou arraste e solte</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG até 5MB</p>
                                    <p id="file-name" class="text-xs text-primary font-bold mt-2 hidden"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Guardian -->
                <div class="step-content hidden" data-step="3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nome do Responsável Financeiro</label>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="Nome completo do pai, mãe ou tutor">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">CPF do Responsável</label>
                            <input type="text" name="guardian_document" value="{{ old('guardian_document') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium cpf-mask" 
                                   placeholder="000.000.000-00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">WhatsApp do Responsável</label>
                            <input type="text" name="guardian_contact" value="{{ old('guardian_contact') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium phone-mask" 
                                   placeholder="(00) 00000-0000">
                        </div>
                        <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i> As faturas das mensalidades serão geradas em nome deste responsável.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Auth & Terms -->
                <div class="step-content hidden" data-step="4">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">E-mail para Acesso</label>
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                   placeholder="exemplo@email.com">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Senha</label>
                                <input type="password" name="password" required 
                                       class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                       placeholder="••••••••">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Confirme a Senha</label>
                                <input type="password" name="password_confirmation" required 
                                       class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-primary transition-all outline-none text-gray-900 font-medium" 
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="space-y-4 pt-4">
                            <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all border border-transparent has-[:checked]:border-primary">
                                <input type="checkbox" name="terms_accepted" value="1" required class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary" {{ old('terms_accepted') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600 leading-relaxed">
                                    Li e concordo com os <a href="javascript:void(0)" onclick="openModal('terms-modal')" class="text-primary font-bold hover:underline">Termos de Uso</a> e as regras de convivência da plataforma.
                                </span>
                            </label>
                            <label class="flex items-start gap-3 p-4 bg-blue-50/50 rounded-2xl cursor-pointer hover:bg-blue-50 transition-all border border-transparent has-[:checked]:border-primary">
                                <input type="checkbox" name="insurance_accepted" value="1" required class="mt-1 w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary" {{ old('insurance_accepted') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600 leading-relaxed">
                                    Estou ciente da cobertura de <a href="javascript:void(0)" onclick="openModal('insurance-modal')" class="text-primary font-bold hover:underline">Seguro Atleta</a> fornecida para inscritos em dia com a mensalidade.
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modals -->
                <div id="terms-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col shadow-2xl">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">Termos de Uso</h3>
                            <button type="button" onclick="closeModal('terms-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-8 overflow-y-auto prose prose-blue max-w-none text-gray-600 whitespace-pre-wrap">{{ $legal['terms'] }}</div>
                        <div class="p-6 border-t border-gray-100 flex justify-end">
                            <button type="button" onclick="closeModal('terms-modal')" class="px-6 py-2 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all">Entendi</button>
                        </div>
                    </div>
                </div>

                <div id="insurance-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col shadow-2xl">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-blue-50">
                            <h3 class="text-xl font-bold text-gray-900">Apólice de Seguro</h3>
                            <button type="button" onclick="closeModal('insurance-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="p-8 overflow-y-auto prose prose-blue max-w-none text-gray-600 whitespace-pre-wrap">{{ $legal['insurance'] }}</div>
                        <div class="p-6 border-t border-gray-100 flex justify-end">
                            <button type="button" onclick="closeModal('insurance-modal')" class="px-6 py-2 bg-primary text-white rounded-xl font-bold hover:bg-primary-dark transition-all">Entendi</button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="pt-8 flex flex-col md:flex-row gap-4">
                    <button type="button" id="prev-btn" class="hidden md:flex flex-1 items-center justify-center bg-gray-100 text-gray-600 py-4 px-8 rounded-2xl font-bold text-lg hover:bg-gray-200 transition-all">
                        Anterior
                    </button>
                    <button type="button" id="next-btn" class="flex-[2] bg-primary text-white py-4 px-8 rounded-2xl font-bold text-lg hover:bg-primary-dark shadow-lg shadow-blue-200 transition-all hover:-translate-y-1">
                        Próximo Passo
                    </button>
                    <button type="submit" id="submit-btn" class="hidden flex-[2] bg-green-600 text-white py-4 px-8 rounded-2xl font-bold text-lg hover:bg-green-700 shadow-lg shadow-green-200 transition-all hover:-translate-y-1">
                        Finalizar Matrícula
                    </button>
                </div>

                <p class="mt-6 text-center text-sm text-gray-500 font-medium">
                    Já tem uma conta? 
                    <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Fazer login</a>
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Masks
        $('.cpf-mask').mask('000.000.000-00');
        $('.phone-mask').mask('(00) 00000-0000');

        let currentStep = 1;
        const totalSteps = 4;

        @if($errors->any())
            @if($errors->hasAny(['full_name', 'document', 'birth_date', 'phone', 'address']))
                currentStep = 1;
            @elseif($errors->hasAny(['height', 'weight', 'positions', 'medical_conditions', 'medical_certificate']))
                currentStep = 2;
            @elseif($errors->hasAny(['guardian_name', 'guardian_document', 'guardian_contact']))
                currentStep = 3;
            @else
                currentStep = 4;
            @endif
        @endif

        function updateStep(step) {
            // Update Visibility
            $('.step-content').addClass('hidden').removeClass('block');
            $(`.step-content[data-step="${step}"]`).removeClass('hidden').addClass('block');

            // Update Indicators
            $('.step-indicator').removeClass('active');
            $(`.step-indicator[data-step="${step}"]`).addClass('active');
            
            // Highlight previous steps
            $('.step-indicator').each(function() {
                const s = $(this).data('step');
                const circle = $(this).find('div:first');
                if (s < step) {
                    circle.addClass('bg-white text-primary border-white').removeClass('border-white/50 border-white/20');
                    circle.html('<i class="fas fa-check"></i>');
                } else if (s === step) {
                    circle.removeClass('bg-white text-primary border-white').addClass('border-white/50');
                    circle.html(s);
                } else {
                    circle.removeClass('bg-white text-primary border-white border-white/50').addClass('border-white/20');
                    circle.html(s);
                }
            });

            // Update Buttons
            if (step === 1) {
                $('#prev-btn').addClass('hidden');
            } else {
                $('#prev-btn').removeClass('hidden');
            }

            if (step === totalSteps) {
                $('#next-btn').addClass('hidden');
                $('#submit-btn').removeClass('hidden');
                $('#step-title').text('Acesso e Finalização.');
            } else {
                $('#next-btn').removeClass('hidden');
                $('#submit-btn').addClass('hidden');
                
                const titles = {
                    1: 'Preencha seus dados básicos.',
                    2: 'Informações físicas e de saúde.',
                    3: 'Dados do responsável financeiro.'
                };
                $('#step-title').text(titles[step]);
            }

            $('#mobile-step-counter').text(`Passo ${step} de ${totalSteps}`);
        }

        // Initialize view
        updateStep(currentStep);

        $('#next-btn').click(function() {
            // Basic validation for current step
            const currentInputs = $(`.step-content[data-step="${currentStep}"] input[required], .step-content[data-step="${currentStep}"] select[required]`);
            let valid = true;
            
            currentInputs.each(function() {
                if (!this.checkValidity()) {
                    this.reportValidity();
                    valid = false;
                    return false;
                }
            });

            if (valid && currentStep < totalSteps) {
                currentStep++;
                updateStep(currentStep);
            }
        });

        // Form submission loading state
        $('#registration-form').on('submit', function() {
            const btn = $('#submit-btn');
            btn.prop('disabled', true);
            btn.html('<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processando...');
        });

        $('#prev-btn').click(function() {
            if (currentStep > 1) {
                currentStep--;
                updateStep(currentStep);
            }
        });
        
        // Show medical certificate filename
        $('#medical_certificate').change(function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $('#file-name').text('Selecionado: ' + fileName).removeClass('hidden');
            } else {
                $('#file-name').addClass('hidden');
            }
        });

        // Modal Helpers
        window.openModal = function(id) {
            $(`#${id}`).removeClass('hidden');
            $('body').addClass('overflow-hidden');
        };

        window.closeModal = function(id) {
            $(`#${id}`).addClass('hidden');
            $('body').removeClass('overflow-hidden');
        };
    });
</script>
<style>
    .step-indicator.active div:first-child {
        background: white;
        color: var(--primary-color, #2563eb);
        border-color: white;
        transform: scale(1.1);
    }
    .step-indicator {
        transition: all 0.3s ease;
    }
    .step-content {
        animation: fadeIn 0.4s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush
@endsection

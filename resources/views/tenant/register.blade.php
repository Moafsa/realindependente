@extends('layouts.app')

@section('title', 'Cadastro de Clube')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Crie sua conta</h1>
            <p class="text-gray-600">Comece a gerenciar seu clube em poucos passos</p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-semibold step-indicator" data-step="1">
                        1
                    </div>
                    <div class="flex-1 h-1 mx-2 bg-gray-300 step-line" data-step="1"></div>
                </div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="2">
                        2
                    </div>
                    <div class="flex-1 h-1 mx-2 bg-gray-300 step-line" data-step="2"></div>
                </div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="3">
                        3
                    </div>
                    <div class="flex-1 h-1 mx-2 bg-gray-300 step-line" data-step="3"></div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-gray-600 font-semibold step-indicator" data-step="4">
                        4
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-2">
                <span class="text-sm text-gray-600 step-label" data-step="1">Dados do Admin</span>
                <span class="text-sm text-gray-600 step-label" data-step="2">Dados do Clube</span>
                <span class="text-sm text-gray-600 step-label" data-step="3">Escolha do Plano</span>
                <span class="text-sm text-gray-600 step-label" data-step="4">Pagamento</span>
            </div>
        </div>

        <!-- Global Errors -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Foram encontrados erros no seu cadastro:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form id="tenant-registration-form" method="POST" action="{{ route('tenant.register.store') }}" class="bg-white rounded-lg shadow-xl p-8">
            @csrf
            <input type="hidden" name="current_step" id="current_step_input" value="{{ old('current_step', 1) }}">
            <input type="hidden" name="plan_id" id="plan_id" value="{{ old('plan_id', $selectedPlanId ?? '') }}" required>

            <!-- Step 1: Admin Data -->
            <div class="step-content" data-step="1">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Dados do Administrador</h2>
                <p class="text-gray-600 mb-6">Estes serão seus dados de acesso ao sistema</p>

                <div class="space-y-6">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="admin_name" 
                               id="admin_name" 
                               value="{{ old('admin_name') }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_name') border-red-500 @enderror"
                               placeholder="Seu nome completo">
                        @error('admin_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="admin_email" 
                               id="admin_email" 
                               value="{{ old('admin_email') }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_email') border-red-500 @enderror"
                               placeholder="seu@email.com">
                        @error('admin_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone/WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="admin_phone" 
                                   id="admin_phone" 
                                   value="{{ old('admin_phone') }}" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_phone') border-red-500 @enderror"
                                   placeholder="(00) 00000-0000">
                            @error('admin_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-2">
                                CPF ou CNPJ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="admin_cpf_cnpj" 
                                   id="admin_cpf_cnpj" 
                                   value="{{ old('admin_cpf_cnpj') }}" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_cpf_cnpj') border-red-500 @enderror"
                                   placeholder="000.000.000-00">
                            @error('admin_cpf_cnpj')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="admin_password" 
                                   id="admin_password" 
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('admin_password') border-red-500 @enderror"
                                   placeholder="Mínimo 8 caracteres">
                            @error('admin_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Senha <span class="text-red-500">*</span>
                            </label>
                            <input type="password" 
                                   name="admin_password_confirmation" 
                                   id="admin_password_confirmation" 
                                   required
                                   minlength="8"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Confirme sua senha">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="button" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition next-step"
                            data-next="2">
                        Próximo →
                    </button>
                </div>
            </div>

            <!-- Step 2: Club Data -->
            <div class="step-content hidden" data-step="2">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Dados do Clube</h2>
                <p class="text-gray-600 mb-6">Informações sobre seu clube ou escolinha</p>

                <div class="space-y-6">
                    <div>
                        <label for="club_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Clube <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="club_name" 
                               id="club_name" 
                               value="{{ old('club_name') }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('club_name') border-red-500 @enderror"
                               placeholder="Ex: Real Independent FC">
                        @error('club_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">
                            Subdomínio <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center">
                            <input type="text" 
                                   name="subdomain" 
                                   id="subdomain" 
                                   value="{{ old('subdomain') }}" 
                                   required
                                   pattern="[a-z0-9-]+"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('subdomain') border-red-500 @enderror"
                                   placeholder="meuclube">
                            <span class="px-4 py-3 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600">
                                .{{ request()->getHost() }}
                            </span>
                        </div>
                        <div id="subdomain-feedback" class="mt-2 text-sm"></div>
                        @error('subdomain')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Apenas letras minúsculas, números e hífens. Este será o endereço do seu clube.
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition prev-step"
                            data-prev="1">
                        ← Voltar
                    </button>
                    <button type="button" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition next-step"
                            data-next="3">
                        Próximo →
                    </button>
                </div>
            </div>

            <!-- Step 3: Plan Selection -->
            <div class="step-content hidden" data-step="3">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Escolha seu Plano</h2>
                <p class="text-gray-600 mb-6">Selecione o plano ideal para o tamanho do seu clube</p>

                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    @foreach($plans as $plan)
                    <div class="plan-card border-2 rounded-lg p-6 cursor-pointer transition hover:shadow-lg {{ $plan->name === 'Profissional' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-200' }}"
                         data-plan-id="{{ $plan->id }}">
                        @if($plan->name === 'Profissional')
                        <div class="bg-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full inline-block mb-4">
                            Mais Popular
                        </div>
                        @endif

                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $plan->description }}</p>

                        <div class="mb-4">
                            <span class="text-3xl font-bold text-gray-900">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</span>
                            <span class="text-gray-600">/mês</span>
                        </div>

                        <ul class="space-y-2 mb-6 text-sm">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                                @endforeach
                            @endif
                        </ul>

                        <button type="button" 
                                class="w-full plan-select-btn px-4 py-2 rounded-lg font-semibold transition {{ $plan->name === 'Profissional' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                data-plan-id="{{ $plan->id }}">
                            Selecionar
                        </button>
                    </div>
                    @endforeach
                </div>

                {{-- Input hidden plan_id movido para o topo do form para consistência --}}

                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="terms" 
                               id="terms" 
                               required
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">
                            Eu aceito os <a href="#" class="text-blue-600 hover:underline">Termos de Uso</a> e a <a href="#" class="text-blue-600 hover:underline">Política de Privacidade</a>
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition prev-step"
                            data-prev="2">
                        ← Voltar
                    </button>
                    <button type="button" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition next-step"
                            data-next="4"
                            id="next-to-payment"
                            disabled>
                        Próximo →
                    </button>
                </div>
            </div>

            <!-- Step 4: Payment -->
            <div class="step-content hidden" data-step="4">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Finalizar Cadastro</h2>
                <p class="text-gray-600 mb-6">Revise suas informações e finalize o pagamento</p>

                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Resumo do Cadastro</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Administrador:</span>
                            <span class="font-medium text-gray-900" id="summary-admin-name">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">E-mail:</span>
                            <span class="font-medium text-gray-900" id="summary-admin-email">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Clube:</span>
                            <span class="font-medium text-gray-900" id="summary-club-name">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subdomínio:</span>
                            <span class="font-medium text-gray-900" id="summary-subdomain">-</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2">
                            <span class="text-gray-600">Plano:</span>
                            <span class="font-medium text-gray-900" id="summary-plan">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>14 dias grátis!</strong> Você terá acesso completo durante o período de teste. 
                        O pagamento será processado após o término do período de teste.
                    </p>
                </div>

                <div class="mt-8 flex justify-between">
                    <button type="button" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition prev-step"
                            data-prev="3">
                        ← Voltar
                    </button>
                    <button type="submit" 
                            class="px-8 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">
                        Finalizar Cadastro
                    </button>
                </div>
            </div>

            <!-- Login Link -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-gray-600">
                    Já tem uma conta de clube? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Faça login aqui</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/tenant-registration.js') }}"></script>
@endsection


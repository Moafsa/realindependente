<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Personal Information -->
    <div class="space-y-6">
        <!-- Profile Completion -->
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-blue-800">Conclusão do Perfil</span>
                <span class="text-sm font-bold text-blue-600">{{ $athlete->profile_completion }}%</span>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $athlete->profile_completion }}%"></div>
            </div>
            @if($athlete->profile_completion < 100)
                <p class="text-xs text-blue-600 mt-2 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Complete seu perfil para desbloquear todos os recursos de IA.
                </p>
            @else
                <p class="text-xs text-green-600 mt-2 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Perfil completo! Atleta verificado e pronto para análise.
                </p>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Pessoais</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Nome Completo</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->full_name }}</dd>
                </div>
                @if($athlete->document)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Documento (CPF)</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->document }}</dd>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->birth_date->format('d/m/Y') }} ({{ $athlete->age }} anos)</dd>
                </div>
                @if($athlete->subcategory)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Categoria (CBF)</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-md text-xs font-bold">{{ $athlete->subcategory }}</span>
                    </dd>
                </div>
                @endif
                @if($athlete->position)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Posição Principal</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->position }}</dd>
                </div>
                @endif
                @if($athlete->positions && count($athlete->positions) > 0)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Outras Posições</dt>
                    <dd class="text-sm text-gray-900">{{ implode(', ', $athlete->positions) }}</dd>
                </div>
                @endif
                @if($athlete->jersey_number)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Número da Camisa</dt>
                    <dd class="text-sm text-gray-900">#{{ $athlete->jersey_number }}</dd>
                </div>
                @endif
                @if($athlete->height)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Altura</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->height }} cm</dd>
                </div>
                @endif
                @if($athlete->weight)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Peso</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->weight }} kg</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($athlete->bio)
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Biografia</h3>
            <p class="text-sm text-gray-700 leading-relaxed">{{ $athlete->bio }}</p>
        </div>
        @endif
    </div>

    <!-- Team and Contact Information -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipe e Localização</h3>
            <dl class="space-y-3">
                @if($athlete->team)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Equipe</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="{{ route('admin.teams.show', $athlete->team) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            {{ $athlete->team->name }}
                        </a>
                    </dd>
                </div>
                @endif
                @if($athlete->branch)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Filial</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="{{ route('branches.show', $athlete->branch) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                            {{ $athlete->branch->name }}
                        </a>
                    </dd>
                </div>
                @endif
                @if($athlete->address)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                    <dd class="text-sm text-gray-900 text-right">{{ $athlete->address }}</dd>
                </div>
                @endif
            </dl>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contatos e Responsáveis</h3>
            <dl class="space-y-3">
                @if($athlete->phone)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Telefone (Atleta)</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->phone }}</dd>
                </div>
                @endif
                @if($athlete->guardian_name)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Responsável</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $athlete->guardian_name }}</dd>
                </div>
                @endif
                @if($athlete->guardian_document)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">CPF Responsável</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->guardian_document }}</dd>
                </div>
                @endif
                @if($athlete->guardian_contact)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">WhatsApp Responsável</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->guardian_contact }}</dd>
                </div>
                @endif
                @if($athlete->guardian_email)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">E-mail Responsável</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="mailto:{{ $athlete->guardian_email }}" class="text-blue-600 hover:text-blue-800">
                            {{ $athlete->guardian_email }}
                        </a>
                    </dd>
                </div>
                @endif
                @if($athlete->emergency_contact)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Contato de Emergência</dt>
                    <dd class="text-sm text-gray-900 text-red-600 font-semibold">{{ $athlete->emergency_contact }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($athlete->medical_conditions || $athlete->allergies)
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Médicas</h3>
            <dl class="space-y-3">
                @if($athlete->medical_conditions && is_array($athlete->medical_conditions) && count($athlete->medical_conditions) > 0)
                <div class="py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Condições Médicas</dt>
                    <dd class="text-sm text-gray-900">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($athlete->medical_conditions as $condition)
                            <li>{{ $condition }}</li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
                @endif
                @if($athlete->allergies && is_array($athlete->allergies) && count($athlete->allergies) > 0)
                <div class="py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Alergias</dt>
                    <dd class="text-sm text-gray-900">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($athlete->allergies as $allergy)
                            <li>{{ $allergy }}</li>
                            @endforeach
                        </ul>
                    </dd>
                </div>
                @endif
                @if($athlete->insurance_info)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Seguro</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->insurance_info }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

        @if($athlete->user)
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Conta de Usuário</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->user->email }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Status da Conta</dt>
                    <dd class="text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $athlete->user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $athlete->user->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
        @endif
    </div>
</div>


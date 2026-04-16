<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Personal Information -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informações Pessoais</h3>
            <dl class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Nome Completo</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->full_name }}</dd>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->birth_date->format('d/m/Y') }} ({{ $athlete->age }} anos)</dd>
                </div>
                @if($athlete->position)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Posição</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->position }}</dd>
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
            <p class="text-sm text-gray-700">{{ $athlete->bio }}</p>
        </div>
        @endif
    </div>

    <!-- Team and Contact Information -->
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipe e Filial</h3>
            <dl class="space-y-3">
                @if($athlete->team)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Equipe</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="{{ route('admin.teams.show', $athlete->team) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $athlete->team->name }}
                        </a>
                    </dd>
                </div>
                @endif
                @if($athlete->branch)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Filial</dt>
                    <dd class="text-sm text-gray-900">
                        <a href="{{ route('branches.show', $athlete->branch) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $athlete->branch->name }}
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contato</h3>
            <dl class="space-y-3">
                @if($athlete->guardian_name)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Responsável</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->guardian_name }}</dd>
                </div>
                @endif
                @if($athlete->guardian_contact)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                    <dd class="text-sm text-gray-900">{{ $athlete->guardian_contact }}</dd>
                </div>
                @endif
                @if($athlete->guardian_email)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <dt class="text-sm font-medium text-gray-500">E-mail</dt>
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
                    <dd class="text-sm text-gray-900">{{ $athlete->emergency_contact }}</dd>
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


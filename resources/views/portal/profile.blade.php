@extends('layouts.portal')

@section('title', 'Meu Perfil')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Meu Perfil</h1>
                    <p class="mt-1 text-sm text-gray-600">Gerencie suas informações pessoais</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-blue-500 h-32"></div>
                    <div class="px-6 py-4 -mt-16">
                        <div class="flex justify-center">
                            <div class="relative">
                                <img src="{{ $athlete->profile_picture_url }}" 
                                     alt="{{ $athlete->full_name }}" 
                                     class="h-32 w-32 rounded-full border-4 border-white object-cover">
                                <button onclick="document.getElementById('profile-picture-input').click()" 
                                        class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-2 hover:bg-blue-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $athlete->full_name }}</h2>
                            @if($athlete->position)
                            <p class="text-sm text-gray-600 mt-1">{{ ucfirst($athlete->position) }}</p>
                            @endif
                            @if($athlete->team)
                            <p class="text-sm text-blue-600 mt-1">{{ $athlete->team->name }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <dl class="space-y-3">
                            @if($athlete->age)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Idade</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $athlete->age }} anos</dd>
                            </div>
                            @endif
                            @if($athlete->jersey_number)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Número da Camisa</dt>
                                <dd class="text-sm font-medium text-gray-900">#{{ $athlete->jersey_number }}</dd>
                            </div>
                            @endif
                            @if($athlete->height)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Altura</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($athlete->height, 2, ',', '.') }} cm</dd>
                            </div>
                            @endif
                            @if($athlete->weight)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Peso</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($athlete->weight, 2, ',', '.') }} kg</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações Pessoais</h3>
                    </div>
                    <form method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" class="hidden" onchange="handleImageUpload(this)">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nome Completo
                                </label>
                                <input type="text" 
                                       name="full_name" 
                                       id="full_name" 
                                       value="{{ old('full_name', $athlete->full_name) }}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('full_name') border-red-500 @enderror">
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Data de Nascimento
                                </label>
                                <input type="date" 
                                       name="birth_date" 
                                       id="birth_date" 
                                       value="{{ old('birth_date', $athlete->birth_date?->format('Y-m-d')) }}"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('birth_date') border-red-500 @enderror">
                                @error('birth_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                                    Posição
                                </label>
                                <select name="position" 
                                        id="position"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="goalkeeper" {{ old('position', $athlete->position) === 'goalkeeper' ? 'selected' : '' }}>Goleiro</option>
                                    <option value="defender" {{ old('position', $athlete->position) === 'defender' ? 'selected' : '' }}>Zagueiro</option>
                                    <option value="midfielder" {{ old('position', $athlete->position) === 'midfielder' ? 'selected' : '' }}>Meio-campo</option>
                                    <option value="forward" {{ old('position', $athlete->position) === 'forward' ? 'selected' : '' }}>Atacante</option>
                                </select>
                                @error('position')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jersey_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número da Camisa
                                </label>
                                <input type="text" 
                                       name="jersey_number" 
                                       id="jersey_number" 
                                       value="{{ old('jersey_number', $athlete->jersey_number) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jersey_number') border-red-500 @enderror">
                                @error('jersey_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                    Altura (cm)
                                </label>
                                <input type="number" 
                                       name="height" 
                                       id="height" 
                                       value="{{ old('height', $athlete->height) }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('height') border-red-500 @enderror">
                                @error('height')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                    Peso (kg)
                                </label>
                                <input type="number" 
                                       name="weight" 
                                       id="weight" 
                                       value="{{ old('weight', $athlete->weight) }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('weight') border-red-500 @enderror">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                Biografia
                            </label>
                            <textarea name="bio" 
                                      id="bio" 
                                      rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror">{{ old('bio', $athlete->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações de Contato</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $athlete->user->email ?? 'Não informado' }}</dd>
                            </div>
                            @if($athlete->guardian_name)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Responsável</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $athlete->guardian_name }}</dd>
                            </div>
                            @endif
                            @if($athlete->guardian_contact)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Telefone do Responsável</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $athlete->guardian_contact }}</dd>
                            </div>
                            @endif
                            @if($athlete->emergency_contact)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contato de Emergência</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $athlete->emergency_contact }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Medical Information -->
                @if($athlete->medical_conditions || $athlete->allergies || $athlete->insurance_info)
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações Médicas</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            @if($athlete->medical_conditions)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Condições Médicas</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(is_array($athlete->medical_conditions))
                                        {{ implode(', ', $athlete->medical_conditions) }}
                                    @else
                                        {{ $athlete->medical_conditions }}
                                    @endif
                                </dd>
                            </div>
                            @endif
                            @if($athlete->allergies)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Alergias</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if(is_array($athlete->allergies))
                                        {{ implode(', ', $athlete->allergies) }}
                                    @else
                                        {{ $athlete->allergies }}
                                    @endif
                                </dd>
                            </div>
                            @endif
                            @if($athlete->insurance_info)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Informações de Seguro</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $athlete->insurance_info }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function handleImageUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('img[alt="{{ $athlete->full_name }}"]').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection


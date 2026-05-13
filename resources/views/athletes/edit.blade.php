@extends('layouts.dashboard')

@section('title', 'Editar Atleta - ' . $athlete->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Atleta</h1>
            <p class="text-gray-600">Atualize os dados de {{ $athlete->full_name }}</p>
        </div>
        <a href="{{ route('admin.athletes.show', $athlete) }}" class="text-gray-600 hover:text-gray-900">
            ← Voltar ao Perfil
        </a>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.athletes.update', $athlete) }}" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="md:col-span-2">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Básicas</h2>
            </div>

            <div class="md:col-span-2">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $athlete->full_name) }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('full_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="document" class="block text-sm font-medium text-gray-700">Documento (CPF)</label>
                <input type="text" name="document" id="document" value="{{ old('document', $athlete->document) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="000.000.000-00">
                @error('document')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone do Atleta</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $athlete->phone) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="(00) 00000-0000">
                @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Endereço Residencial</label>
                <input type="text" name="address" id="address" value="{{ old('address', $athlete->address) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Rua, Número, Bairro, Cidade - UF">
                @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gênero</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="gender" value="masculino" class="mr-2" {{ old('gender', $athlete->gender) == 'masculino' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Masculino</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="gender" value="feminino" class="mr-2" {{ old('gender', $athlete->gender) == 'feminino' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Feminino</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700">Data de Nascimento *</label>
                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $athlete->birth_date ? $athlete->birth_date->format('Y-m-d') : '') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('birth_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subcategory" class="block text-sm font-medium text-gray-700">Subcategoria (Auto se vazio)</label>
                <select name="subcategory" id="subcategory"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Automático por idade</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}" {{ old('subcategory', $athlete->subcategory) == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                @error('subcategory')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Posição</label>
                <input type="text" name="position" id="position" value="{{ old('position', $athlete->position) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Ex: Goleiro, Atacante, etc.">
                @error('position')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="team_id" class="block text-sm font-medium text-gray-700">Equipe</label>
                <select name="team_id" id="team_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Selecione uma equipe</option>
                    @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ old('team_id', $athlete->team_id) == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('team_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700">Filial</label>
                <select name="branch_id" id="branch_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Selecione uma filial</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ old('branch_id', $athlete->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jersey_number" class="block text-sm font-medium text-gray-700">Número da Camisa</label>
                <input type="text" name="jersey_number" id="jersey_number" value="{{ old('jersey_number', $athlete->jersey_number) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('jersey_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700">Foto do Perfil (deixe em branco para manter a atual)</label>
                <div class="mt-1 flex items-center space-x-4">
                    @if($athlete->profile_picture_url)
                        <img src="{{ $athlete->profile_picture_url }}" alt="Atual" class="h-12 w-12 rounded-full object-cover border">
                    @endif
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                @error('profile_picture')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="bio" class="block text-sm font-medium text-gray-700">Biografia</label>
                <textarea name="bio" id="bio" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('bio', $athlete->bio) }}</textarea>
                @error('bio')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Physical Information -->
            <div class="md:col-span-2 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Físicas</h2>
            </div>

            <div>
                <label for="height" class="block text-sm font-medium text-gray-700">Altura (cm)</label>
                <input type="number" name="height" id="height" value="{{ old('height', $athlete->height) }}" min="0" max="300" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('height')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Peso (kg)</label>
                <input type="number" name="weight" id="weight" value="{{ old('weight', $athlete->weight) }}" min="0" max="500" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('weight')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Guardian Information -->
            <div class="md:col-span-2 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações do Responsável</h2>
            </div>

            <div>
                <label for="guardian_name" class="block text-sm font-medium text-gray-700">Nome do Responsável</label>
                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name', $athlete->guardian_name) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="guardian_document" class="block text-sm font-medium text-gray-700">CPF do Responsável</label>
                <input type="text" name="guardian_document" id="guardian_document" value="{{ old('guardian_document', $athlete->guardian_document) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="000.000.000-00">
                @error('guardian_document')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="guardian_email" class="block text-sm font-medium text-gray-700">Email do Responsável</label>
                <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email', $athlete->guardian_email) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="guardian_contact" class="block text-sm font-medium text-gray-700">Telefone do Responsável</label>
                <input type="text" name="guardian_contact" id="guardian_contact" value="{{ old('guardian_contact', $athlete->guardian_contact) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_contact')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Contato de Emergência</label>
                <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $athlete->emergency_contact) }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('emergency_contact')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Medical Information -->
            <div class="md:col-span-2 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Médicas</h2>
            </div>

            <div class="md:col-span-2">
                <label for="medical_conditions" class="block text-sm font-medium text-gray-700">Condições Médicas (separadas por vírgula)</label>
                <input type="text" name="medical_conditions" id="medical_conditions" value="{{ old('medical_conditions', is_array($athlete->medical_conditions) ? implode(', ', $athlete->medical_conditions) : '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Ex: Asma, Diabetes, etc.">
                @error('medical_conditions')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="allergies" class="block text-sm font-medium text-gray-700">Alergias (separadas por vírgula)</label>
                <input type="text" name="allergies" id="allergies" value="{{ old('allergies', is_array($athlete->allergies) ? implode(', ', $athlete->allergies) : '') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Ex: Amendoim, Lácteos, etc.">
                @error('allergies')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="insurance_info" class="block text-sm font-medium text-gray-700">Informações de Seguro</label>
                <textarea name="insurance_info" id="insurance_info" rows="2"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('insurance_info', $athlete->insurance_info) }}</textarea>
                @error('insurance_info')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="medical_certificate" class="block text-sm font-medium text-gray-700">Atestado Médico (PDF ou Imagem)</label>
                <div class="mt-1 flex items-center space-x-4">
                    @if($athlete->medical_certificate_path)
                        <a href="{{ $athlete->medical_certificate_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver Atual
                        </a>
                    @endif
                    <input type="file" name="medical_certificate" id="medical_certificate" accept=".pdf,image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>
                @error('medical_certificate')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $athlete->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Atleta Ativo</span>
                </label>
            </div>

            <!-- Security/Account Information -->
            <div class="md:col-span-2 mt-6 border-t pt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Segurança e Acesso</h2>
                <p class="text-sm text-gray-500 mb-4 italic">Deixe os campos de senha em branco se não desejar alterar a senha do atleta.</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                <input type="password" name="password" id="password"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.athletes.show', $athlete) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                Salvar Alterações
            </button>
        </div>
    </form>
</div>
@endsection

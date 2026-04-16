@extends('layouts.dashboard')

@section('title', 'Adicionar Atleta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Adicionar Atleta</h1>
            <p class="text-gray-600">Preencha os dados do novo atleta</p>
        </div>
        <a href="{{ route('admin.athletes.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Voltar
        </a>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.athletes.store') }}" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="md:col-span-2">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações Básicas</h2>
            </div>

            <div class="md:col-span-2">
                <label for="full_name" class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('full_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="birth_date" class="block text-sm font-medium text-gray-700">Data de Nascimento *</label>
                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('birth_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Posição</label>
                <input type="text" name="position" id="position" value="{{ old('position') }}"
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
                    <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
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
                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jersey_number" class="block text-sm font-medium text-gray-700">Número da Camisa</label>
                <input type="text" name="jersey_number" id="jersey_number" value="{{ old('jersey_number') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('jersey_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700">Foto do Perfil</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                @error('profile_picture')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="bio" class="block text-sm font-medium text-gray-700">Biografia</label>
                <textarea name="bio" id="bio" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('bio') }}</textarea>
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
                <input type="number" name="height" id="height" value="{{ old('height') }}" min="0" max="300" step="0.01"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('height')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Peso (kg)</label>
                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" min="0" max="500" step="0.01"
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
                <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="guardian_email" class="block text-sm font-medium text-gray-700">Email do Responsável</label>
                <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="guardian_contact" class="block text-sm font-medium text-gray-700">Telefone do Responsável</label>
                <input type="text" name="guardian_contact" id="guardian_contact" value="{{ old('guardian_contact') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('guardian_contact')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="emergency_contact" class="block text-sm font-medium text-gray-700">Contato de Emergência</label>
                <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}"
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
                <input type="text" name="medical_conditions" id="medical_conditions" value="{{ old('medical_conditions') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Ex: Asma, Diabetes, etc.">
                @error('medical_conditions')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="allergies" class="block text-sm font-medium text-gray-700">Alergias (separadas por vírgula)</label>
                <input type="text" name="allergies" id="allergies" value="{{ old('allergies') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="Ex: Amendoim, Lácteos, etc.">
                @error('allergies')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="insurance_info" class="block text-sm font-medium text-gray-700">Informações de Seguro</label>
                <textarea name="insurance_info" id="insurance_info" rows="2"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('insurance_info') }}</textarea>
                @error('insurance_info')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- User Account -->
            <div class="md:col-span-2 mt-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Conta de Usuário</h2>
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center">
                    <input type="checkbox" name="create_user_account" value="1" {{ old('create_user_account') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Criar conta de usuário para este atleta</span>
                </label>
            </div>

            <div class="md:col-span-2 user-account-fields" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-gray-700">Email do Usuário</label>
                        <input type="email" name="user_email" id="user_email" value="{{ old('user_email') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('user_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="user_password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" name="user_password" id="user_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('user_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.athletes.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                Criar Atleta
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.querySelector('input[name="create_user_account"]');
    const fields = document.querySelector('.user-account-fields');
    
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            fields.style.display = 'block';
            document.getElementById('user_email').required = true;
            document.getElementById('user_password').required = true;
        } else {
            fields.style.display = 'none';
            document.getElementById('user_email').required = false;
            document.getElementById('user_password').required = false;
        }
    });
    
    // Initialize on page load
    if (checkbox.checked) {
        fields.style.display = 'block';
    }
});
</script>
@endsection

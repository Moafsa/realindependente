@extends('layouts.dashboard')

@section('title', 'Editar Equipe: ' . $team->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-900">Editar Equipe</h1>
            <p class="text-sm text-gray-600">Atualize as informações da equipe</p>
        </div>
        
        <form method="POST" action="{{ route('admin.teams.update', $team) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome da Equipe</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $team->name) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                    <select name="category" id="category" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('category') border-red-300 @enderror">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ old('category', $team->category) == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descrição</label>
                <textarea name="description" id="description" rows="3"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror">{{ old('description', $team->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="coach_id" class="block text-sm font-medium text-gray-700">Treinador</label>
                <select name="coach_id" id="coach_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('coach_id') border-red-300 @enderror">
                    <option value="">Selecione um treinador</option>
                    @foreach($coaches as $coach)
                    <option value="{{ $coach->id }}" {{ old('coach_id', $team->coach_id) == $coach->id ? 'selected' : '' }}>
                        {{ $coach->name }}
                    </option>
                    @endforeach
                </select>
                @error('coach_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="color_primary" class="block text-sm font-medium text-gray-700">Cor Primária</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $team->primary_color) }}"
                               class="h-10 w-20 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('primary_color') border-red-300 @enderror">
                        <input type="text" value="{{ old('primary_color', $team->primary_color) }}"
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               oninput="this.previousElementSibling.value = this.value">
                    </div>
                    @error('primary_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="color_secondary" class="block text-sm font-medium text-gray-700">Cor Secundária</label>
                    <div class="mt-1 flex items-center space-x-3">
                        <input type="color" name="secondary_color" id="secondary_color" value="{{ old('secondary_color', $team->secondary_color) }}"
                               class="h-10 w-20 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('secondary_color') border-red-300 @enderror">
                        <input type="text" value="{{ old('secondary_color', $team->secondary_color) }}"
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               oninput="this.previousElementSibling.value = this.value">
                    </div>
                    @error('secondary_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="logo" class="block text-sm font-medium text-gray-700">Logo da Equipe</label>
                <div class="mt-1 flex items-center space-x-4">
                    @if($team->logo)
                        <img src="{{ Storage::url($team->logo) }}" alt="Logo" class="h-16 w-16 rounded-full object-cover border">
                    @endif
                    <input id="logo" name="logo" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                </div>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.teams.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

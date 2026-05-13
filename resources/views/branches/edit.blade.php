@extends('layouts.dashboard')

@section('title', 'Editar Filial: ' . $branch->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900">Editar Filial</h1>
            <p class="text-sm text-gray-600">Atualize as informações da unidade {{ $branch->name }}</p>
        </div>
        
        <form method="POST" action="{{ route('branches.update', $branch) }}" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Nome da Unidade <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" required
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium @error('name') border-red-300 @enderror"
                           placeholder="Ex: Sede Principal, Unidade Centro">
                    @error('name')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Endereço Completo <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="3" required
                              class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium @error('address') border-red-300 @enderror"
                              placeholder="Rua, Número, Bairro, Cidade - Estado">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Telefone de Contato</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}"
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium @error('phone') border-red-300 @enderror"
                           placeholder="(00) 00000-0000">
                    @error('phone')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">E-mail Administrativo</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}"
                           class="w-full px-5 py-4 bg-gray-50 border-transparent rounded-2xl focus:bg-white focus:ring-2 focus:ring-blue-600 transition outline-none text-gray-900 font-medium @error('email') border-red-300 @enderror"
                           placeholder="contato@filial.com">
                    @error('email')
                        <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <div class="bg-blue-50 border border-blue-100 rounded-3xl p-6 flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-blue-900 mb-1">Geolocalização (Opcional)</h3>
                            <p class="text-xs text-blue-700 leading-relaxed mb-4">Insira as coordenadas para habilitar o botão do Google Maps no perfil da unidade.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="number" name="latitude" value="{{ old('latitude', $branch->latitude) }}" step="any" placeholder="Latitude" class="w-full px-4 py-3 bg-white border-transparent rounded-xl focus:ring-2 focus:ring-blue-600 transition outline-none text-sm text-gray-900">
                                <input type="number" name="longitude" value="{{ old('longitude', $branch->longitude) }}" step="any" placeholder="Longitude" class="w-full px-4 py-3 bg-white border-transparent rounded-xl focus:ring-2 focus:ring-blue-600 transition outline-none text-sm text-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50">
                <a href="{{ route('branches.show', $branch) }}" class="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition">
                    Descartar Alterações
                </a>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 transform hover:-translate-y-1">
                    Salvar Filial
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.portal')

@section('title', 'Meu Perfil')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-white uppercase italic tracking-tighter">Meu <span class="text-blue-500">Perfil</span></h1>
            <p class="text-gray-400">Gerencie suas informações pessoais e conta</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-xl text-sm font-bold uppercase tracking-widest animate-pulse">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl text-sm font-bold uppercase tracking-widest">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden shadow-xl">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-24"></div>
                <div class="px-6 py-4 -mt-12 text-center">
                    <div class="relative inline-block">
                        <img src="{{ $athlete ? $athlete->profile_picture_url : (auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name)) }}" 
                             alt="{{ $athlete ? $athlete->full_name : auth()->user()->name }}" 
                             class="h-24 w-24 rounded-2xl border-4 border-[#0a0a0a] object-cover shadow-2xl mx-auto">
                        @if($athlete)
                        <button onclick="document.getElementById('profile-picture-input').click()" 
                                class="absolute -bottom-2 -right-2 bg-blue-600 text-white rounded-xl p-2 hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 border-2 border-[#0a0a0a]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                        @endif
                    </div>
                    <h2 class="text-xl font-black text-white mt-4 uppercase italic tracking-tight">{{ $athlete ? $athlete->full_name : auth()->user()->name }}</h2>
                    <p class="text-xs text-blue-400 font-bold uppercase tracking-widest mt-1">{{ $athlete ? ($athlete->subcategory ?? 'Categoria não definida') : auth()->user()->role }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-1">{{ $athlete ? ($athlete->team->name ?? 'Sem equipe') : 'Administrador do Sistema' }}</p>
                </div>
                
                <div class="px-6 py-4 border-t border-white/5 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Posição</span>
                        <span class="text-xs text-white font-bold">{{ $athlete ? ($athlete->position ?? 'N/A') : 'Staff' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Documento</span>
                        <span class="text-xs text-white font-bold">{{ $athlete ? ($athlete->document ?? 'N/A') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">E-mail</span>
                        <span class="text-xs text-white font-bold">{{ auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl">
                <form id="profile-form" method="POST" action="{{ route('portal.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="px-6 py-4 border-b border-white/5">
                        <h3 class="text-sm font-black text-white uppercase italic tracking-widest">Informações da Conta</h3>
                    </div>

                    @if($athlete)
                    <input type="file" id="profile-picture-input" name="profile_picture" accept="image/*" class="hidden" onchange="handleImageUpload(this); document.getElementById('profile-form').submit();">
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="full_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                Nome Completo
                            </label>
                            <input type="text" name="full_name" id="full_name" 
                                   value="{{ old('full_name', $athlete->full_name) }}" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="birth_date" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Data de Nascimento
                                </label>
                                <input type="date" name="birth_date" id="birth_date" 
                                       value="{{ old('birth_date', $athlete->birth_date?->format('Y-m-d')) }}" required
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label for="athlete_document" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Documento (RG/CPF)
                                </label>
                                <input type="text" name="document" id="athlete_document" 
                                       value="{{ old('document', $athlete->document) }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="position" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Posição Principal
                                </label>
                                <input type="text" name="position" id="position" 
                                       value="{{ old('position', $athlete->position) }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label for="jersey_number" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Número da Camisa
                                </label>
                                <input type="text" name="jersey_number" id="jersey_number" 
                                       value="{{ old('jersey_number', $athlete->jersey_number) }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="height" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Altura (m)
                                </label>
                                <input type="number" name="height" id="height" step="0.01"
                                       value="{{ old('height', $athlete->height) }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label for="weight" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Peso (kg)
                                </label>
                                <input type="number" name="weight" id="weight" step="0.1"
                                       value="{{ old('weight', $athlete->weight) }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                Telefone de Contato
                            </label>
                            <input type="text" name="phone" id="phone" 
                                   value="{{ old('phone', $athlete->phone) }}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        <div>
                            <label for="address" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                Endereço Completo
                            </label>
                            <input type="text" name="address" id="address" 
                                   value="{{ old('address', $athlete->address) }}"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                    </div>
                    @else
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                Nome
                            </label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', auth()->user()->name) }}" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                        <div>
                            <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                E-mail
                            </label>
                            <input type="email" name="email" id="email" 
                                   value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Nova Senha (deixe em branco para não alterar)
                                </label>
                                <input type="password" name="password" id="password" 
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Confirmar Nova Senha
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($athlete)
                    <!-- Guardian Information -->
                    <div class="border-t border-white/5">
                        <div class="px-6 py-4 border-b border-white/5">
                            <h3 class="text-sm font-black text-white uppercase italic tracking-widest">Informações do Responsável</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="guardian_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Nome do Responsável
                                    </label>
                                    <input type="text" name="guardian_name" id="guardian_name" 
                                           value="{{ old('guardian_name', $athlete->guardian_name) }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>

                                <div>
                                    <label for="guardian_contact" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Contato (WhatsApp/Fone)
                                    </label>
                                    <input type="text" name="guardian_contact" id="guardian_contact" 
                                           value="{{ old('guardian_contact', $athlete->guardian_contact) }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="px-6 py-4 border-t border-white/5 flex justify-end">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            @if($athlete)
            <!-- Documents Section -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl">
                <div class="px-6 py-4 border-b border-white/5">
                    <h3 class="text-sm font-black text-white uppercase italic tracking-widest">Documentos e Atestados</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/5">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-white font-bold uppercase tracking-tight">Atestado Médico</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Validade: {{ $athlete->medical_expiry_date ?? 'Não informada' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if($athlete->medical_certificate_path)
                            <a href="{{ $athlete->medical_certificate_url }}" target="_blank" class="text-xs text-blue-400 font-black uppercase tracking-widest hover:text-blue-300">Ver</a>
                            @endif
                            <button onclick="document.getElementById('medical-cert-input').click()" class="text-xs text-gray-400 font-black uppercase tracking-widest hover:text-white">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function handleImageUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.querySelector('img[alt="{{ $athlete ? $athlete->full_name : auth()->user()->name }}"]');
                if (img) img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

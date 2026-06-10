@extends(auth()->user()->role === 'admin' || auth()->user()->role === 'coach' ? 'layouts.admin' : 'layouts.portal')

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

                    @if($athlete && in_array(auth()->user()->role, ['athlete', 'guardian']))
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

                    @if(auth()->user()->role === 'coach')
                    <!-- Coach Professional Profile -->
                    <div class="border-t border-white/5">
                        <div class="px-6 py-4 border-b border-white/5">
                            <h3 class="text-sm font-black text-white uppercase italic tracking-widest">Perfil Profissional & Currículo</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Telefone / WhatsApp
                                    </label>
                                    <input type="text" name="phone" id="phone" 
                                           value="{{ auth()->user()->phone }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="(00) 00000-0000">
                                </div>
                                <div>
                                    <label for="specialties" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Especialidades Técnicas
                                    </label>
                                    <input type="text" name="specialties" id="specialties" 
                                           value="{{ auth()->user()->specialties }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Ex: Tática, Base, Analista">
                                </div>
                            </div>

                            <div>
                                <label for="bio" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Biografia e Filosofia de Jogo
                                </label>
                                <textarea name="bio" id="bio" rows="4" 
                                          class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Compartilhe sua trajetória...">{{ auth()->user()->bio }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="education" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Formação Acadêmica
                                    </label>
                                    <textarea name="education" id="education" rows="4" 
                                              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Graus acadêmicos...">{{ auth()->user()->education }}</textarea>
                                </div>
                                <div>
                                    <label for="experience" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Experiência Profissional
                                    </label>
                                    <textarea name="experience" id="experience" rows="4" 
                                              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Clubes anteriores...">{{ auth()->user()->experience }}</textarea>
                                </div>
                            </div>

                            <!-- Certificates Section -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Certificações & Diplomas</h4>
                                    <button type="button" onclick="addCertificateRow()" class="text-xs font-bold text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Adicionar Novo
                                    </button>
                                </div>
                                
                                <div id="certificates-container" class="space-y-3">
                                    @foreach(auth()->user()->certificates ?? [] as $index => $cert)
                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5 group hover:bg-white/10 transition-all">
                                        <div class="flex items-center gap-3 overflow-hidden">
                                            <div class="p-2 bg-blue-500/10 text-blue-400 rounded-lg shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <span class="text-xs font-bold text-gray-300 truncate">{{ $cert['name'] }}</span>
                                        </div>
                                        <button type="submit" name="remove_certificate" value="{{ $index }}" class="text-rose-500/50 hover:text-rose-500 transition-colors p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->role === 'admin')
                    <!-- Club / Website Settings -->
                    <div class="border-t border-white/5">
                        <div class="px-6 py-4 border-b border-white/5">
                            <h3 class="text-sm font-black text-white uppercase italic tracking-widest">Dados do Clube & Website</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="site_name" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Nome do Clube (Exibição)
                                    </label>
                                    <input type="text" name="settings[site_name]" id="site_name" 
                                           value="{{ \App\Models\SiteSetting::get('site_name') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                                <div>
                                    <label for="contact_email" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        E-mail de Contato Público
                                    </label>
                                    <input type="email" name="settings[contact_email]" id="contact_email" 
                                           value="{{ \App\Models\SiteSetting::get('contact_email') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_phone" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Telefone de Contato
                                    </label>
                                    <input type="text" name="settings[contact_phone]" id="contact_phone" 
                                           value="{{ \App\Models\SiteSetting::get('contact_phone') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                                <div>
                                    <label for="contact_whatsapp" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        WhatsApp (Somente números)
                                    </label>
                                    <input type="text" name="settings[contact_whatsapp]" id="contact_whatsapp" 
                                           value="{{ \App\Models\SiteSetting::get('contact_whatsapp') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="contact_address" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                    Endereço Sede (Aparece no Mapa)
                                </label>
                                <input type="text" name="settings[contact_address]" id="contact_address" 
                                       value="{{ \App\Models\SiteSetting::get('contact_address') }}"
                                       class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="instagram_url" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Link do Instagram
                                    </label>
                                    <input type="text" name="settings[instagram_url]" id="instagram_url" 
                                           value="{{ \App\Models\SiteSetting::get('instagram_url') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="https://instagram.com/seuclube">
                                </div>
                                <div>
                                    <label for="facebook_url" class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">
                                        Link do Facebook
                                    </label>
                                    <input type="text" name="settings[facebook_url]" id="facebook_url" 
                                           value="{{ \App\Models\SiteSetting::get('facebook_url') }}"
                                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="https://facebook.com/seuclube">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
    <script>
        function addCertificateRow() {
            const container = document.getElementById('certificates-container');
            const row = document.createElement('div');
            row.className = 'grid grid-cols-1 md:grid-cols-2 gap-4 relative group animate__animated animate__fadeInUp';
            row.innerHTML = `
                <input type="text" name="certificate_names[]" placeholder="Nome do Certificado" class="px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                <div class="flex gap-2">
                    <input type="file" name="certificate_files[]" class="flex-1 px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-gray-400 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-500 file:text-white hover:file:bg-indigo-600 transition-all">
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="p-3 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            `;
            container.appendChild(row);
        }
    </script>
@endsection

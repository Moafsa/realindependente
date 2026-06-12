@extends('layouts.admin')

@section('title', 'Meu Perfil Profissional')

@section('content')
<div class="animate__animated animate__fadeIn">
    <div class="mb-12">
        <h1 class="text-3xl font-black text-white tracking-tight">Meu Perfil Profissional</h1>
        <p class="text-gray-400 mt-2">Gerencie sua identidade, currículo e certificações oficiais.</p>
    </div>

    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 flex items-center gap-3 animate__animated animate__shakeX">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.coach.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Avatar and Quick Actions -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 text-center relative overflow-hidden group">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/10 blur-[80px] rounded-full group-hover:bg-indigo-600/20 transition-all duration-700"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="absolute inset-0 bg-gradient-to-tr from-indigo-600 to-purple-600 rounded-full blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <img id="avatar-preview" src="{{ $coach->avatar_url }}" alt="Avatar" class="relative w-40 h-40 rounded-full object-cover border-4 border-white/10 shadow-2xl mx-auto">
                        <label for="avatar" class="absolute bottom-1 right-1 bg-indigo-600 text-white p-3 rounded-2xl cursor-pointer shadow-xl hover:bg-indigo-700 hover:scale-110 transition-all z-10 border border-white/10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    
                    <h2 class="text-2xl font-black text-white tracking-tight">{{ $coach->name }}</h2>
                    <p class="text-indigo-400 font-bold text-xs uppercase tracking-[0.3em] mt-2">Membro da Comissão</p>
                    
                    <div class="mt-10 space-y-4 text-left">
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">E-mail Institucional</label>
                            <p class="text-sm text-gray-200 font-bold truncate">{{ $coach->email }}</p>
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Telefone / WhatsApp</label>
                            <input type="text" name="phone" value="{{ $coach->phone }}" class="w-full bg-transparent border-none p-0 text-sm text-white font-bold focus:ring-0" placeholder="(00) 00000-0000">
                        </div>
                        <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Especialidades Técnicas</label>
                            <input type="text" name="specialties" value="{{ $coach->specialties }}" placeholder="Ex: Tática, Base, Analista" class="w-full bg-transparent border-none p-0 text-sm text-white font-bold focus:ring-0">
                        </div>
                    </div>
                </div>

                <!-- Certificates Grid Sidebar -->
                <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest">Certificados</h3>
                        <span class="px-2 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold rounded-lg border border-indigo-500/20">
                            {{ count($coach->certificates ?? []) }} TOTAL
                        </span>
                    </div>
                    <div class="space-y-3">
                        @forelse($coach->certificates ?? [] as $index => $cert)
                            <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/5 group hover:bg-white/10 transition-all">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="p-2 bg-indigo-500/10 text-indigo-400 rounded-lg shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-gray-300 truncate" title="{{ $cert['name'] }}">{{ $cert['name'] }}</span>
                                </div>
                                <button type="submit" name="remove_certificate" value="{{ $index }}" class="text-rose-500/50 hover:text-rose-500 transition-colors p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-white/5 rounded-2xl">
                                <p class="text-xs text-gray-500 font-medium italic">Nenhum registro oficial.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Curriculo and Experience -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-[#0F1423] rounded-[2rem] p-10 shadow-2xl border border-white/5">
                    <div class="space-y-10">
                        <div>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-white tracking-tight">Biografia e Filosofia</h3>
                            </div>
                            <textarea name="bio" rows="6" class="w-full px-6 py-5 bg-white/5 border border-white/10 rounded-[2rem] text-sm text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:bg-white/10 transition-all placeholder-gray-600" placeholder="Compartilhe sua trajetória, conquistas e visão de jogo...">{{ $coach->bio }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    Formação Acadêmica
                                </h3>
                                <textarea name="education" rows="5" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Graus acadêmicos e cursos de especialização...">{{ $coach->education }}</textarea>
                            </div>
                            <div class="space-y-4">
                                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Experiência Profissional
                                </h3>
                                <textarea name="experience" rows="5" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Clubes anteriores, cargos e períodos...">{{ $coach->experience }}</textarea>
                            </div>
                        </div>

                        <hr class="border-white/5">

                        <!-- Multi-upload section -->
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-white tracking-tight">Novas Certificações</h3>
                                <button type="button" onclick="addCertificateRow()" class="px-4 py-2 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Novo Documento
                                </button>
                            </div>
                            <div id="certificates-container" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 animate__animated animate__fadeIn">
                                    <input type="text" name="certificate_names[]" placeholder="Título (ex: Licença A CBF)" class="px-5 py-3 bg-white/5 border border-white/10 rounded-xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <input type="file" name="certificate_files[]" class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-xl text-xs text-gray-400 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-500 file:text-white hover:file:bg-indigo-600 transition-all">
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 flex justify-end">
                            <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-500/40 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all">
                                Atualizar Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Coach Gallery -->
    <div class="mt-8">
        <h2 class="text-2xl font-black text-white tracking-tight mb-4">Galeria de Mídia</h2>
        <x-gallery-manager :galleryItems="$coach->galleryItems" galleryableType="App\Models\User" :galleryableId="$coach->id" />
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

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

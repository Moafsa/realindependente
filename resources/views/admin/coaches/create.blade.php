@extends('layouts.admin')

@section('title', 'Adicionar Treinador')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="mb-12 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Novo Profissional</h1>
            <p class="text-gray-400 mt-2">Cadastre um novo treinador ou auxiliar na comissão técnica.</p>
        </div>
        <a href="{{ route('admin.coaches.index') }}" class="px-6 py-3 bg-white/5 text-gray-400 hover:text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all">Voltar</a>
    </div>

    <form action="{{ route('admin.coaches.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
        @csrf
        <div class="space-y-8">
            <!-- Basic Info Card -->
            <div class="bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5 relative overflow-hidden group">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/10 blur-[80px] rounded-full group-hover:bg-indigo-600/20 transition-all duration-700"></div>
                
                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informações de Identidade
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="name" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Nome Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                               placeholder="Ex: João Silva">
                        @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">E-mail de Acesso</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                               placeholder="coach@clube.com">
                        @error('email') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Senha Temporária</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                               placeholder="mínimo 8 caracteres">
                        @error('password') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">WhatsApp / Telefone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                               placeholder="(00) 00000-0000">
                        @error('phone') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="avatar" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Foto de Perfil</label>
                        <div class="flex items-center gap-4">
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="flex-1 text-[10px] text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition-all cursor-pointer">
                        </div>
                        @error('avatar') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Financial Card -->
            <div class="bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5 relative overflow-hidden group">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-600/10 blur-[80px] rounded-full group-hover:bg-emerald-600/20 transition-all duration-700"></div>
                
                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    Acordos Financeiros
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="salary" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Base de Remuneração (R$)</label>
                        <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary') }}"
                               class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-600 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                               placeholder="0.00">
                        @error('salary') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="payment_frequency" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest ml-2">Recorrência do Ciclo</label>
                        <select name="payment_frequency" id="payment_frequency"
                                class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none appearance-none">
                            <option value="" class="bg-[#0F1423]">Selecione um ciclo...</option>
                            @foreach($frequencies as $key => $label)
                                <option value="{{ $key }}" {{ old('payment_frequency') == $key ? 'selected' : '' }} class="bg-[#0F1423]">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_frequency') <p class="text-rose-500 text-[10px] font-bold mt-1 ml-2 uppercase tracking-tighter">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" class="px-12 py-5 bg-indigo-600 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-600/30 hover:bg-indigo-700 hover:-translate-y-1 active:scale-95 transition-all">
                    Finalizar Cadastro
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

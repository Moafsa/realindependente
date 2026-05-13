@extends('layouts.admin')

@section('title', 'Corpo Técnico')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Header -->
    <div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Corpo Técnico</h1>
            <p class="text-gray-400 mt-2">Gerencie treinadores, auxiliares e profissionais do clube.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.coaches.create') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 hover:-translate-y-1 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Novo Profissional
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-[#0F1423] p-6 rounded-[2rem] shadow-2xl border border-white/5 mb-10">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="block w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 focus:bg-white/10 transition-all outline-none" 
                       placeholder="Buscar por nome ou e-mail...">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-8 py-4 bg-white/5 text-white border border-white/10 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-white/10 transition-all">
                    Filtrar
                </button>
                <a href="{{ route('admin.coaches.index') }}" class="px-4 py-4 bg-rose-500/10 text-rose-500 rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Coaches Grid/Table -->
    <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/[0.01]">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Profissional</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Contato</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Saldo em Aberto</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($coaches as $coach)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="{{ $coach->avatar_url }}" class="w-12 h-12 rounded-xl object-cover ring-2 ring-white/5 group-hover:ring-indigo-500 transition-all">
                                    @if($coach->is_active)
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-[#0F1423] rounded-full"></div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black text-white leading-none mb-1">{{ $coach->name }}</p>
                                    <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest">{{ $coach->payment_frequency ?? 'Não definido' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-300">{{ $coach->email }}</div>
                            <div class="text-[10px] text-gray-500 font-bold uppercase mt-1">{{ $coach->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-lg font-black {{ $coach->current_balance < 0 ? 'text-rose-500' : 'text-emerald-500' }}">
                                    R$ {{ number_format($coach->current_balance, 2, ',', '.') }}
                                </span>
                                @if($coach->salary)
                                <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest mt-1">
                                    Base: R$ {{ number_format($coach->salary, 2, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $coach->is_active ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                                {{ $coach->is_active ? 'Ativo' : 'Bloqueado' }}
                            </span>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            <div class="flex justify-end items-center gap-2">
                                <!-- Quick Pay -->
                                <form action="{{ route('admin.coaches.pay', $coach) }}" method="POST" class="inline" onsubmit="return confirm('Confirmar registro de pagamento de salário?')">
                                    @csrf
                                    <button type="submit" class="p-3 bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl transition-all" title="Pagar Salário">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </button>
                                </form>

                                <!-- Manual Transaction -->
                                <button onclick="openTransactionModal({{ $coach->id }}, '{{ $coach->name }}')" class="p-3 bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl transition-all" title="Lançamento Manual">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </button>

                                <div class="w-px h-6 bg-white/5 mx-1"></div>

                                <!-- View Details -->
                                <a href="{{ route('admin.coaches.show', $coach) }}" class="p-3 bg-white/5 text-gray-400 hover:bg-indigo-500/20 hover:text-indigo-400 rounded-xl transition-all" title="Ver Detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('admin.coaches.edit', $coach) }}" class="p-3 bg-white/5 text-gray-400 hover:bg-white/10 hover:text-white rounded-xl transition-all" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('admin.coaches.destroy', $coach) }}" method="POST" class="inline" onsubmit="return confirm('Excluir treinador permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-3 bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white rounded-xl transition-all" title="Excluir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center text-gray-700 mb-6">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-sm">Nenhum profissional cadastrado</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($coaches->hasPages())
        <div class="p-8 bg-white/[0.01] border-t border-white/5">
            {{ $coaches->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Transaction Modal (Glassmorphic) -->
<div id="transactionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeTransactionModal()">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-[#0F1423] rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/10">
            <form id="transactionForm" method="POST">
                @csrf
                <div class="p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight" id="modal-title">Novo Lançamento</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest" id="coachName"></p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Descrição da Operação</label>
                            <input type="text" name="description" required placeholder="Ex: Bônus de Performance, Ajuste de Salário..." class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Valor Líquido (R$)</label>
                                <input type="number" name="amount" step="0.01" required placeholder="0.00" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Natureza</label>
                                <select name="type" required class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none appearance-none">
                                    <option value="exit" class="bg-[#0F1423]">Saída (Clube paga)</option>
                                    <option value="entry" class="bg-[#0F1423]">Entrada (Clube recebe)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Data do Lançamento</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        </div>
                    </div>
                </div>
                <div class="p-10 bg-white/[0.02] border-t border-white/5 flex gap-3">
                    <button type="button" onclick="closeTransactionModal()" class="flex-1 px-8 py-4 bg-white/5 text-white rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-white/10 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all">
                        Efetivar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openTransactionModal(coachId, coachName) {
        document.getElementById('coachName').innerText = coachName;
        let url = "{{ route('admin.coaches.add-transaction', ':id') }}";
        document.getElementById('transactionForm').action = url.replace(':id', coachId);
        document.getElementById('transactionModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTransactionModal() {
        document.getElementById('transactionModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeTransactionModal();
        }
    });
</script>
@endsection

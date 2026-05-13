@extends('layouts.dashboard')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Entradas e Saídas</h2>
            <p class="text-gray-500 text-sm">Gerencie todas as movimentações financeiras do clube.</p>
        </div>
        <button onclick="openModal('add-transaction-modal')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nova Transação
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Entradas</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">R$ {{ number_format($entries, 2, ',', '.') }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Saídas</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">R$ {{ number_format($exits, 2, ',', '.') }}</div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 {{ $balance >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600' }} rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold {{ $balance >= 0 ? 'text-blue-600' : 'text-orange-600' }} uppercase tracking-wider">Saldo</span>
            </div>
            <div class="text-3xl font-bold text-gray-900">R$ {{ number_format($balance, 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('admin.cash-flow.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo</label>
                <select name="type" class="w-full bg-gray-50 border-transparent rounded-lg text-sm focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="entry" {{ request('type') == 'entry' ? 'selected' : '' }}>Entrada</option>
                    <option value="exit" {{ request('type') == 'exit' ? 'selected' : '' }}>Saída</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Início</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-gray-50 border-transparent rounded-lg text-sm focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fim</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-gray-50 border-transparent rounded-lg text-sm focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Categoria</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $transaction->description }}</div>
                            <div class="text-xs text-gray-400">Por: {{ $transaction->creator->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $transaction->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->type === 'entry')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Entrada
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Saída
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-bold {{ $transaction->type === 'entry' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'entry' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button onclick="deleteTransaction('{{ $transaction->id }}')" class="text-gray-400 hover:text-red-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Nenhuma transação encontrada para este período.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="add-transaction-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeModal('add-transaction-modal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.cash-flow.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Nova Transação</h3>
                    <button type="button" onclick="closeModal('add-transaction-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descrição</label>
                        <input type="text" name="description" required class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500" placeholder="Ex: Pagamento Aluguel">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Valor (R$)</label>
                            <input type="number" step="0.01" name="amount" required class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500" placeholder="0,00">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Data</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tipo</label>
                            <select name="type" required class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500">
                                <option value="entry">Entrada</option>
                                <option value="exit" selected>Saída</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Categoria</label>
                            <input type="text" name="category" list="category-list" required class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500" placeholder="Ex: Material">
                            <datalist id="category-list">
                                @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" required class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500">
                            <option value="completed">Concluído</option>
                            <option value="pending">Pendente</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notas (Opcional)</label>
                        <textarea name="notes" rows="2" class="w-full bg-gray-50 border-transparent rounded-lg focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end space-x-3 bg-gray-50">
                    <button type="button" onclick="closeModal('add-transaction-modal')" class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700 transition">Cancelar</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">Salvar Transação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function deleteTransaction(id) {
        if (confirm('Tem certeza que deseja excluir esta transação?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('cash-flow') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection

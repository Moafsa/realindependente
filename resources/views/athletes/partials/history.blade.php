<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Histórico de Clubes</h3>
            <p class="text-sm text-gray-500">Gerencie a linha do tempo de clubes pelos quais o atleta já passou.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.athletes.history.update', $athlete) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="bg-white border rounded-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-medium text-gray-900">Clubes Cadastrados</h4>
                <button type="button" onclick="addHistoryRowAdmin()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Clube
                </button>
            </div>

            <div id="admin-history-container" class="space-y-4">
                @forelse($athlete->history as $history)
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nome do Clube</label>
                            <input type="text" name="history[{{ $history->id }}][club_name]" value="{{ $history->club_name }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Escudo (opcional)</label>
                            <input type="file" name="history[{{ $history->id }}][logo]" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                            @if($history->club_logo_url)
                                <p class="text-xs text-green-600 mt-1">Já possui logo. Envie outra para substituir.</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Data de Entrada</label>
                            <input type="date" name="history[{{ $history->id }}][start_date]" value="{{ $history->start_date->format('Y-m-d') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Data de Saída (Deixe em branco se for o atual)</label>
                            <input type="date" name="history[{{ $history->id }}][end_date]" value="{{ $history->end_date ? $history->end_date->format('Y-m-d') : '' }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <a href="{{ route('admin.athletes.history.delete', ['athlete' => $athlete->id, 'id' => $history->id]) }}" onclick="return confirm('Excluir este histórico?')" class="text-xs text-red-600 hover:text-red-800 font-medium">Excluir Clube</a>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4" id="admin-history-empty">Nenhum histórico adicionado ainda.</p>
                @endforelse
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Salvar Histórico
            </button>
        </div>
    </form>
</div>

<script>
    function addHistoryRowAdmin() {
        const container = document.getElementById('admin-history-container');
        const emptyMsg = document.getElementById('admin-history-empty');
        if(emptyMsg) emptyMsg.style.display = 'none';
        
        const newId = 'new_' + Date.now();
        const row = document.createElement('div');
        row.className = 'p-4 bg-gray-50 rounded-lg border border-gray-200 relative mt-4';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Nome do Clube</label>
                    <input type="text" name="new_history[${newId}][club_name]" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Escudo (opcional)</label>
                    <input type="file" name="new_history[${newId}][logo]" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de Entrada</label>
                    <input type="date" name="new_history[${newId}][start_date]" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Data de Saída (Deixe em branco se for o atual)</label>
                    <input type="date" name="new_history[${newId}][end_date]" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>
            <div class="mt-3 flex justify-end">
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-xs text-red-600 hover:text-red-800 font-medium">Cancelar</button>
            </div>
        `;
        container.appendChild(row);
    }
</script>

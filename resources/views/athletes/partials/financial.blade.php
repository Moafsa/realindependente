<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Histórico Financeiro</h3>
        <a href="{{ route('athletes.financial-history', $athlete) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            Ver detalhes →
        </a>
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-600">
            O histórico financeiro completo está disponível na página dedicada.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">Total Pago</div>
            <div class="text-2xl font-bold text-green-600" id="total-paid">R$ 0,00</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">Pendente</div>
            <div class="text-2xl font-bold text-yellow-600" id="total-pending">R$ 0,00</div>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="text-sm text-gray-500 mb-1">Total de Pedidos</div>
            <div class="text-2xl font-bold text-gray-900" id="total-orders">0</div>
        </div>
    </div>

    <div id="financial-history-list" class="space-y-3">
        <!-- Financial history will be loaded via AJAX -->
        <div class="text-center py-8 text-gray-500">
            Carregando histórico financeiro...
        </div>
    </div>
</div>

<script>
    // Load financial history
    fetch('{{ route("athletes.financial-history", $athlete) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update summary cards
                document.getElementById('total-paid').textContent = 'R$ ' + data.summary.total_paid.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('total-pending').textContent = 'R$ ' + data.summary.total_pending.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('total-orders').textContent = data.summary.total_orders;

                // Render history list
                const listContainer = document.getElementById('financial-history-list');
                if (data.orders && data.orders.length > 0) {
                    listContainer.innerHTML = data.orders.map(order => `
                        <div class="flex items-center justify-between p-3 bg-white border rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900">Pedido #${order.id}</div>
                                <div class="text-sm text-gray-500">${order.date}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-gray-900">R$ ${order.amount.toLocaleString('pt-BR', {minimumFractionDigits: 2})}</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${order.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                    ${order.status_label}
                                </span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    listContainer.innerHTML = '<div class="text-center py-8 text-gray-500">Nenhum pedido encontrado</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading financial history:', error);
            document.getElementById('financial-history-list').innerHTML = '<div class="text-center py-8 text-red-500">Erro ao carregar histórico financeiro</div>';
        });
</script>


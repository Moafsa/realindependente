@extends('layouts.dashboard')

@section('title', $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">Detalhes do produto</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('products.edit', $product) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar
            </a>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                Voltar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Image -->
            <div class="bg-white shadow rounded-lg p-6 flex justify-center">
                <img src="{{ $product->image_url }}" 
                     alt="{{ $product->name }}" 
                     class="max-w-full h-96 object-contain rounded-lg">
            </div>

            <!-- Description -->
            @if($product->description)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrição</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
            </div>
            @endif

            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Estatísticas</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-500 mb-1">Total de Vendas</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total_sales'] ?? 0 }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-500 mb-1">Receita Total</div>
                        <div class="text-2xl font-bold text-green-600">R$ {{ number_format($stats['total_revenue'] ?? 0, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Product Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Preço</dt>
                        <dd class="text-sm font-semibold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                        <dd class="text-sm text-gray-900">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($product->type) }}
                            </span>
                        </dd>
                    </div>
                    @if($product->sku)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                        <dd class="text-sm text-gray-900">{{ $product->sku }}</dd>
                    </div>
                    @endif
                    @if($product->stock_quantity !== null)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Estoque</dt>
                        <dd class="text-sm font-semibold {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->stock_quantity }}
                        </dd>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </dd>
                    </div>
                    @if($product->is_featured)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Destaque</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Sim
                            </span>
                        </dd>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Criado em</dt>
                        <dd class="text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm font-medium text-gray-500">Atualizado em</dt>
                        <dd class="text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h2>
                <div class="space-y-2">
                    <form method="POST" action="{{ route('products.toggle-status', $product) }}" class="inline w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                            {{ $product->is_active ? 'Desativar' : 'Ativar' }} Produto
                        </button>
                    </form>
                    @if($product->stock_quantity !== null)
                    <button onclick="showStockModal()" 
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition">
                        Atualizar Estoque
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Update Modal -->
<div id="stock-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Atualizar Estoque</h3>
            <form method="POST" action="{{ route('products.update-stock', $product) }}">
                @csrf
                <div class="mb-4">
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Nova Quantidade
                    </label>
                    <input type="number" 
                           name="stock_quantity" 
                           id="stock_quantity" 
                           value="{{ $product->stock_quantity }}"
                           min="0"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hideStockModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showStockModal() {
        document.getElementById('stock-modal').classList.remove('hidden');
    }

    function hideStockModal() {
        document.getElementById('stock-modal').classList.add('hidden');
    }
</script>
@endsection


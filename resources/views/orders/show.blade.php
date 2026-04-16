@extends('layouts.dashboard')

@section('title', 'Pedido #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $order->id }}</h1>
            <p class="text-sm text-gray-600 mt-1">Detalhes do pedido</p>
        </div>
        <a href="{{ route('orders.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
            Voltar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Itens do Pedido</h2>
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                    <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0">
                            <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100' }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="h-16 w-16 object-cover rounded-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-500">Quantidade: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">R$ {{ number_format($item->price, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">Total: R$ {{ number_format($item->total, 2, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Observações</h2>
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                $order->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($order->status === 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                ($order->status === 'delivered' ? 'bg-purple-100 text-purple-800' : 
                                'bg-red-100 text-red-800'))) 
                            }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Data do Pedido</dt>
                        <dd class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($order->paid_at)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Data de Pagamento</dt>
                        <dd class="text-sm text-gray-900">{{ $order->paid_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($order->shipped_at)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Data de Envio</dt>
                        <dd class="text-sm text-gray-900">{{ $order->shipped_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($order->delivered_at)
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Data de Entrega</dt>
                        <dd class="text-sm text-gray-900">{{ $order->delivered_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                        <dd class="text-sm text-gray-900">R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-lg font-semibold text-gray-900">Total</dt>
                        <dd class="text-lg font-bold text-gray-900">R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Customer Info -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome</dt>
                        <dd class="text-sm text-gray-900">{{ $order->user->name ?? $order->athlete->full_name ?? 'Cliente' }}</dd>
                    </div>
                    @if($order->user)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                        <dd class="text-sm text-gray-900">{{ $order->user->email }}</dd>
                    </div>
                    @endif
                    @if($order->athlete)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Atleta</dt>
                        <dd class="text-sm text-gray-900">
                            <a href="{{ route('admin.athletes.show', $order->athlete) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $order->athlete->full_name }}
                            </a>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ações</h2>
                <div class="space-y-2">
                    @if($order->status === 'pending')
                    <form method="POST" action="{{ route('orders.update-status', $order) }}" class="inline w-full">
                        @csrf
                        <input type="hidden" name="status" value="paid">
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition">
                            Marcar como Pago
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'paid')
                    <form method="POST" action="{{ route('orders.ship', $order) }}" class="inline w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition">
                            Marcar como Enviado
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'shipped')
                    <form method="POST" action="{{ route('orders.deliver', $order) }}" class="inline w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left px-4 py-2 text-sm bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg transition">
                            Marcar como Entregue
                        </button>
                    </form>
                    @endif

                    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                    <form method="POST" action="{{ route('orders.update-status', $order) }}" class="inline w-full">
                        @csrf
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" 
                                onclick="return confirm('Tem certeza que deseja cancelar este pedido?')"
                                class="w-full text-left px-4 py-2 text-sm bg-red-50 text-red-700 hover:bg-red-100 rounded-lg transition">
                            Cancelar Pedido
                        </button>
                    </form>
                    @endif

                    <!-- Update Status Form -->
                    <div class="pt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('orders.update-status', $order) }}">
                            @csrf
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Alterar Status
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-2">
                                @foreach(['pending' => 'Pendente', 'paid' => 'Pago', 'shipped' => 'Enviado', 'delivered' => 'Entregue', 'cancelled' => 'Cancelado'] as $key => $label)
                                <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                                Atualizar Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


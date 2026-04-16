@extends('layouts.dashboard')

@section('title', 'Detalhes da Cobrança #' . $order->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detalhes da Cobrança #{{ $order->id }}</h1>
            <p class="text-gray-600">Informações completas da cobrança</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('financial.charges') }}" class="text-gray-600 hover:text-gray-900">
                ← Voltar
            </a>
            @if($order->status !== 'paid' && $order->status !== 'cancelled')
            <form method="POST" action="{{ route('financial.cancel-charge', $order) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja cancelar esta cobrança?');">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    Cancelar Cobrança
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações da Cobrança</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID da Cobrança</dt>
                        <dd class="mt-1 text-sm text-gray-900">#{{ $order->id }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status === 'paid') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'overdue') bg-red-100 text-red-800
                                @elseif($order->status === 'cancelled') bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($order->status === 'paid') Pago
                                @elseif($order->status === 'pending') Pendente
                                @elseif($order->status === 'overdue') Vencido
                                @elseif($order->status === 'cancelled') Cancelado
                                @else {{ ucfirst($order->status ?? 'N/A') }}
                                @endif
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Valor Total</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Criação</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </dd>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Pagamento</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $order->paid_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                    @endif
                    @if($order->asaas_payment_id)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID Asaas</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->asaas_payment_id }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Athlete Information -->
            @if($order->athlete)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações do Atleta</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->athlete->full_name ?? 'N/A' }}</dd>
                    </div>
                    @if($order->athlete->team)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Equipe</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->athlete->team->name }}</dd>
                    </div>
                    @endif
                    @if($order->athlete->guardian_email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email do Responsável</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->athlete->guardian_email }}</dd>
                    </div>
                    @endif
                    @if($order->athlete->guardian_contact)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contato do Responsável</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $order->athlete->guardian_contact }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Order Items -->
            @if($order->orderItems && $order->orderItems->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Itens da Cobrança</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço Unitário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->product->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    R$ {{ number_format($item->price ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    R$ {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Asaas Details -->
            @if($chargeDetails)
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Asaas</h2>
                <dl class="space-y-3">
                    @if(isset($chargeDetails['id']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">ID do Pagamento</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $chargeDetails['id'] }}</dd>
                    </div>
                    @endif
                    @if(isset($chargeDetails['status']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($chargeDetails['status']) }}</dd>
                    </div>
                    @endif
                    @if(isset($chargeDetails['invoiceUrl']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Link de Pagamento</dt>
                        <dd class="mt-1">
                            <a href="{{ $chargeDetails['invoiceUrl'] }}" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                Abrir Boleto/PIX
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if(isset($chargeDetails['dueDate']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Vencimento</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($chargeDetails['dueDate'])->format('d/m/Y') }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Ações</h2>
                <div class="space-y-2">
                    <a href="{{ route('financial.charges') }}" class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Voltar para Lista
                    </a>
                    @if($order->asaas_payment_url)
                    <a href="{{ $order->asaas_payment_url }}" target="_blank" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Ver Pagamento no Asaas
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

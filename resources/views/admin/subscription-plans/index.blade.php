@extends('layouts.dashboard')

@section('title', 'Planos de Assinatura')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Planos de Assinatura</h1>
            <p class="text-sm text-gray-600 mt-1">Gerencie os planos de mensalidade dos seus atletas</p>
        </div>
        <a href="{{ route('admin.subscription-plans.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Plano
        </a>
    </div>

    <!-- Plans Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($plans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciclo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($plans as $plan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $plan->name }}</div>
                                    @if($plan->is_featured)
                                    <div class="text-xs text-yellow-600 font-medium">★ Plano em Destaque</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @php
                                $cycleLabels = [
                                    'MONTHLY' => 'Mensal',
                                    'QUARTERLY' => 'Trimestral',
                                    'SEMIANNUALLY' => 'Semestral',
                                    'YEARLY' => 'Anual'
                                ];
                                echo $cycleLabels[$plan->attributes['cycle'] ?? 'MONTHLY'] ?? 'Mensal';
                            @endphp
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ route('admin.subscription-plans.edit', $plan) }}" 
                                   class="text-blue-600 hover:text-blue-900 font-bold">
                                    Editar
                                </a>
                                <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Deseja realmente excluir este plano?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $plans->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Nenhum plano cadastrado</h3>
            <p class="mt-1 text-sm text-gray-500">Comece criando seu primeiro plano de assinatura.</p>
            <div class="mt-6">
                <a href="{{ route('admin.subscription-plans.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    Criar Novo Plano
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

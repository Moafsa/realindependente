@extends('layouts.admin')

@section('title', 'Dashboard Global (Super Admin)')

@section('content')
<div class="p-6">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Visão Geral do Sistema</h1>
            <p class="text-gray-600 mt-2">Resumo da performance de todos os clubes cadastrados.</p>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-widest">Super Admin Mode</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-3xl shadow-xl p-6 border-b-4 border-blue-500 transition-all hover:shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Total de Clubes</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['total_tenants'] }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-6 border-b-4 border-emerald-500 transition-all hover:shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Clubes Ativos</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['active_tenants'] }}</p>
                </div>
                <div class="bg-emerald-50 p-3 rounded-2xl">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-6 border-b-4 border-amber-500 transition-all hover:shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Pendentes</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['pending_tenants'] }}</p>
                </div>
                <div class="bg-amber-50 p-3 rounded-2xl">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-6 border-b-4 border-indigo-500 transition-all hover:shadow-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">MRR Estimado</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">R$ {{ number_format($stats['revenue_monthly'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-indigo-50 p-3 rounded-2xl">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Growth Chart -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl p-8 overflow-hidden">
            <h2 class="text-xl font-black text-gray-900 mb-8">Crescimento (Novos Clubes)</h2>
            <div class="flex items-end justify-between h-48 gap-2">
                @foreach($growth as $data)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-indigo-500 rounded-t-lg transition-all hover:bg-indigo-600" style="height: {{ max($data['count'] * 20, 5) }}px"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase mt-4">{{ $data['month'] }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ $data['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Distribution by Plan -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h2 class="text-xl font-black text-gray-900 mb-8">Distribuição por Plano</h2>
            <div class="space-y-6">
                @foreach($tenantsByPlan as $plan)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-black text-gray-500 uppercase tracking-widest">{{ $plan->name }}</span>
                        <span class="text-sm font-black text-gray-900">{{ $plan->tenants_count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        @php
                            $percentage = $stats['total_tenants'] > 0 ? ($plan->tenants_count / $stats['total_tenants']) * 100 : 0;
                        @endphp
                        <div class="bg-indigo-500 h-3 rounded-full shadow-lg shadow-indigo-100" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Tenants -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-xl font-black text-gray-900">Clubes Recentes</h2>
                <a href="{{ route('admin.tenants.index') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-black uppercase tracking-widest">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Clube</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Plano</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentTenants as $tenant)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6 whitespace-nowrap">
                                <div class="text-sm font-black text-gray-900">{{ $tenant->name }}</div>
                                <div class="text-[10px] text-gray-500">{{ $tenant->subdomain }}.meuclube.app</div>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg bg-gray-100 text-gray-600">{{ $tenant->plan->name ?? '-' }}</span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap">
                                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg 
                                    {{ $tenant->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                <a href="{{ route('admin.tenants.show', $tenant) }}" class="text-indigo-600 hover:text-indigo-800 font-black text-xs uppercase tracking-widest">Gerenciar</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top AI Consumers -->
        <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
            <h2 class="text-xl font-black text-gray-900 mb-8">Maiores Consumidores IA</h2>
            <div class="space-y-6">
                @forelse($topAiTenants as $consumer)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-black mr-3">
                            {{ substr($consumer['name'], 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-black text-gray-900">{{ $consumer['name'] }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">{{ $consumer['requests'] }} requisições</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-black text-indigo-600">US$ {{ number_format($consumer['cost'], 2) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">Nenhum dado de uso de IA disponível.</p>
                @endforelse
            </div>
            <a href="{{ route('admin.ai.monitoring') }}" class="block w-full mt-8 py-4 bg-gray-50 text-center text-xs font-black text-gray-500 uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition-all">Ver Relatório Completo</a>
        </div>
    </div>
</div>
@endsection

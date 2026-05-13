@extends('layouts.admin')

@section('title', 'Monitoramento de IA')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Custos & Uso de IA</h1>
        <p class="text-gray-600 mt-2">Acompanhe o consumo de tokens e custos da OpenAI em tempo real.</p>
    </div>

    <!-- Global Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-3xl shadow-xl p-8 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Custo Global Estimado (Mês)</p>
                    <p class="text-4xl font-black text-gray-900 mt-1">US$ {{ number_format($totalGlobalCost, 4, '.', ',') }}</p>
                    <p class="text-sm text-gray-500 mt-2">Aproximadamente R$ {{ number_format($totalGlobalCost * 5.5, 2, ',', '.') }}</p>
                </div>
                <div class="bg-indigo-50 p-4 rounded-2xl">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl p-8 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Total de Requisições (Mês)</p>
                    <p class="text-4xl font-black text-gray-900 mt-1">{{ number_format($totalGlobalRequests, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-2">Gerações de planos e análises</p>
                </div>
                <div class="bg-emerald-50 p-4 rounded-2xl">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Usage by Tenant -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
            <h2 class="text-xl font-black text-gray-900">Consumo por Clube</h2>
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Ordenado por custo</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Clube</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Plano</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Requisições</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Custo Estimado</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Tendência</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($monitoringData as $data)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 font-black mr-4 uppercase">
                                    {{ substr($data['tenant']->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="text-sm font-black text-gray-900">{{ $data['tenant']->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $data['tenant']->subdomain }}.meuclube.app</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap">
                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg bg-gray-100 text-gray-600">
                                {{ $data['tenant']->plan->name ?? 'Sem Plano' }}
                            </span>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-center">
                            <div class="text-sm font-black text-gray-900">{{ $data['usage']['count'] ?? 0 }}</div>
                            <div class="flex justify-center gap-1 mt-1">
                                @foreach($data['usage']['by_type'] ?? [] as $type => $count)
                                    <span class="w-2 h-2 rounded-full {{ $type === 'workout_plan' ? 'bg-blue-400' : 'bg-emerald-400' }}" title="{{ $type }}: {{ $count }}"></span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            <div class="text-sm font-black text-indigo-600">US$ {{ number_format($data['usage']['costs'] ?? 0, 4, '.', ',') }}</div>
                            <div class="text-[10px] text-gray-400 mt-1">R$ {{ number_format(($data['usage']['costs'] ?? 0) * 5.5, 2, ',', '.') }}</div>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end text-emerald-500 text-sm font-bold">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                0%
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Vendas por Clube')

@section('content')
<div class="space-y-6 animate__animated animate__fadeIn">
    <div class="relative group">
        <div class="absolute -inset-0.5 bg-white/5 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
        <div class="relative bg-[#0F1423]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Performance Transacional</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Volume de vendas e comissões geradas por cada clube</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5">
                            <th class="px-8 py-6 text-left">Clube</th>
                            <th class="px-8 py-6 text-left">Plano</th>
                            <th class="px-8 py-6 text-left">Volume (GMV)</th>
                            <th class="px-8 py-6 text-left">Taxa</th>
                            <th class="px-8 py-6 text-left">Comissão Plataforma</th>
                            <th class="px-8 py-6 text-right">Pedidos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($salesData as $data)
                        <tr class="group hover:bg-white/[0.02] transition-colors">
                            <td class="px-8 py-6">
                                <p class="text-xs font-black text-white uppercase tracking-tight">{{ $data->tenant_name }}</p>
                                <p class="text-[9px] text-gray-500 font-bold mt-0.5 tracking-widest">{{ $data->tenant_id }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $data->plan_name }}</span>
                            </td>
                            <td class="px-8 py-6 text-xs text-white font-bold">
                                R$ {{ number_format($data->revenue, 2, ',', '.') }}
                            </td>
                            <td class="px-8 py-6 text-[10px] font-black text-orange-400">
                                {{ $data->fee_percentage }}%
                            </td>
                            <td class="px-8 py-6 text-xs text-emerald-400 font-black">
                                R$ {{ number_format($data->commission, 2, ',', '.') }}
                            </td>
                            <td class="px-8 py-6 text-right text-xs text-gray-400">
                                {{ $data->orders_count }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

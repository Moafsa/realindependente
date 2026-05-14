@extends('layouts.admin')

@section('title', 'Assinaturas de Clubes')

@section('content')
<div class="space-y-6 animate__animated animate__fadeIn">
    <div class="relative group">
        <div class="absolute -inset-0.5 bg-white/5 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
        <div class="relative bg-[#0F1423]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 overflow-hidden">
            <div class="p-8 border-b border-white/5">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Controle de Assinaturas SaaS</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Status de pagamento e renovação dos clubes</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] border-b border-white/5">
                            <th class="px-8 py-6 text-left">Clube</th>
                            <th class="px-8 py-6 text-left">Plano</th>
                            <th class="px-8 py-6 text-left">Próxima Fatura</th>
                            <th class="px-8 py-6 text-left">Status Asaas</th>
                            <th class="px-8 py-6 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($tenants as $tenant)
                        <tr class="group hover:bg-white/[0.02] transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-400 font-black text-xs">
                                        {{ substr($tenant->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-white uppercase tracking-tight">{{ $tenant->name }}</p>
                                        <p class="text-[9px] text-gray-500 font-bold mt-0.5 tracking-widest">{{ $tenant->domain }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $tenant->plan->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-8 py-6 text-xs text-gray-400">
                                {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-{{ $tenant->status == 'active' ? 'emerald' : 'orange' }}-500/10 text-{{ $tenant->status == 'active' ? 'emerald' : 'orange' }}-500 text-[9px] font-black uppercase tracking-widest rounded-full">
                                    {{ $tenant->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <button class="p-2 text-gray-500 hover:text-white transition-colors" title="Ver faturas no Asaas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-8 border-t border-white/5">
                {{ $tenants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

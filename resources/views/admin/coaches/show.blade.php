@extends('layouts.admin')

@section('title', 'Detalhes do Treinador')

@section('content')
<div class="animate__animated animate__fadeIn">
    <!-- Header Card -->
    <div class="mb-10 bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-600/10 blur-[100px] rounded-full"></div>
        <div class="flex flex-col md:flex-row items-center gap-10 relative">
            <div class="relative group">
                <img src="{{ $coach->avatar_url }}" alt="{{ $coach->name }}" class="w-40 h-40 rounded-[2rem] object-cover ring-4 ring-white/10 group-hover:ring-indigo-500/50 transition-all shadow-2xl shadow-indigo-600/20">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-[2rem] flex items-center justify-center">
                    <span class="text-[10px] font-black text-white uppercase tracking-widest">Treinador</span>
                </div>
            </div>
            <div class="text-center md:text-left flex-1">
                <div class="flex flex-col md:flex-row md:items-center gap-4 mb-4">
                    <h1 class="text-4xl font-black text-white tracking-tighter">{{ $coach->name }}</h1>
                    <span class="px-4 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest inline-block self-center md:self-start mt-2 md:mt-0">ID #{{ $coach->id }}</span>
                </div>
                <div class="flex flex-wrap justify-center md:justify-start gap-6 text-gray-400">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-sm font-bold tracking-tight">{{ $coach->email }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="text-sm font-bold tracking-tight">{{ $coach->phone ?? 'Não informado' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3 shrink-0 w-full md:w-auto">
                <a href="{{ route('admin.coaches.edit', $coach) }}" class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest text-center transition-all border border-white/5">Editar Perfil</a>
                <a href="{{ route('admin.coaches.extract') }}?coach_id={{ $coach->id }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest text-center transition-all shadow-xl shadow-indigo-600/20">Ver Extrato</a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-600/10 blur-[50px] rounded-full group-hover:bg-indigo-600/20 transition-all"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Saldo Atual</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($coach->current_balance, 2, ',', '.') }}</h3>
        </div>
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-600/10 blur-[50px] rounded-full group-hover:bg-emerald-600/20 transition-all"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Equipes Gerenciadas</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $teams->count() }}</h3>
        </div>
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-purple-600/10 blur-[50px] rounded-full group-hover:bg-purple-600/20 transition-all"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Atletas sob Comando</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $total_athletes }}</h3>
        </div>
        <div class="bg-[#0F1423] rounded-[2rem] p-8 shadow-2xl border border-white/5 relative overflow-hidden group">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-orange-600/10 blur-[50px] rounded-full group-hover:bg-orange-600/20 transition-all"></div>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Salário Registrado</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($coach->salary, 2, ',', '.') }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Details Column -->
        <div class="lg:col-span-8 space-y-10">
            <!-- Professional Info -->
            <div class="bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5">
                <h3 class="text-xl font-black text-white tracking-tight mb-8 flex items-center gap-3">
                    <div class="w-2 h-8 bg-indigo-500 rounded-full"></div>
                    Informações Profissionais
                </h3>
                
                <div class="space-y-10">
                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Biografia / Perfil</p>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $coach->bio ?? 'Sem biografia cadastrada.' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Educação / Formação</p>
                            <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-line">{{ $coach->education ?? 'Não informado.' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Experiência Profissional</p>
                            <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-line">{{ $coach->experience ?? 'Não informado.' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Especialidades</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $coach->specialties ?? '') as $specialty)
                                @if(trim($specialty))
                                    <span class="px-4 py-2 bg-white/5 text-gray-300 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-white/5">{{ trim($specialty) }}</span>
                                @endif
                            @endforeach
                            @if(!$coach->specialties)
                                <span class="text-sm text-gray-500 italic">Nenhuma especialidade listada.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Managed Teams -->
            <div class="bg-[#0F1423] rounded-[2.5rem] shadow-2xl border border-white/5 overflow-hidden">
                <div class="p-8 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <h3 class="text-lg font-black text-white tracking-tight uppercase tracking-widest">Equipes Atuais</h3>
                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 text-[10px] font-black rounded-lg uppercase tracking-widest border border-indigo-500/20">{{ $teams->count() }} Elencos</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-white/[0.02] border-b border-white/5">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-widest">Equipe</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-widest">Categoria</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-widest text-center">Atletas</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($teams as $team)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-8 py-6 text-sm font-black text-white uppercase tracking-tight">{{ $team->name }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-white/5 text-gray-400 rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $team->category ?? 'Geral' }}</span>
                                </td>
                                <td class="px-8 py-6 text-center text-sm font-black text-indigo-400">{{ $team->athletes_count }}</td>
                                <td class="px-8 py-6 text-right">
                                    <a href="{{ route('admin.teams.show', $team) }}" class="px-4 py-2 bg-white/5 text-gray-400 hover:text-white hover:bg-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Ver Elenco →</a>
                                </td>
                            </tr>
                            @endforeach
                            @if($teams->isEmpty())
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center text-gray-500 text-xs font-bold uppercase tracking-widest">Nenhuma equipe vinculada a este treinador.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Certificates -->
            <div class="bg-[#0F1423] rounded-[2.5rem] p-10 shadow-2xl border border-white/5">
                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-8 flex items-center justify-between">
                    Certificações
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </h3>
                
                <div class="space-y-4">
                    @forelse($coach->certificates ?? [] as $cert)
                    <div class="flex items-center justify-between p-5 bg-white/[0.03] rounded-2xl border border-white/5 group hover:bg-white/[0.06] transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-500/10 text-indigo-400 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-white uppercase tracking-widest truncate max-w-[150px]">{{ $cert['name'] }}</p>
                                <p class="text-[8px] text-gray-500 font-bold uppercase tracking-widest mt-1">{{ \Carbon\Carbon::parse($cert['date'])->translatedFormat('d M, Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('tenant.assets', ['path' => $cert['path']]) }}" target="_blank" class="p-2 text-gray-500 hover:text-indigo-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </div>
                    @empty
                    <div class="text-center py-10 opacity-30">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Nenhum certificado</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Financial Alert Box -->
            <div class="bg-indigo-600 rounded-[2.5rem] p-10 shadow-2xl shadow-indigo-600/30 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 blur-[40px] rounded-full"></div>
                <h4 class="text-white text-lg font-black tracking-tight mb-2">Resumo Financeiro</h4>
                <p class="text-white/60 text-xs font-bold uppercase tracking-widest mb-6">Próximo pagamento: --</p>
                
                <div class="space-y-4 mb-8">
                    <div class="flex items-center justify-between text-white/80">
                        <span class="text-[10px] font-black uppercase tracking-widest">Ganhos Totais</span>
                        <span class="text-sm font-black tracking-tight">R$ --</span>
                    </div>
                    <div class="flex items-center justify-between text-white/80">
                        <span class="text-[10px] font-black uppercase tracking-widest">Bônus Pendentes</span>
                        <span class="text-sm font-black tracking-tight">R$ --</span>
                    </div>
                </div>
                
                <a href="{{ route('admin.coaches.pay', $coach->id) }}" class="w-full flex items-center justify-center py-4 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-[1.02] transition-all">Realizar Pagamento</a>
            </div>
        </div>
    </div>
</div>
@endsection

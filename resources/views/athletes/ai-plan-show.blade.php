@extends('layouts.app')

@section('header')
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.athletes.show', $athlete) }}#ai-plans" class="p-2 bg-white/10 backdrop-blur-md border border-white/10 rounded-xl hover:bg-white/20 transition-all shadow-lg text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-black text-white uppercase italic tracking-tight leading-tight">{{ $plan->title }}</h2>
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-400">Atleta: {{ $athlete->full_name }} | Gerado em: {{ $plan->generated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-6xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    <!-- Action Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-[#0f172a] p-6 rounded-[2rem] border border-white/10 shadow-2xl">
        <div class="flex items-center space-x-6">
            <div class="text-center">
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Status</p>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $plan->status === 'active' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-gray-500/20 text-gray-400 border border-gray-500/30' }}">
                    {{ $plan->status === 'active' ? 'Ativo' : 'Concluído' }}
                </span>
            </div>
            <div class="text-center">
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Tipo</p>
                <p class="text-xs font-bold text-white uppercase">{{ $plan->type === 'workout_plan' ? 'Treino' : 'Nutrição' }}</p>
            </div>
            <div class="text-center">
                <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Duração</p>
                <p class="text-xs font-bold text-white uppercase">{{ $plan->duration ?? '30 Dias' }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="openEditModal()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg flex items-center">
                <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                EDITAR PLANO
            </button>
            <button onclick="toggleSuspend()" class="px-6 py-2.5 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
                {{ $plan->status === 'active' ? 'SUSPENDER' : 'REATIVAR' }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Analysis -->
            <div class="bg-[#0f172a] p-8 rounded-[2.5rem] border border-white/10 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="w-32 h-32 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                </div>
                <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-6 flex items-center">
                    <span class="w-8 h-px bg-blue-500/50 mr-3"></span>
                    Análise da Inteligência Artificial
                </h3>
                <div class="prose prose-invert prose-sm max-w-none text-gray-300 leading-relaxed italic">
                    {!! nl2br(e($plan->content['description'] ?? '')) !!}
                </div>
            </div>

            <!-- List -->
            <div class="bg-[#0f172a] rounded-[2.5rem] border border-white/10 shadow-xl overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 bg-white/5 flex justify-between items-center">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest">
                        {{ $plan->type === 'workout_plan' ? 'Cronograma de Exercícios' : 'Plano de Refeições' }}
                    </h3>
                </div>
                
                <div class="divide-y divide-white/5">
                    @if($plan->type === 'workout_plan')
                        @foreach($plan->content['exercises'] ?? [] as $exercise)
                            <div class="p-8 hover:bg-white/[0.02] transition-colors group">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors">{{ $exercise['name'] }}</h4>
                                        <p class="text-xs text-gray-500 mt-2 leading-relaxed max-w-xl">{{ $exercise['description'] }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-lg font-black text-[9px] uppercase border border-blue-500/20">{{ $exercise['sets'] }} Séries</span>
                                        <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 rounded-lg font-black text-[9px] uppercase border border-indigo-500/20">{{ $exercise['reps'] }} Reps</span>
                                    </div>
                                </div>
                                <div class="flex items-center text-[10px] text-gray-500 font-black uppercase tracking-wider">
                                    <svg class="w-3 h-3 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Descanso: {{ $exercise['rest'] ?? 'N/A' }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach($plan->content['meals'] ?? [] as $meal)
                            <div class="p-8 hover:bg-white/[0.02] transition-colors group">
                                <div class="flex flex-col md:flex-row gap-8">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <h4 class="text-lg font-bold text-white group-hover:text-green-400 transition-colors">{{ $meal['name'] }}</h4>
                                                <div class="flex flex-wrap gap-2 mt-3">
                                                    @foreach($meal['foods'] ?? [] as $food)
                                                        <span class="px-2 py-0.5 bg-white/5 text-gray-400 rounded text-[8px] font-black uppercase border border-white/10">{{ $food }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <span class="px-3 py-1 bg-green-500/10 text-green-400 rounded-lg font-black text-[9px] uppercase border border-green-500/20">{{ $meal['time'] }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 italic leading-relaxed mb-4">{{ $meal['description'] ?? '' }}</p>
                                        <div class="flex gap-6">
                                            <div class="text-center">
                                                <p class="text-[8px] font-black text-gray-500 uppercase mb-1 tracking-widest">Calorias</p>
                                                <p class="text-xs font-bold text-white">{{ $meal['calories'] ?? '--' }} kcal</p>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-[8px] font-black text-gray-500 uppercase mb-1 tracking-widest">Proteína</p>
                                                <p class="text-xs font-bold text-white">{{ $meal['protein'] ?? '--' }}g</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Goal & Frequency -->
            <div class="bg-[#0f172a] p-8 rounded-[2.5rem] border border-white/10 shadow-xl">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6">Configurações Técnicas</h3>
                <div class="space-y-4">
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <p class="text-[8px] font-black text-gray-500 uppercase mb-1 tracking-widest">Objetivo Principal</p>
                        <p class="text-sm font-bold text-blue-400">{{ $plan->goal ?? 'Alta Performance' }}</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-2xl border border-white/10">
                        <p class="text-[8px] font-black text-gray-500 uppercase mb-1 tracking-widest">Frequência Semanal</p>
                        <p class="text-sm font-bold text-white">{{ $plan->frequency ?? '5x por semana' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pro Tips -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden">
                <div class="absolute -top-4 -right-4 opacity-10">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.68a1 1 0 10-1.404-1.427l-.707.707a1 1 0 101.414 1.414l.707-.707zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zM12.95 14.364l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 101.414 1.414zM11 17a1 1 0 10-2 0v-1a1 1 0 102 0v1zM4.343 12.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zM8.586 7a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414L8.586 7zM14.5 10a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"></path></svg>
                </div>
                <h3 class="text-xs font-black uppercase tracking-widest mb-6 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.68a1 1 0 10-1.404-1.427l-.707.707a1 1 0 101.414 1.414l.707-.707zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zM12.95 14.364l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 101.414 1.414zM11 17a1 1 0 10-2 0v-1a1 1 0 102 0v1zM4.343 12.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zM8.586 7a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414L8.586 7zM14.5 10a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z"></path></svg>
                    Dicas do Coach Pro Max
                </h3>
                <ul class="space-y-4">
                    @foreach($plan->content['tips'] ?? [] as $tip)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 mr-3 text-blue-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-medium leading-relaxed">{{ $tip }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- WhatsApp Notifications -->
            <div class="bg-[#0f172a] p-8 rounded-[2.5rem] border border-white/10 shadow-xl">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-4.821 4.754a9.624 9.624 0 0 1-4.77-1.256l-.342-.205-3.53.926.942-3.443-.226-.359a9.615 9.615 0 0 1-1.472-5.118c0-5.31 4.316-9.63 9.638-9.63 2.57 0 4.986 1.002 6.804 2.822 1.816 1.82 2.822 4.237 2.822 6.809 0 5.314-4.322 9.636-9.636 9.636m7.412-17.042A10.667 10.667 0 0 0 12.651 0C6.674 0 1.812 4.864 1.812 10.838c0 1.91.49 3.776 1.419 5.438L0 23.33l7.391-1.94a10.635 10.635 0 0 0 5.257 1.393h.005c5.975 0 10.838-4.865 10.838-10.838a10.655 10.655 0 0 0-3.08-7.427"></path></svg>
                    Alertas Ativos
                </h3>
                <div class="space-y-3">
                    @foreach($plan->notification_settings ?? [] as $time)
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl border border-white/10">
                            <span class="text-xs font-bold text-gray-400">Lembrete</span>
                            <span class="px-3 py-1 bg-blue-500/10 border border-blue-500/20 rounded-lg text-[10px] font-black text-blue-400 shadow-sm">{{ $time }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição (Simplified JSON Editor or structured form) -->
<div id="editPlanModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
    <div class="bg-[#0f172a] border border-white/10 w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
        <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/5">
            <div>
                <h3 class="text-white font-black uppercase text-xs tracking-widest mb-1">Editar Plano Gerado</h3>
                <p class="text-[10px] text-gray-500 font-medium">As alterações serão salvas diretamente no JSON do plano.</p>
            </div>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-8 max-h-[70vh] overflow-y-auto text-gray-300 custom-scrollbar">
            <form id="editPlanForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Título do Plano</label>
                        <input type="text" name="title" value="{{ $plan->title }}" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Objetivo</label>
                        <input type="text" name="goal" value="{{ $plan->goal }}" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Descrição / Análise IA</label>
                    <textarea name="description" rows="4" class="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-white focus:ring-blue-500 focus:border-blue-500 text-sm italic">{{ $plan->content['description'] ?? '' }}</textarea>
                </div>

                @if($plan->type === 'workout_plan')
                    <div>
                        <label class="block text-xs font-black text-white uppercase tracking-widest mb-4">Exercícios</label>
                        <div id="edit-exercises-container" class="space-y-4">
                            @foreach($plan->content['exercises'] ?? [] as $index => $ex)
                                <div class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-4">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="col-span-2">
                                            <input type="text" name="exercises[{{$index}}][name]" value="{{$ex['name']}}" placeholder="Nome do Exercício" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm font-bold">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" name="exercises[{{$index}}][sets]" value="{{$ex['sets']}}" placeholder="Séries" class="w-1/2 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                                            <input type="text" name="exercises[{{$index}}][reps]" value="{{$ex['reps']}}" placeholder="Reps" class="w-1/2 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                                        </div>
                                    </div>
                                    <textarea name="exercises[{{$index}}][description]" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white text-xs" placeholder="Descrição/Instruções">{{$ex['description']}}</textarea>
                                    <div class="flex items-center justify-between">
                                        <input type="text" name="exercises[{{$index}}][rest]" value="{{$ex['rest'] ?? ''}}" placeholder="Tempo de Descanso" class="w-1/3 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-[10px]">
                                        <button type="button" onclick="this.closest('div.bg-white\\/5').remove()" class="text-red-500 text-[10px] font-black uppercase">Remover</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addExercise()" class="mt-4 w-full py-3 border-2 border-dashed border-white/10 rounded-2xl text-gray-500 hover:text-white hover:border-white/20 transition-all font-black text-[10px] uppercase tracking-widest">+ ADICIONAR EXERCÍCIO</button>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-black text-white uppercase tracking-widest mb-4">Refeições</label>
                        <div id="edit-meals-container" class="space-y-4">
                            @foreach($plan->content['meals'] ?? [] as $index => $meal)
                                <div class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <input type="text" name="meals[{{$index}}][name]" value="{{$meal['name']}}" placeholder="Nome da Refeição" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm font-bold">
                                        <input type="text" name="meals[{{$index}}][time]" value="{{$meal['time']}}" placeholder="Horário" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm">
                                    </div>
                                    <textarea name="meals[{{$index}}][description]" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white text-xs" placeholder="Descrição">${meal['description']}</textarea>
                                    <div class="grid grid-cols-2 gap-4">
                                        <input type="number" name="meals[{{$index}}][calories]" value="{{$meal['calories'] ?? 0}}" placeholder="Calorias" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                                        <input type="number" name="meals[{{$index}}][protein]" value="{{$meal['protein'] ?? 0}}" placeholder="Proteína" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                                    </div>
                                    <button type="button" onclick="this.closest('div.bg-white\\/5').remove()" class="text-red-500 text-[10px] font-black uppercase">Remover Refeição</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addMeal()" class="mt-4 w-full py-3 border-2 border-dashed border-white/10 rounded-2xl text-gray-500 hover:text-white hover:border-white/20 transition-all font-black text-[10px] uppercase tracking-widest">+ ADICIONAR REFEIÇÃO</button>
                    </div>
                @endif
            </form>
        </div>
        <div class="p-8 border-t border-white/5 bg-white/5 flex justify-end space-x-3">
            <button onclick="closeEditModal()" class="px-6 py-2 text-xs font-black text-gray-500 uppercase tracking-widest">Cancelar</button>
            <button onclick="savePlan()" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl transition-all">SALVAR ALTERAÇÕES</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal() {
        document.getElementById('editPlanModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editPlanModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function addExercise() {
        const container = document.getElementById('edit-exercises-container');
        const index = container.children.length;
        const html = `
            <div class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-4 animate-in slide-in-from-right-4 duration-300">
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <input type="text" name="exercises[${index}][name]" placeholder="Nome do Exercício" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm font-bold">
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="exercises[${index}][sets]" placeholder="Séries" class="w-1/2 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                        <input type="text" name="exercises[${index}][reps]" placeholder="Reps" class="w-1/2 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                    </div>
                </div>
                <textarea name="exercises[${index}][description]" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white text-xs" placeholder="Descrição/Instruções"></textarea>
                <div class="flex items-center justify-between">
                    <input type="text" name="exercises[${index}][rest]" placeholder="Tempo de Descanso" class="w-1/3 bg-white/5 border border-white/10 rounded-lg p-2 text-white text-[10px]">
                    <button type="button" onclick="this.closest('div.bg-white\\/5').remove()" class="text-red-500 text-[10px] font-black uppercase">Remover</button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function addMeal() {
        const container = document.getElementById('edit-meals-container');
        const index = container.children.length;
        const html = `
            <div class="bg-white/5 p-6 rounded-2xl border border-white/10 space-y-4 animate-in slide-in-from-right-4 duration-300">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="meals[${index}][name]" placeholder="Nome da Refeição" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm font-bold">
                    <input type="text" name="meals[${index}][time]" placeholder="Horário" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-sm">
                </div>
                <textarea name="meals[${index}][description]" rows="2" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white text-xs" placeholder="Descrição"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="meals[${index}][calories]" value="0" placeholder="Calorias" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                    <input type="number" name="meals[${index}][protein]" value="0" placeholder="Proteína" class="w-full bg-white/5 border border-white/10 rounded-lg p-2 text-white text-xs">
                </div>
                <button type="button" onclick="this.closest('div.bg-white\\/5').remove()" class="text-red-500 text-[10px] font-black uppercase">Remover Refeição</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    function savePlan() {
        const form = document.getElementById('editPlanForm');
        const formData = new FormData(form);
        const data = {
            title: formData.get('title'),
            goal: formData.get('goal'),
            content: {
                description: formData.get('description'),
                exercises: [],
                meals: []
            },
            notification_settings: [] // Logic to collect from existing checkboxes or similar
        };

        // Collect Exercises
        const exercises = [];
        @if($plan->type === 'workout_plan')
            form.querySelectorAll('#edit-exercises-container > div').forEach((div, i) => {
                exercises.push({
                    name: div.querySelector(`input[name*="[name]"]`).value,
                    sets: div.querySelector(`input[name*="[sets]"]`).value,
                    reps: div.querySelector(`input[name*="[reps]"]`).value,
                    description: div.querySelector(`textarea[name*="[description]"]`).value,
                    rest: div.querySelector(`input[name*="[rest]"]`).value
                });
            });
            data.content.exercises = exercises;
        @else
            const meals = [];
            form.querySelectorAll('#edit-meals-container > div').forEach((div, i) => {
                meals.push({
                    name: div.querySelector(`input[name*="[name]"]`).value,
                    time: div.querySelector(`input[name*="[time]"]`).value,
                    description: div.querySelector(`textarea[name*="[description]"]`).value,
                    calories: div.querySelector(`input[name*="[calories]"]`).value,
                    protein: div.querySelector(`input[name*="[protein]"]`).value
                });
            });
            data.content.meals = meals;
        @endif

        fetch('{{ route("athletes.ai-plans.update", ["athlete" => $athlete->id, "plan" => $plan->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                location.reload();
            } else {
                alert('Erro: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao salvar alterações.');
        });
    }

    function toggleSuspend() {
        if (!confirm('Deseja alterar o status deste plano?')) return;
        
        fetch('{{ route("athletes.ai-plans.toggle-suspend", ["athlete" => $athlete->id, "plan" => $plan->id]) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        });
    }
</script>
@endpush

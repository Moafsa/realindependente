<div class="space-y-8">
    <!-- Header com Botões de Ação -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-blue-600 to-indigo-700 p-6 rounded-2xl shadow-lg text-white">
        <div>
            <h3 class="text-xl font-bold">Planos Inteligentes (IA)</h3>
            <p class="text-blue-100 text-sm">Gere treinamentos e dietas personalizadas baseadas na performance do atleta.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openAiPlanModal('workout_plan')" class="px-4 py-2 bg-white text-blue-700 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                NOVO TREINO
            </button>
            <button onclick="openAiPlanModal('meal_plan')" class="px-4 py-2 bg-blue-500 text-white border border-blue-400 rounded-xl font-bold text-sm hover:bg-blue-400 transition-all flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                NOVA DIETA
            </button>
        </div>
    </div>

    <!-- Filtros de Visualização -->
    <div class="flex items-center space-x-4 border-b pb-1">
        <button onclick="filterPlans('all')" class="plan-filter-btn px-4 py-2 text-sm font-bold border-b-2 border-blue-600 text-blue-600" id="filter-all">Todos os Planos</button>
        <button onclick="filterPlans('active')" class="plan-filter-btn px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" id="filter-active">Ativos</button>
        <button onclick="filterPlans('history')" class="plan-filter-btn px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700" id="filter-history">Histórico</button>
    </div>

    <!-- Lista de Planos -->
    <div id="ai-plans-container" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($ai_plans as $plan)
            <div class="plan-card bg-white border rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 group {{ $plan->status === 'active' ? 'border-blue-200 ring-1 ring-blue-100' : '' }}" data-status="{{ $plan->status }}">
                <div class="p-5">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            <div class="p-2 rounded-lg {{ $plan->type === 'workout_plan' ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600' }}">
                                @if($plan->type === 'workout_plan')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h4 class="font-bold text-gray-900">{{ $plan->title }}</h4>
                                <p class="text-xs text-gray-500 font-medium">{{ $plan->generated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $plan->status === 'active' ? 'bg-green-100 text-green-700' : ($plan->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                            @if($plan->status === 'active')
                                Ativo
                            @elseif($plan->status === 'pending')
                                Solicitação Pendente
                            @else
                                Concluído
                            @endif
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 line-clamp-2 mb-4 italic">
                        "{{ $plan->content['description'] ?? 'Sem descrição' }}"
                    </p>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 p-2 rounded-lg text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Objetivo</p>
                            <p class="text-xs font-bold text-gray-700">{{ $plan->goal ?? 'Geral' }}</p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded-lg text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Notificações</p>
                            <p class="text-xs font-bold text-gray-700">
                                @if(is_array($plan->notification_settings))
                                    {{ implode(', ', $plan->notification_settings) }}
                                @else
                                    Nenhuma
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t">
                        <div class="flex -space-x-2">
                            <span class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">AI</span>
                        </div>
                        @if($plan->status === 'pending')
                            <button onclick="openCoachInterventionModal({{ $plan->id }})" class="px-4 py-1.5 bg-yellow-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-yellow-600 transition-all flex items-center shadow-sm">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Editar e Gerar
                            </button>
                        @else
                            <div class="flex items-center space-x-2">
                                <button onclick="openViewPlanModal({{ $plan->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase tracking-widest flex items-center">
                                    Ver Detalhes
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </button>
                                
                                <div class="h-4 w-px bg-gray-200 mx-1"></div>
                                
                                <button onclick="toggleSuspendPlan({{ $plan->id }})" class="{{ $plan->status === 'suspended' ? 'text-green-600 hover:text-green-800' : 'text-amber-600 hover:text-amber-800' }} text-[10px] font-bold uppercase tracking-widest">
                                    {{ $plan->status === 'suspended' ? 'Ativar' : 'Suspender' }}
                                </button>
                                
                                <button onclick="deletePlan({{ $plan->id }})" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        @endif

                        <!-- Data Store for this plan (Hidden) -->
                        <script type="application/json" id="plan-data-{{ $plan->id }}">
                            {!! json_encode([
                                'id' => $plan->id,
                                'title' => $plan->title,
                                'type' => $plan->type,
                                'status' => $plan->status,
                                'goal' => $plan->goal,
                                'duration' => $plan->duration ?? '30 dias',
                                'frequency' => ucfirst(str_replace('_', ' ', $plan->frequency ?? '3x por semana')),
                                'description' => $plan->content['description'] ?? '',
                                'exercises' => $plan->content['exercises'] ?? [],
                                'meals' => $plan->content['meals'] ?? [],
                                'tips' => $plan->content['tips'] ?? [],
                                'notifications' => $plan->notification_settings ?? [],
                                'generated_at' => $plan->generated_at->format('d/m/Y H:i'),
                                'last_edited_by_name' => $plan->editor->name ?? null,
                                'edited_at' => $plan->edited_at ? $plan->edited_at->format('d/m/Y H:i') : null
                            ]) !!}
                        </script>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <h4 class="text-lg font-bold text-gray-900">Nenhum Plano Inteligente</h4>
                <p class="text-gray-500 max-w-xs mx-auto mb-6">Use a inteligência artificial para criar rotinas personalizadas para o atleta.</p>
                <button onclick="openAiPlanModal('workout_plan')" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition-all">GERAR MEU PRIMEIRO PLANO</button>
            </div>
        @endforelse
    <!-- Modal de Visualização de Detalhes do Plano -->
    <div id="viewPlanModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-[#0f172a] border border-white/10 w-full max-w-5xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
            <!-- Header -->
            <div class="px-8 py-6 border-b border-white/5 bg-white/5 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-black text-white uppercase italic tracking-tight" id="view-plan-title">Título do Plano</h3>
                    <p class="text-[10px] font-black uppercase tracking-widest text-blue-400" id="view-plan-meta">Gerado em: --/--/----</p>
                </div>
                <button type="button" onclick="closeViewPlanModal()" class="p-2 bg-white/5 border border-white/10 rounded-xl text-gray-500 hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body -->
            <div class="px-8 py-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                <div class="space-y-8">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10 text-center">
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Status</p>
                            <span id="view-plan-status" class="px-3 py-1 rounded-full text-[9px] font-black uppercase">--</span>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10 text-center">
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Duração</p>
                            <p id="view-plan-duration" class="text-xs font-bold text-white">--</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10 text-center">
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Frequência</p>
                            <p id="view-plan-frequency" class="text-xs font-bold text-white">--</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/10 text-center">
                            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-1">Objetivo</p>
                            <p id="view-plan-goal" class="text-xs font-bold text-blue-400">--</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-8">
                            <!-- Analysis -->
                            <div class="bg-white/5 p-6 rounded-2xl border border-white/10">
                                <h4 class="text-[10px] font-black text-blue-400 mb-4 uppercase tracking-widest flex items-center">
                                    <span class="w-6 h-px bg-blue-500/50 mr-2"></span>
                                    Análise do Especialista
                                </h4>
                                <div id="view-plan-description" class="text-xs text-gray-300 leading-relaxed italic prose prose-invert max-w-none">
                                    --
                                </div>
                            </div>

                            <!-- Dynamic List -->
                            <div class="bg-white/5 rounded-2xl border border-white/10 overflow-hidden">
                                <div class="px-6 py-4 bg-white/5 border-b border-white/5">
                                    <h4 class="text-[10px] font-black text-white uppercase tracking-widest" id="view-plan-list-title">Plano Detalhado</h4>
                                </div>
                                <div id="view-plan-list-container" class="divide-y divide-white/5">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-8">
                            <!-- Pro Tips -->
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-900 p-6 rounded-2xl shadow-xl text-white">
                                <h4 class="text-[10px] font-black mb-4 uppercase tracking-widest">Dicas Pro Max</h4>
                                <ul id="view-plan-tips" class="space-y-3">
                                    <!-- Populated by JS -->
                                </ul>
                            </div>

                            <!-- Notifications -->
                            <div class="bg-white/5 p-6 rounded-2xl border border-white/10">
                                <h4 class="text-[10px] font-black text-white mb-4 uppercase tracking-widest flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-4.821 4.754a9.624 9.624 0 0 1-4.77-1.256l-.342-.205-3.53.926.942-3.443-.226-.359a9.615 9.615 0 0 1-1.472-5.118c0-5.31 4.316-9.63 9.638-9.63 2.57 0 4.986 1.002 6.804 2.822 1.816 1.82 2.822 4.237 2.822 6.809 0 5.314-4.322 9.636-9.636 9.636m7.412-17.042A10.667 10.667 0 0 0 12.651 0C6.674 0 1.812 4.864 1.812 10.838c0 1.91.49 3.776 1.419 5.438L0 23.33l7.391-1.94a10.635 10.635 0 0 0 5.257 1.393h.005c5.975 0 10.838-4.865 10.838-10.838a10.655 10.655 0 0 0-3.08-7.427"></path></svg>
                                    WhatsApp
                                </h4>
                                <div id="view-plan-notifications" class="space-y-2">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-8 border-t border-white/5 bg-white/5 flex justify-end space-x-3">
                <a id="view-plan-full-link" href="#" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">GERENCIAR PLANO COMPLETO</a>
                <button onclick="closeViewPlanModal()" class="px-6 py-2.5 text-gray-500 hover:text-white font-black text-[10px] uppercase tracking-widest transition-all">Fechar</button>
            </div>
        </div>
    </div>
   </div>
    </div>

    <!-- Modal de Intervenção do Coach (Editar Solicitação) -->
    <div id="coachInterventionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-80" onclick="closeCoachInterventionModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <form id="coachInterventionForm" action="{{ route('admin.athletes.ai-plans.generate', $athlete) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="intervention-plan-type">
                    <input type="hidden" name="request_id" id="intervention-request-id">
                    
                    <div class="px-8 py-6 border-b bg-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Intervenção do Treinador</h3>
                            <p class="text-xs text-gray-500 font-medium">Personalize a solicitação do atleta antes de gerar.</p>
                        </div>
                        <button type="button" onclick="closeCoachInterventionModal()" class="p-2 bg-white border rounded-xl text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-8 py-8 space-y-6">
                        <div class="bg-blue-50 p-5 rounded-2xl border border-blue-100">
                            <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Solicitação Original do Atleta</label>
                            <p id="intervention-original-goal" class="text-sm text-blue-900 font-medium italic">--</p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Objetivo (Pode ser editado)</label>
                            <textarea name="goal" id="intervention-goal" required rows="2" class="w-full border-gray-200 rounded-2xl focus:ring-blue-500 focus:border-blue-500 text-sm p-4 bg-gray-50"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Instruções Adicionais do Treinador (Incrementar Prompt)</label>
                            <textarea name="coach_instructions" rows="4" placeholder="Ex: Priorize exercícios de explosão lateral. O atleta está se recuperando de uma leve fadiga no adutor, evite carga excessiva nos primeiros 3 dias..." class="w-full border-gray-200 rounded-2xl focus:ring-blue-500 focus:border-blue-500 text-sm p-4 bg-white shadow-inner"></textarea>
                            <p class="mt-2 text-[10px] text-gray-400">Estas instruções serão enviadas diretamente para a IA para refinar o plano.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Duração (Dias)</label>
                                <input type="number" name="duration" id="intervention-duration" value="30" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Frequência</label>
                                <input type="text" name="frequency" id="intervention-frequency" value="5x por semana" class="w-full border-gray-200 rounded-xl text-sm p-3 bg-gray-50">
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-gray-50 border-t flex justify-end space-x-3">
                        <button type="button" onclick="closeCoachInterventionModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest">DESCARTAR</button>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all flex items-center">
                            <span id="intervention-submit-text">GERAR PLANO REFINADO</span>
                            <svg id="intervention-submit-spinner" class="hidden w-4 h-4 ml-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Geração de Plano -->
    <div id="aiPlanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAiPlanModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="aiPlanForm" action="{{ route('admin.athletes.ai-plans.generate', $athlete) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" id="modal-plan-type">
                    
                    <div class="px-6 py-4 border-b bg-gray-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">Gerar Novo Plano</h3>
                            <button type="button" onclick="closeAiPlanModal()" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6 space-y-6">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                <strong>Inteligência Especialista:</strong> A IA analisará a performance atual do atleta e definirá automaticamente a <strong>Duração</strong> e <strong>Frequência</strong> ideais para este objetivo.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Objetivo do Plano</label>
                            <textarea name="goal" required rows="3" placeholder="Ex: Preciso de um plano para melhorar a explosão muscular do atleta, focando em treinos que ele possa fazer em casa..." class="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Horários Preferenciais (Opcional)</label>
                            <div class="grid grid-cols-3 gap-2" id="notification-times-container">
                                <input type="time" name="notifications[]" class="border-gray-300 rounded-lg text-xs p-2">
                                <button type="button" onclick="addNotificationTime()" class="border-2 border-dashed border-gray-300 rounded-lg text-gray-400 hover:text-gray-600 hover:border-gray-400 text-xs font-bold p-2">+</button>
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400">Se deixado em branco, a IA sugerirá os melhores horários técnicos.</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                        <button type="button" onclick="closeAiPlanModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">CANCELAR</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold shadow-lg hover:bg-blue-700 transition-all flex items-center">
                            <span id="submit-text">SOLICITAR À IA</span>
                            <svg id="submit-spinner" class="hidden w-4 h-4 ml-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openAiPlanModal(type) {
        document.getElementById('modal-plan-type').value = type;
        document.getElementById('modal-title').textContent = type === 'workout_plan' ? 'Gerar Plano de Treino IA' : 'Gerar Protocolo Nutricional IA';
        document.getElementById('aiPlanModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAiPlanModal() {
        document.getElementById('aiPlanModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openViewPlanModal(planId) {
        const data = JSON.parse(document.getElementById('plan-data-' + planId).textContent);
        
        // Basic Info
        document.getElementById('view-plan-title').textContent = data.title;
        document.getElementById('view-plan-meta').textContent = 'Gerado em: ' + data.generated_at;
        document.getElementById('view-plan-duration').textContent = data.duration;
        document.getElementById('view-plan-frequency').textContent = data.frequency;
        document.getElementById('view-plan-goal').textContent = data.goal;
        document.getElementById('view-plan-description').innerHTML = data.description;
        
        // Status Badge
        const statusEl = document.getElementById('view-plan-status');
        statusEl.textContent = data.status === 'active' ? 'Ativo' : 'Concluído';
        statusEl.className = 'px-3 py-1 rounded-full text-[10px] font-bold uppercase ' + 
                            (data.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600');

        // Dynamic List Title
        document.getElementById('view-plan-list-title').textContent = data.type === 'workout_plan' ? 'Cronograma de Exercícios' : 'Plano de Refeições';

        // Populate List
        const listContainer = document.getElementById('view-plan-list-container');
        listContainer.innerHTML = '';
        
        if (data.type === 'workout_plan') {
            data.exercises.forEach(ex => {
                listContainer.innerHTML += `
                    <div class="p-6 hover:bg-blue-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h5 class="text-base font-bold text-gray-900">${ex.name}</h5>
                                <p class="text-xs text-gray-500 mt-1">${ex.description}</p>
                            </div>
                            <div class="flex gap-2">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg font-bold text-[10px]">${ex.sets} Séries</span>
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg font-bold text-[10px]">${ex.reps} Reps</span>
                            </div>
                        </div>
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Descanso: ${ex.rest || 'N/A'}</div>
                    </div>
                `;
            });
        } else {
            data.meals.forEach(meal => {
                let foodsHtml = (meal.foods || []).map(f => `<span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[9px] font-bold uppercase">${f}</span>`).join(' ');
                
                listContainer.innerHTML += `
                    <div class="p-6 hover:bg-green-50 transition-colors">
                        <div class="flex gap-4">
                            ${meal.image_url ? `<div class="w-20 h-20 rounded-xl overflow-hidden shadow-sm flex-shrink-0"><img src="${meal.image_url}" class="w-full h-full object-cover"></div>` : ''}
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-1">
                                    <h5 class="text-base font-bold text-gray-900">${meal.name}</h5>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg font-bold text-[10px]">${meal.time}</span>
                                </div>
                                <div class="flex flex-wrap gap-1 mb-2">${foodsHtml}</div>
                                <p class="text-xs text-gray-500 italic">${meal.description || ''}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // Populate Tips
        const tipsContainer = document.getElementById('view-plan-tips');
        tipsContainer.innerHTML = '';
        data.tips.forEach(tip => {
            tipsContainer.innerHTML += `
                <li class="flex items-start">
                    <svg class="w-4 h-4 mr-2 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-xs font-medium leading-tight text-blue-50">${tip}</span>
                </li>
            `;
        });

        // Populate Notifications
        const notifContainer = document.getElementById('view-plan-notifications');
        notifContainer.innerHTML = '';
        data.notifications.forEach(time => {
            notifContainer.innerHTML += `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                    <span class="text-[10px] font-bold text-gray-700">Lembrete</span>
                    <span class="px-2 py-0.5 bg-white border rounded text-[10px] font-black text-blue-600">${time}</span>
                </div>
            `;
        });

        document.getElementById('viewPlanModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeViewPlanModal() {
        document.getElementById('viewPlanModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function addNotificationTime() {
        const container = document.getElementById('notification-times-container');
        const input = document.createElement('input');
        input.type = 'time';
        input.name = 'notifications[]';
        input.className = 'border-gray-300 rounded-lg text-xs p-2';
        container.insertBefore(input, container.lastElementChild);
    }

    function filterPlans(status) {
        document.querySelectorAll('.plan-filter-btn').forEach(btn => {
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        
        document.getElementById('filter-' + status).classList.add('border-blue-600', 'text-blue-600');
        document.getElementById('filter-' + status).classList.remove('border-transparent', 'text-gray-500');

        document.querySelectorAll('.plan-card').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function openCoachInterventionModal(planId) {
        const data = JSON.parse(document.getElementById('plan-data-' + planId).textContent);
        
        document.getElementById('intervention-plan-type').value = data.type;
        document.getElementById('intervention-request-id').value = data.id;
        document.getElementById('intervention-original-goal').textContent = data.goal || 'Não especificado pelo atleta';
        document.getElementById('intervention-goal').value = data.goal || '';
        
        document.getElementById('coachInterventionModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCoachInterventionModal() {
        document.getElementById('coachInterventionModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.getElementById('coachInterventionForm').addEventListener('submit', function(e) {
        document.getElementById('intervention-submit-text').textContent = 'IA ESTÁ PROCESSANDO...';
        document.getElementById('intervention-submit-spinner').classList.remove('hidden');
    });

    document.getElementById('aiPlanForm').addEventListener('submit', function(e) {
        document.getElementById('submit-text').textContent = 'IA ESTÁ PENSANDO...';
        document.getElementById('submit-spinner').classList.remove('hidden');
    });

    function toggleSuspendPlan(planId) {
        if (!confirm('Deseja alterar o status deste plano?')) return;

        const athleteId = '{{ $athlete->id }}';
        fetch(`/athletes/${athleteId}/ai-plans/${planId}/toggle-suspend`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao processar solicitação');
        });
    }

    function deletePlan(planId) {
        if (!confirm('TEM CERTEZA? Esta ação não pode ser desfeita.')) return;

        const athleteId = '{{ $athlete->id }}';
        fetch(`/athletes/${athleteId}/ai-plans/${planId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao excluir plano');
        });
    }
</script>

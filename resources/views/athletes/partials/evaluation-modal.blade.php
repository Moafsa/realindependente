<!-- Evaluation Modal -->
<div id="evaluationModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('admin.athletes.evaluate', $athlete) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6 uppercase tracking-tight">Avaliação Profissional de Desempenho</h3>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data da Avaliação</label>
                                <input type="date" name="recorded_at" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Antropometria -->
                                <div class="md:col-span-2 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-1">Antropometria & Medidas</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Peso (kg)</label>
                                            <input type="number" step="0.1" name="metrics[Peso]" value="{{ $athlete->weight }}" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Altura (cm)</label>
                                            <input type="number" step="0.1" name="metrics[Altura]" value="{{ $athlete->height }}" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Cintura (cm)</label>
                                            <input type="number" step="0.1" name="metrics[Cintura]" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Bíceps (cm)</label>
                                            <input type="number" step="0.1" name="metrics[Bíceps]" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Coxa (cm)</label>
                                            <input type="number" step="0.1" name="metrics[Coxa]" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Panturrilha (cm)</label>
                                            <input type="number" step="0.1" name="metrics[Panturrilha]" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Biotipo / Genética</label>
                                            <select name="metrics[Biotipo]" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500">
                                                <option value="">Selecione...</option>
                                                <option value="Ectomorfo">Ectomorfo (Magro/Longilíneo)</option>
                                                <option value="Mesomorfo">Mesomorfo (Atlético/Musculoso)</option>
                                                <option value="Endomorfo">Endomorfo (Largo/Tendência a acumular)</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">% Gordura Corporal (Opcional)</label>
                                            <input type="number" step="0.01" name="metrics[% Gordura]" 
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500" placeholder="Ex: 12.5">
                                        </div>
                                    </div>
                                </div>
                                <!-- Technical -->
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-1">Habilidades Técnicas</h4>
                                    @foreach(['Passe', 'Finalização', 'Drible', 'Controle', 'Marcação'] as $metric)
                                    <div class="mb-4">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-semibold text-gray-700">{{ $metric }}</span>
                                            <span id="val-{{ \Illuminate\Support\Str::slug($metric) }}" class="text-sm font-bold text-blue-600">70</span>
                                        </div>
                                        <input type="range" name="metrics[{{ $metric }}]" min="0" max="100" value="70" 
                                               oninput="document.getElementById('val-{{ \Illuminate\Support\Str::slug($metric) }}').innerText = this.value"
                                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Physical -->
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-1">Capacidade Física</h4>
                                    @foreach(['Velocidade', 'Resistência', 'Força', 'Agilidade', 'Explosão'] as $metric)
                                    <div class="mb-4">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-semibold text-gray-700">{{ $metric }}</span>
                                            <span id="val-{{ \Illuminate\Support\Str::slug($metric) }}" class="text-sm font-bold text-green-600">70</span>
                                        </div>
                                        <input type="range" name="metrics[{{ $metric }}]" min="0" max="100" value="70" 
                                               oninput="document.getElementById('val-{{ \Illuminate\Support\Str::slug($metric) }}').innerText = this.value"
                                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-600">
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Tactical & Mental -->
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-1">Tático & Mental</h4>
                                    @foreach(['Posicionamento', 'Visão de Jogo', 'Decisão', 'Foco', 'Trabalho em Equipe'] as $metric)
                                    <div class="mb-4">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-sm font-semibold text-gray-700">{{ $metric }}</span>
                                            <span id="val-{{ \Illuminate\Support\Str::slug($metric) }}" class="text-sm font-bold text-purple-600">70</span>
                                        </div>
                                        <input type="range" name="metrics[{{ $metric }}]" min="0" max="100" value="70" 
                                               oninput="document.getElementById('val-{{ \Illuminate\Support\Str::slug($metric) }}').innerText = this.value"
                                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600">
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-1">
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 border-b pb-1">Observações do Coach</h4>
                                    <textarea name="notes" rows="6" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Descreva os pontos fortes e o que o atleta precisa melhorar..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-2.5 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest">
                        Salvar Avaliação
                    </button>
                    <button type="button" onclick="toggleEvaluationModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleEvaluationModal() {
        const modal = document.getElementById('evaluationModal');
        modal.classList.toggle('hidden');
    }
</script>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Planos de IA Gerados</h3>
        <a href="{{ route('admin.athletes.ai-plans', $athlete) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            Ver todos →
        </a>
    </div>

    <div id="ai-plans-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- AI plans will be loaded via AJAX -->
        <div class="text-center py-8 text-gray-500 col-span-2">
            Carregando planos de IA...
        </div>
    </div>
</div>

<script>
    // Load AI plans
    fetch('{{ route("athletes.ai-plans", $athlete) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.plans && data.plans.length > 0) {
                const container = document.getElementById('ai-plans-list');
                container.innerHTML = data.plans.map(plan => `
                    <div class="bg-white border rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">${plan.type_label || plan.type}</h4>
                                <p class="text-sm text-gray-500">${plan.generated_at}</p>
                            </div>
                            ${plan.is_favorite ? '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>' : ''}
                        </div>
                        <p class="text-sm text-gray-600 line-clamp-3">${plan.content_preview || 'Plano gerado por IA'}</p>
                        <div class="mt-4">
                            <a href="/athletes/{{ $athlete->id }}/ai-plans/${plan.id}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ver detalhes →
                            </a>
                        </div>
                    </div>
                `).join('');
            } else {
                document.getElementById('ai-plans-list').innerHTML = '<div class="text-center py-8 text-gray-500 col-span-2">Nenhum plano de IA gerado ainda</div>';
            }
        })
        .catch(error => {
            console.error('Error loading AI plans:', error);
            document.getElementById('ai-plans-list').innerHTML = '<div class="text-center py-8 text-red-500 col-span-2">Erro ao carregar planos de IA</div>';
        });
</script>


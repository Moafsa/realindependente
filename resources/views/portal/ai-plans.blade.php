@extends('layouts.portal')

@section('title', 'Planos de IA')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Planos de IA</h1>
            <p class="text-gray-600">Planos personalizados de treino e nutrição</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="generatePlan('workout_plan')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                Novo Treino
            </button>
            <button onclick="generatePlan('meal_plan')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Nova Nutrição
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="flex space-x-4">
            <button onclick="filterContent('all')" class="filter-btn active px-4 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-800">
                Todos
            </button>
            <button onclick="filterContent('workout_plan')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">
                Treinos
            </button>
            <button onclick="filterContent('meal_plan')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">
                Nutrição
            </button>
            <button onclick="filterContent('favorites')" class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">
                Favoritos
            </button>
        </div>
    </div>

    <!-- AI Content Grid -->
    <div id="ai-content-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($aiContent as $content)
        <div class="ai-content-item bg-white rounded-lg shadow-md overflow-hidden" data-type="{{ $content->type }}" data-favorite="{{ $content->is_favorite ? 'true' : 'false' }}">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $content->type === 'workout_plan' ? 'bg-green-100' : 'bg-blue-100' }}">
                            @if($content->type === 'workout_plan')
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            @else
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $content->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $content->generated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    <button onclick="toggleFavorite({{ $content->id }})" class="favorite-btn p-2 text-gray-400 hover:text-yellow-500 {{ $content->is_favorite ? 'text-yellow-500' : '' }}">
                        <svg class="h-5 w-5" fill="{{ $content->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($content->summary, 100) }}</p>
                
                @if($content->type === 'workout_plan')
                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                    <span>⏱️ {{ $content->duration ?? 'N/A' }}</span>
                    <span>💪 {{ $content->difficulty ?? 'N/A' }}</span>
                </div>
                @else
                <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                    <span>🔥 {{ $content->calories ?? 'N/A' }} cal</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $content->type === 'workout_plan' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $content->type === 'workout_plan' ? 'Treino' : 'Nutrição' }}
                    </span>
                    <button onclick="viewContent({{ $content->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver Detalhes
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum plano encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">Comece gerando seu primeiro plano personalizado.</p>
            <div class="mt-6 flex justify-center space-x-2">
                <button onclick="generatePlan('workout_plan')" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    Novo Treino
                </button>
                <button onclick="generatePlan('meal_plan')" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Nova Nutrição
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($aiContent->hasPages())
    <div class="flex justify-center">
        {{ $aiContent->links() }}
    </div>
    @endif
</div>

<!-- Content Modal -->
<div id="contentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Detalhes do Plano</h3>
                <button onclick="closeContentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="contentDetails" class="max-h-96 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
let currentFilter = 'all';

function generatePlan(type) {
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Gerando...';
    button.disabled = true;

    fetch(`/portal/ai-plans/generate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao gerar plano');
    })
    .finally(() => {
        button.textContent = originalText;
        button.disabled = false;
    });
}

function filterContent(filter) {
    currentFilter = filter;
    
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
        btn.classList.add('text-gray-600', 'hover:text-gray-900');
    });
    event.target.classList.add('active', 'bg-blue-100', 'text-blue-800');
    event.target.classList.remove('text-gray-600', 'hover:text-gray-900');
    
    // Filter content
    const items = document.querySelectorAll('.ai-content-item');
    items.forEach(item => {
        const type = item.dataset.type;
        const isFavorite = item.dataset.favorite === 'true';
        
        let show = false;
        if (filter === 'all') {
            show = true;
        } else if (filter === 'workout_plan' && type === 'workout_plan') {
            show = true;
        } else if (filter === 'meal_plan' && type === 'meal_plan') {
            show = true;
        } else if (filter === 'favorites' && isFavorite) {
            show = true;
        }
        
        item.style.display = show ? 'block' : 'none';
    });
}

function toggleFavorite(contentId) {
    fetch(`/portal/ai-plans/${contentId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = event.target.closest('.favorite-btn');
            const svg = btn.querySelector('svg');
            const path = svg.querySelector('path');
            
            if (data.is_favorite) {
                btn.classList.add('text-yellow-500');
                path.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.remove('text-yellow-500');
                path.setAttribute('fill', 'none');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function viewContent(contentId) {
    fetch(`/portal/ai-plans/${contentId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('contentDetails').innerHTML = formatContent(data.data);
            document.getElementById('contentModal').classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function closeContentModal() {
    document.getElementById('contentModal').classList.add('hidden');
}

function formatContent(content) {
    let html = `<h2 class="text-xl font-bold mb-4">${content.title}</h2>`;
    html += `<p class="text-gray-600 mb-4">${content.description}</p>`;
    
    if (content.type === 'workout_plan') {
        html += `<div class="mb-4"><strong>Duração:</strong> ${content.duration}</div>`;
        html += `<div class="mb-4"><strong>Dificuldade:</strong> ${content.difficulty}</div>`;
        
        if (content.exercises && content.exercises.length > 0) {
            html += `<h3 class="text-lg font-semibold mb-2">Exercícios:</h3>`;
            html += `<ul class="list-disc list-inside space-y-2">`;
            content.exercises.forEach(exercise => {
                html += `<li><strong>${exercise.name}</strong> - ${exercise.description}</li>`;
            });
            html += `</ul>`;
        }
    } else {
        html += `<div class="mb-4"><strong>Calorias:</strong> ${content.calories}</div>`;
        
        if (content.meals && content.meals.length > 0) {
            html += `<h3 class="text-lg font-semibold mb-2">Refeições:</h3>`;
            content.meals.forEach(meal => {
                html += `<div class="mb-2"><strong>${meal.name}</strong> (${meal.time}) - ${meal.description}</div>`;
            });
        }
    }
    
    if (content.tips && content.tips.length > 0) {
        html += `<h3 class="text-lg font-semibold mb-2">Dicas:</h3>`;
        html += `<ul class="list-disc list-inside space-y-1">`;
        content.tips.forEach(tip => {
            html += `<li>${tip}</li>`;
        });
        html += `</ul>`;
    }
    
    return html;
}
</script>
@endsection

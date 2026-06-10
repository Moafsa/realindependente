@extends('layouts.dashboard')

@section('title', 'Novo Post do Blog')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Novo Post</h1>
        <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Voltar</a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 flex">
            <button onclick="switchTab('manual')" id="tab-manual" class="px-6 py-4 font-semibold text-blue-600 border-b-2 border-blue-600 focus:outline-none flex-1 md:flex-none">
                Criar Manualmente
            </button>
            <button onclick="switchTab('ia')" id="tab-ia" class="px-6 py-4 font-semibold text-gray-500 hover:text-blue-600 border-b-2 border-transparent focus:outline-none flex flex-1 md:flex-none items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Modo IA PRO</span>
            </button>
        </div>

        <div class="p-6">
            <!-- ABA IA PRO -->
            <div id="content-ia" class="hidden space-y-6 bg-blue-50/50 p-6 rounded-lg border border-blue-100 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-blue-800">Gerador de Posts Inteligente</h2>
                    <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-800 rounded uppercase tracking-wider">SEO Otimizado</span>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">Preencha os campos abaixo e a IA criará um artigo estruturado (com H1, H2, negritos) seguindo boas práticas de SEO. Depois, você poderá revisar e salvar.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tema Principal</label>
                        <input type="text" id="ai_topic" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Benefícios do Treino Funcional">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Palavras-chave (Keywords SEO)</label>
                        <input type="text" id="ai_keywords" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: funcional, saúde, esporte, atleta">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instruções / Descrição</label>
                        <textarea id="ai_description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="O que não pode faltar neste texto? Cite pelo menos 3 benefícios..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tamanho do Artigo</label>
                        <select id="ai_word_count" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="500">Curto (~500 palavras) - Ideal para dicas rápidas</option>
                            <option value="1000" selected>Médio (~1000 palavras) - Bom para SEO geral</option>
                            <option value="1500">Longo (~1500 palavras) - Artigo aprofundado/Técnico</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <button type="button" onclick="generateAiPost()" id="btn-generate-ai" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <span>Gerar Post com Inteligência Artificial</span>
                    </button>
                </div>
            </div>

            <!-- FORMULÁRIO PRINCIPAL (Manual / Revisão da IA) -->
            <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Título do Post</label>
                                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 text-lg" value="{{ old('title') }}" required>
                            </div>

                            <div>
                                <label for="content" class="block text-sm font-bold text-gray-700 mb-1">Conteúdo (HTML Aceito)</label>
                                <textarea name="content" id="content" rows="15" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 font-mono text-sm" required>{{ old('content') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Dica: Use tags &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;strong&gt;, &lt;ul&gt; para formatar o texto e melhorar o SEO.</p>
                            </div>

                            <div>
                                <label for="excerpt" class="block text-sm font-bold text-gray-700 mb-1">Resumo (Excerpt)</label>
                                <textarea name="excerpt" id="excerpt" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('excerpt') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Um breve parágrafo que aparecerá na listagem do blog.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Status de Publicação</label>
                                <select name="status" id="status" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500 mb-4">
                                    <option value="draft">Rascunho</option>
                                    <option value="pending_approval">Aguardando Aprovação / Agendar</option>
                                    <option value="published">Publicar Imediatamente</option>
                                </select>

                                <label class="block text-sm font-bold text-gray-700 mb-1 mt-4">Imagem de Destaque</label>
                                <input type="file" name="image_url" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors">
                                        Salvar Post
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        const manualBtn = document.getElementById('tab-manual');
        const iaBtn = document.getElementById('tab-ia');
        const contentIa = document.getElementById('content-ia');

        if (tab === 'ia') {
            iaBtn.classList.replace('text-gray-500', 'text-blue-600');
            iaBtn.classList.replace('border-transparent', 'border-blue-600');
            manualBtn.classList.replace('text-blue-600', 'text-gray-500');
            manualBtn.classList.replace('border-blue-600', 'border-transparent');
            contentIa.classList.remove('hidden');
        } else {
            manualBtn.classList.replace('text-gray-500', 'text-blue-600');
            manualBtn.classList.replace('border-transparent', 'border-blue-600');
            iaBtn.classList.replace('text-blue-600', 'text-gray-500');
            iaBtn.classList.replace('border-blue-600', 'border-transparent');
            contentIa.classList.add('hidden');
        }
    }

    async function generateAiPost() {
        const topic = document.getElementById('ai_topic').value;
        const keywords = document.getElementById('ai_keywords').value;
        const description = document.getElementById('ai_description').value;
        const wordCount = document.getElementById('ai_word_count').value;

        if (!topic || !description) {
            alert('Por favor, preencha pelo menos o Tema e a Descrição para a IA.');
            return;
        }

        const btn = document.getElementById('btn-generate-ai');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando Mágica... Aguarde.';
        btn.disabled = true;

        try {
            const response = await fetch("{{ route('admin.posts.ai-generate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    topic: topic,
                    keywords: keywords,
                    description: description,
                    word_count: wordCount
                })
            });

            const result = await response.json();

            if (result.success) {
                // Populate the form fields
                document.getElementById('title').value = result.data.title;
                document.getElementById('content').value = result.data.content;
                document.getElementById('excerpt').value = result.data.excerpt;
                
                // Add keywords to description or excerpt if needed
                if(result.data.keywords) {
                    const currentExcerpt = document.getElementById('excerpt').value;
                    document.getElementById('excerpt').value = currentExcerpt + '\n\nTags SEO geradas: ' + result.data.keywords;
                }

                alert('Post gerado com sucesso! Agora você pode revisar o conteúdo e depois salvá-lo.');
                
                // Switch to manual mode so they can see the form filled out
                // switchTab('manual'); // We leave the IA pro visible so they see their inputs
            } else {
                alert(result.message || 'Erro ao gerar o post.');
            }
        } catch (error) {
            alert('Erro de conexão com o servidor da IA.');
            console.error(error);
        } finally {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    }
</script>
@endpush

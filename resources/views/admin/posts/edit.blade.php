@extends('layouts.dashboard')

@section('title', 'Editar Post')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <a href="{{ route('admin.posts.index') }}" class="hover:text-blue-600 transition">Blog</a>
            <span>/</span>
            <span class="text-gray-900 font-medium">Editar Post</span>
        </div>
        <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Voltar</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Editar Conteúdo do Post</h2>
            @if($post->status == 'pending_approval')
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Aguardando Aprovação</span>
            @elseif($post->status == 'scheduled')
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Agendado</span>
            @elseif($post->status == 'published')
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
            @endif
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Título</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-lg">
                    </div>

                    <div>
                        <label for="excerpt" class="block text-sm font-bold text-gray-700 mb-1">Resumo (Excerpt)</label>
                        <textarea id="excerpt" name="excerpt" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-bold text-gray-700 mb-1">Conteúdo (HTML)</label>
                        <textarea id="content" name="content" rows="15" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">{{ old('content', $post->content) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">A IA gera o conteúdo em HTML básico. Você pode editar as tags livremente.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label for="scheduled_at" class="block text-sm font-bold text-gray-700 mb-1">Data de Agendamento</label>
                            <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Deixe em branco para permitir que o sistema agende automaticamente.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex items-center space-x-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition-colors">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

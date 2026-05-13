@extends('layouts.admin')

@section('title', 'Editar Post')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.posts.index') }}">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Post</li>
        </ol>
    </nav>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Editar Conteúdo do Post</h6>
            @if($post->status == 'pending_approval')
                <span class="badge badge-warning">Aguardando Aprovação</span>
            @elseif($post->status == 'scheduled')
                <span class="badge badge-info">Agendado</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title" class="font-weight-bold">Título</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="excerpt" class="font-weight-bold">Resumo (Excerpt)</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="content" class="font-weight-bold">Conteúdo (HTML)</label>
                    <textarea class="form-control" id="content" name="content" rows="15" required>{{ old('content', $post->content) }}</textarea>
                    <small class="form-text text-muted">A IA gera o conteúdo em HTML básico. Você pode editar as tags livremente.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="scheduled_at" class="font-weight-bold">Data de Agendamento</label>
                            <input type="datetime-local" class="form-control" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}">
                            <small class="form-text text-muted">Deixe em branco para permitir que o sistema agende automaticamente.</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Voltar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

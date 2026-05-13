@extends('layouts.admin')

@section('title', 'Gestão de Posts (IA)')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Automação de Blog com IA</h1>
            <p class="text-muted">Gerencie os posts gerados automaticamente e configure a frequência de publicação.</p>
        </div>
        <div class="col-md-4 text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#settingsModal">
                <i class="fas fa-cog"></i> Configurações de Automação
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Aprovação Pendente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Agendados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['scheduled'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Publicados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $status == 'pending_approval' ? 'active' : '' }}" href="{{ route('admin.posts.index', ['status' => 'pending_approval']) }}">Pendentes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'scheduled' ? 'active' : '' }}" href="{{ route('admin.posts.index', ['status' => 'scheduled']) }}">Agendados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $status == 'published' ? 'active' : '' }}" href="{{ route('admin.posts.index', ['status' => 'published']) }}">Publicados</a>
        </li>
    </ul>

    <!-- Posts Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data</th>
                            <th>IA Model</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>
                                    <strong>{{ $post->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($post->excerpt, 100) }}</small>
                                </td>
                                <td>
                                    @if($post->status == 'scheduled')
                                        <span class="badge badge-info">Agenda: {{ $post->scheduled_at ? $post->scheduled_at->format('d/m/Y H:i') : 'Pendente' }}</span>
                                    @elseif($post->status == 'published')
                                        <span class="badge badge-success">Pub: {{ $post->published_at->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="badge badge-warning">Gerado em: {{ $post->created_at->format('d/m/Y') }}</span>
                                    @endif
                                </td>
                                <td>{{ $post->ai_model ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($post->status == 'pending_approval')
                                            <form action="{{ route('admin.posts.approve', $post) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Aprovar">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Nenhum post encontrado nesta categoria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $posts->links() }}
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.posts.settings.update') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Configurações de Automação de Blog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="blog_auto_generate" name="blog_auto_generate" value="1" {{ \App\Models\SiteSetting::get('blog_auto_generate') ? 'checked' : '' }}>
                            <label class="custom-control-label font-weight-bold" for="blog_auto_generate">Habilitar geração automática por IA</label>
                        </div>
                        <small class="form-text text-muted">Quando ativado, a IA gerará novos rascunhos automaticamente se o estoque estiver baixo.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Frequência de Publicação (Posts por semana)</label>
                        <select name="blog_post_frequency" class="form-control">
                            @for($i = 0; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ \App\Models\SiteSetting::get('blog_post_frequency') == $i ? 'selected' : '' }}>
                                    {{ $i == 0 ? 'Desativado' : $i . ' post(s) por semana' }}
                                </option>
                            @endfor
                        </select>
                        <small class="form-text text-muted">Define quantos posts aprovados serão publicados automaticamente por semana.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Contexto para a IA</label>
                        <textarea name="blog_post_context" class="form-control" rows="5" placeholder="Ex: Foco em notícias sobre o time sub-17, dicas de nutrição para jovens atletas e bastidores dos treinos.">{{ \App\Models\SiteSetting::get('blog_post_context') }}</textarea>
                        <small class="form-text text-muted">Dê orientações para a IA sobre o que escrever. Seja específico sobre o tom e os assuntos preferidos.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Configurações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

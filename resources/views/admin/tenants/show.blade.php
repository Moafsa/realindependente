@extends('layouts.admin')

@section('title', 'Detalhes do Clube')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.tenants.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Voltar para lista
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->name }}</h1>
        <p class="text-gray-600 mt-2">Gerenciar informações e status do clube</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informações Básicas</h2>
                
                <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Clube</label>
                            <input type="text" name="name" value="{{ old('name', $tenant->name) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subdomínio</label>
                            <input type="text" value="{{ $tenant->domain }}" disabled
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            <p class="text-xs text-gray-500 mt-1">URL: {{ str_replace(['http://', 'https://'], '', $tenant->url) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plano</label>
                            <select name="plan_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach(\App\Models\Plan::all() as $plan)
                                    <option value="{{ $plan->id }}" {{ $tenant->plan_id == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="trial" {{ $tenant->status === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="suspended" {{ $tenant->status === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                                <option value="cancelled" {{ $tenant->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trial até</label>
                                <input type="date" name="trial_ends_at" 
                                       value="{{ old('trial_ends_at', $tenant->trial_ends_at?->format('Y-m-d')) }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assinatura até</label>
                                <input type="date" name="subscription_ends_at" 
                                       value="{{ old('subscription_ends_at', $tenant->subscription_ends_at?->format('Y-m-d')) }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Domains -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Domínios</h2>
                <div class="space-y-2">
                    @forelse($tenant->domains as $domain)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div>
                                <span class="font-medium">{{ $domain->domain }}</span>
                                @if($domain->is_primary)
                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Principal</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ $domain->is_verified ? 'Verificado' : 'Não verificado' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500">Nenhum domínio cadastrado</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ações Rápidas</h2>
                    <form method="POST" action="{{ route('admin.tenants.impersonate', $tenant) }}" class="inline-block w-full">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold mb-4 shadow-lg shadow-indigo-100 transition-all">
                            Entrar como Admin
                        </button>
                    </form>

                    <div class="h-px bg-gray-100 my-4"></div>

                    @if($tenant->status !== 'active')
                        <form method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Ativar Clube
                            </button>
                        </form>
                    @endif

                    @if($tenant->status !== 'suspended')
                        <form method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                Suspender Clube
                            </button>
                        </form>
                    @endif

                    @if($tenant->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.tenants.cancel', $tenant) }}" class="inline-block w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                    onclick="return confirm('Tem certeza que deseja cancelar este clube?')">
                                Cancelar Clube
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Usage Metrics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Métricas de Uso</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-[10px] font-black text-gray-400 uppercase">Atletas</div>
                        <div class="text-lg font-black text-gray-900">{{ $usageStats['athletes_count'] ?? 0 }}</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-[10px] font-black text-gray-400 uppercase">Equipes</div>
                        <div class="text-lg font-black text-gray-900">{{ $usageStats['teams_count'] ?? 0 }}</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-[10px] font-black text-gray-400 uppercase">Planos IA</div>
                        <div class="text-lg font-black text-gray-900">{{ $usageStats['ai_content_count'] ?? 0 }}</div>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-xl">
                        <div class="text-[10px] font-black text-gray-400 uppercase">Usuários</div>
                        <div class="text-lg font-black text-gray-900">{{ $usageStats['users_count'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Estatísticas</h2>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-gray-600">Criado em</div>
                        <div class="text-sm font-medium">{{ $tenant->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Status Atual</div>
                        <div class="text-sm font-medium">
                            @php
                                $statusLabels = [
                                    'trial' => 'Em Trial',
                                    'active' => 'Ativo',
                                    'suspended' => 'Suspenso',
                                    'cancelled' => 'Cancelado',
                                ];
                            @endphp
                            {{ $statusLabels[$tenant->status] ?? $tenant->status }}
                        </div>
                    </div>
                    @if($tenant->trial_ends_at)
                        <div>
                            <div class="text-sm text-gray-600">Trial até</div>
                            <div class="text-sm font-medium">{{ $tenant->trial_ends_at->format('d/m/Y') }}</div>
                        </div>
                    @endif
                    @if($tenant->subscription_ends_at)
                        <div>
                            <div class="text-sm text-gray-600">Assinatura até</div>
                            <div class="text-sm font-medium">{{ $tenant->subscription_ends_at->format('d/m/Y') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


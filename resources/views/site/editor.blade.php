@extends('layouts.dashboard')

@section('title', 'Editor do Site')

@section('content')
<div class="space-y-6">
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-4">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editor do Site</h1>
            <p class="text-sm text-gray-600 mt-1">Personalize cada detalhe do seu site</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('site.home') }}" target="_blank" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">Ver Site</a>
            <button type="submit" form="site-settings-form" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Salvar Tudo</button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 overflow-x-auto">
        <nav class="-mb-px flex space-x-8 min-w-max">
            <button onclick="switchTab('geral')" id="tab-geral" class="tab-btn border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Geral</button>
            <button onclick="switchTab('menu')" id="tab-menu" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Menu & Páginas</button>
            <button onclick="switchTab('contato')" id="tab-contato" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Contato</button>
            <button onclick="switchTab('home')" id="tab-home" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Início</button>
            <button onclick="switchTab('sobre')" id="tab-sobre" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Sobre</button>
            <button onclick="switchTab('treinadores')" id="tab-treinadores" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Treinadores</button>
            <button onclick="switchTab('atletas')" id="tab-atletas" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Atletas</button>
            <button onclick="switchTab('equipes')" id="tab-equipes" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Equipes</button>
            <button onclick="switchTab('loja')" id="tab-loja" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Loja</button>
            <button onclick="switchTab('planos')" id="tab-planos" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Planos</button>
            <button onclick="switchTab('dominio')" id="tab-dominio" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Domínio</button>
            <button onclick="switchTab('galeria')" id="tab-galeria" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Galeria</button>
            <button onclick="switchTab('financeiro')" id="tab-financeiro" class="tab-btn border-transparent text-gray-500 hover:text-gray-700 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm text-red-600 font-bold">Financeiro</button>
        </nav>
    </div>

    <form id="site-settings-form" action="{{ route('site.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Editor Panel -->
            <div class="space-y-6">
                
                <!-- Tab: Geral -->
                <div id="content-geral" class="tab-content space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">Configurações Básicas</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Nome do Clube</label>
                                <input type="text" name="settings[site_name]" id="site_name" value="{{ $settings->firstWhere('key', 'site_name')->value ?? '' }}" oninput="updatePreview()" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Cores do Tema</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <span class="text-xs text-gray-500">Primária</span>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" name="settings[color_primary]" id="color_primary" value="{{ $settings->firstWhere('key', 'color_primary')->value ?? '#2563eb' }}" oninput="updatePreview()" class="h-10 w-12 border-0 p-0 bg-transparent cursor-pointer">
                                            <input type="text" value="{{ $settings->firstWhere('key', 'color_primary')->value ?? '#2563eb' }}" class="flex-1 px-2 py-1 border rounded text-xs uppercase" oninput="syncColor(this, 'color_primary')">
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-xs text-gray-500">Secundária</span>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" name="settings[color_secondary]" id="color_secondary" value="{{ $settings->firstWhere('key', 'color_secondary')->value ?? '#16a34a' }}" oninput="updatePreview()" class="h-10 w-12 border-0 p-0 bg-transparent cursor-pointer">
                                            <input type="text" value="{{ $settings->firstWhere('key', 'color_secondary')->value ?? '#16a34a' }}" class="flex-1 px-2 py-1 border rounded text-xs uppercase" oninput="syncColor(this, 'color_secondary')">
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-xs text-gray-500">Rodapé</span>
                                        <div class="flex items-center space-x-2">
                                            <input type="color" name="settings[color_footer]" id="color_footer" value="{{ $settings->firstWhere('key', 'color_footer')->value ?? '#1f2937' }}" oninput="updatePreview()" class="h-10 w-12 border-0 p-0 bg-transparent cursor-pointer">
                                            <input type="text" value="{{ $settings->firstWhere('key', 'color_footer')->value ?? '#1f2937' }}" class="flex-1 px-2 py-1 border rounded text-xs uppercase" oninput="syncColor(this, 'color_footer')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Logo</label>
                                <div class="flex items-center space-x-4">
                                    <div class="h-16 w-16 bg-gray-50 border rounded flex items-center justify-center overflow-hidden">
                                        <img id="logo-preview" src="{{ $settings->firstWhere('key', 'site_logo') ? Storage::url($settings->firstWhere('key', 'site_logo')->value) : '' }}" class="max-w-full max-h-full {{ $settings->firstWhere('key', 'site_logo') ? '' : 'hidden' }}">
                                        @if(!$settings->firstWhere('key', 'site_logo'))
                                            <span class="text-[10px] text-gray-400">Sem logo</span>
                                        @endif
                                    </div>
                                    <input type="file" name="settings[site_logo]" onchange="handleImageUpload(this, 'logo-preview')" class="flex-1 text-sm border p-2 rounded-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Menu e Páginas -->
                <div id="content-menu" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Ativar / Desativar Páginas</h2>
                        <p class="text-sm text-gray-600 mb-6">Escolha quais páginas devem aparecer no menu do seu site.</p>
                        
                        <div class="space-y-4">
                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_about_page]" value="0">
                                    <input type="checkbox" name="settings[enable_about_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_about_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Sobre Nós</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_teams_page]" value="0">
                                    <input type="checkbox" name="settings[enable_teams_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_teams_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Equipes</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_coaches_page]" value="0">
                                    <input type="checkbox" name="settings[enable_coaches_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_coaches_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Treinadores</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_athletes_page]" value="0">
                                    <input type="checkbox" name="settings[enable_athletes_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_athletes_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Atletas</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_store_page]" value="0">
                                    <input type="checkbox" name="settings[enable_store_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_store_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Loja</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_plans_page]" value="0">
                                    <input type="checkbox" name="settings[enable_plans_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_plans_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Planos</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_contact_page]" value="0">
                                    <input type="checkbox" name="settings[enable_contact_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_contact_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Contato</span>
                            </label>

                            <label class="flex items-center cursor-pointer p-3 border rounded-lg hover:bg-gray-50">
                                <div class="relative">
                                    <input type="hidden" name="settings[enable_blog_page]" value="0">
                                    <input type="checkbox" name="settings[enable_blog_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_blog_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900">Blog / Notícias</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Tab: Home -->
                <div id="content-home" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-md font-bold mb-4 border-b pb-2">Seção Hero (Topo)</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Banner Principal</label>
                                <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden">
                                    <img id="banner-preview" src="{{ $settings->firstWhere('key', 'banner_image') ? Storage::url($settings->firstWhere('key', 'banner_image')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'banner_image') ? '' : 'hidden' }}">
                                    @if(!$settings->firstWhere('key', 'banner_image'))
                                        <span class="text-xs text-gray-400">Sem imagem</span>
                                    @endif
                                </div>
                                <input type="file" name="settings[banner_image]" onchange="handleImageUpload(this, 'banner-preview')" class="w-full text-sm border p-2 rounded-lg">
                            </div>
                            <div><label class="text-sm font-medium">Título</label><input type="text" id="hero_title" name="settings[hero_title]" value="{{ $settings->firstWhere('key', 'hero_title')->value ?? '' }}" oninput="updatePreview()" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Subtítulo</label><input type="text" id="hero_subtitle" name="settings[hero_subtitle]" value="{{ $settings->firstWhere('key', 'hero_subtitle')->value ?? '' }}" oninput="updatePreview()" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Texto de Apoio</label><textarea id="hero_description" name="settings[hero_description]" rows="2" oninput="updatePreview()" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'hero_description')->value ?? '' }}</textarea></div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Estatísticas</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Label Atletas</label><input type="text" name="settings[stats_athletes_label]" value="{{ $settings->firstWhere('key', 'stats_athletes_label')->value ?? 'Atletas Ativos' }}" class="w-full p-2 border rounded-lg text-sm mb-2">
                                <label class="text-xs text-gray-500 uppercase">Qtd. Atletas (Deixe em branco para Automático)</label><input type="number" name="settings[athletes_count]" value="{{ $settings->firstWhere('key', 'athletes_count')->value ?? '' }}" class="w-full p-2 border rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Label História</label><input type="text" name="settings[stats_history_label]" value="{{ $settings->firstWhere('key', 'stats_history_label')->value ?? 'Anos de História' }}" class="w-full p-2 border rounded-lg text-sm mb-2">
                                <label class="text-xs text-gray-500 uppercase">Anos de História (Número)</label><input type="number" name="settings[history_years]" value="{{ $settings->firstWhere('key', 'history_years')->value ?? '10' }}" class="w-full p-2 border rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Label Categorias</label><input type="text" name="settings[stats_teams_label]" value="{{ $settings->firstWhere('key', 'stats_teams_label')->value ?? 'Categorias' }}" class="w-full p-2 border rounded-lg text-sm mb-2">
                                <label class="text-xs text-gray-500 uppercase">Qtd. Categorias (Deixe em branco para Automático)</label><input type="number" name="settings[categories_count]" value="{{ $settings->firstWhere('key', 'categories_count')->value ?? '' }}" class="w-full p-2 border rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase">Label Títulos</label><input type="text" name="settings[stats_titles_label]" value="{{ $settings->firstWhere('key', 'stats_titles_label')->value ?? 'Títulos Conquistados' }}" class="w-full p-2 border rounded-lg text-sm mb-2">
                                <label class="text-xs text-gray-500 uppercase">Quantidade de Títulos (Número)</label><input type="number" name="settings[titles_count]" value="{{ $settings->firstWhere('key', 'titles_count')->value ?? '0' }}" class="w-full p-2 border rounded-lg text-sm">
                            </div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Seção de Equipes</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Título da Seção</label><input type="text" name="settings[teams_section_title]" value="{{ $settings->firstWhere('key', 'teams_section_title')->value ?? 'Nossas Equipes' }}" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Subtítulo da Seção</label><textarea name="settings[teams_section_subtitle]" rows="2" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'teams_section_subtitle')->value ?? '' }}</textarea></div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Metodologia e Diferenciais</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Título Principal</label><input type="text" name="settings[methodology_title]" value="{{ $settings->firstWhere('key', 'methodology_title')->value ?? 'Nossa Metodologia' }}" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Subtítulo Principal</label><textarea name="settings[methodology_subtitle]" rows="2" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'methodology_subtitle')->value ?? '' }}</textarea></div>
                            
                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Diferencial 1</p>
                                <input type="text" name="settings[feature1_title]" value="{{ $settings->firstWhere('key', 'feature1_title')->value ?? 'Treinamento com IA' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[feature1_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'feature1_text')->value ?? '' }}</textarea>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Diferencial 2</p>
                                <input type="text" name="settings[feature2_title]" value="{{ $settings->firstWhere('key', 'feature2_title')->value ?? 'Monitoramento Real' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[feature2_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'feature2_text')->value ?? '' }}</textarea>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Diferencial 3</p>
                                <input type="text" name="settings[feature3_title]" value="{{ $settings->firstWhere('key', 'feature3_title')->value ?? 'Nutrição do Amanhã' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[feature3_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'feature3_text')->value ?? '' }}</textarea>
                            </div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Contato (Rodapé da Home)</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Título</label><input type="text" name="settings[contact_title]" value="{{ $settings->firstWhere('key', 'contact_title')->value ?? 'Fale Conosco' }}" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Subtítulo</label><textarea name="settings[contact_subtitle]" rows="2" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'contact_subtitle')->value ?? '' }}</textarea></div>
                        </div>
                    </div>
                </div>

                <!-- Tab: Sobre -->
                <div id="content-sobre" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-md font-bold mb-4 border-b pb-2">Seção Hero (Topo)</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Banner Superior</label><input type="file" name="settings[site_hero_image]" class="w-full border p-2 rounded-lg text-sm"></div>
                            <div><label class="text-sm font-medium">Título da Página</label><input type="text" name="settings[about_page_title]" value="{{ $settings->firstWhere('key', 'about_page_title')->value ?? 'Sobre Nós' }}" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Descrição</label><textarea name="settings[site_description]" rows="2" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'site_description')->value ?? '' }}</textarea></div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Nossa História</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Título</label><input type="text" id="about_title" name="settings[about_title]" value="{{ $settings->firstWhere('key', 'about_title')->value ?? 'Um Legado' }}" oninput="updatePreview()" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Subtítulo</label><input type="text" id="about_subtitle" name="settings[about_subtitle]" value="{{ $settings->firstWhere('key', 'about_subtitle')->value ?? 'em cada jogada.' }}" oninput="updatePreview()" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">História</label><textarea name="settings[about_history]" rows="3" class="w-full border p-2 rounded-lg">{{ $settings->firstWhere('key', 'about_history')->value ?? '' }}</textarea></div>
                            <div><label class="text-sm font-medium">Frase de Efeito (Citação)</label><textarea name="settings[about_quote]" rows="2" class="w-full border p-2 rounded-lg">{{ $settings->firstWhere('key', 'about_quote')->value ?? '' }}</textarea></div>
                            <div><label class="text-sm font-medium">Missão/Visão</label><textarea name="settings[about_mission]" rows="3" class="w-full border p-2 rounded-lg">{{ $settings->firstWhere('key', 'about_mission')->value ?? '' }}</textarea></div>
                            <div><label class="text-sm font-medium">Imagem Lateral</label><input type="file" name="settings[about_image]" class="w-full border p-2 rounded-lg text-sm"></div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Nossos Pilares</h3>
                        <div class="space-y-6">
                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Pilar 1</p>
                                <input type="text" name="settings[about_pillar1_title]" value="{{ $settings->firstWhere('key', 'about_pillar1_title')->value ?? 'Transparência' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[about_pillar1_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'about_pillar1_text')->value ?? '' }}</textarea>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Pilar 2</p>
                                <input type="text" name="settings[about_pillar2_title]" value="{{ $settings->firstWhere('key', 'about_pillar2_title')->value ?? 'Inovação' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[about_pillar2_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'about_pillar2_text')->value ?? '' }}</textarea>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-lg space-y-3">
                                <p class="text-xs font-bold uppercase text-gray-500">Pilar 3</p>
                                <input type="text" name="settings[about_pillar3_title]" value="{{ $settings->firstWhere('key', 'about_pillar3_title')->value ?? 'Família' }}" placeholder="Título" class="w-full p-2 border rounded-lg text-sm">
                                <textarea name="settings[about_pillar3_text]" rows="2" placeholder="Descrição" class="w-full p-2 border rounded-lg text-sm">{{ $settings->firstWhere('key', 'about_pillar3_text')->value ?? '' }}</textarea>
                            </div>
                        </div>

                        <h3 class="text-md font-bold mt-8 mb-4 border-b pb-2">Chamada para Ação (CTA)</h3>
                        <div class="space-y-4">
                            <div><label class="text-sm font-medium">Título do CTA</label><input type="text" name="settings[about_cta_title]" value="{{ $settings->firstWhere('key', 'about_cta_title')->value ?? 'Pronto para escrever sua história?' }}" class="w-full p-2 border rounded-lg"></div>
                            <div><label class="text-sm font-medium">Texto do CTA</label><textarea name="settings[about_cta_text]" rows="2" class="w-full p-2 border rounded-lg">{{ $settings->firstWhere('key', 'about_cta_text')->value ?? '' }}</textarea></div>
                        </div>
                    </div>
                </div>

                <!-- Tabs: Treinadores, Atletas, Equipes, Loja -->
                <div id="content-treinadores" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="coaches-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'coaches_banner') ? Storage::url($settings->firstWhere('key', 'coaches_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'coaches_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'coaches_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[coaches_banner]" onchange="handleImageUpload(this, 'coaches-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    <input type="text" id="coaches_title" name="settings[coaches_title]" value="{{ $settings->firstWhere('key', 'coaches_title')->value ?? '' }}" oninput="updatePreview()" placeholder="Título" class="w-full border p-2 mb-4 rounded-lg">
                    <textarea id="coaches_subtitle" name="settings[coaches_subtitle]" rows="2" oninput="updatePreview()" placeholder="Subtítulo" class="w-full border p-2 mb-4 rounded-lg text-sm">{{ $settings->firstWhere('key', 'coaches_subtitle')->value ?? '' }}</textarea>
                    <textarea name="settings[coaches_description]" rows="3" placeholder="Texto de Descrição" class="w-full border p-2 rounded-lg text-sm">{{ $settings->firstWhere('key', 'coaches_description')->value ?? '' }}</textarea>
                </div></div>
                <div id="content-atletas" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="athletes-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'athletes_banner') ? Storage::url($settings->firstWhere('key', 'athletes_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'athletes_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'athletes_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[athletes_banner]" onchange="handleImageUpload(this, 'athletes-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    <input type="text" id="athletes_title" name="settings[athletes_title]" value="{{ $settings->firstWhere('key', 'athletes_title')->value ?? '' }}" oninput="updatePreview()" placeholder="Título" class="w-full border p-2 mb-4 rounded-lg">
                    <textarea id="athletes_subtitle" name="settings[athletes_subtitle]" rows="2" oninput="updatePreview()" placeholder="Subtítulo" class="w-full border p-2 mb-4 rounded-lg text-sm">{{ $settings->firstWhere('key', 'athletes_subtitle')->value ?? '' }}</textarea>
                    <textarea name="settings[athletes_description]" rows="3" placeholder="Texto de Descrição" class="w-full border p-2 rounded-lg text-sm">{{ $settings->firstWhere('key', 'athletes_description')->value ?? '' }}</textarea>
                </div></div>
                <div id="content-equipes" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="teams-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'teams_banner') ? Storage::url($settings->firstWhere('key', 'teams_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'teams_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'teams_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[teams_banner]" onchange="handleImageUpload(this, 'teams-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    <input type="text" id="teams_title" name="settings[teams_title]" value="{{ $settings->firstWhere('key', 'teams_title')->value ?? '' }}" oninput="updatePreview()" placeholder="Título" class="w-full border p-2 mb-4 rounded-lg">
                    <textarea id="teams_subtitle" name="settings[teams_subtitle]" rows="2" oninput="updatePreview()" placeholder="Subtítulo" class="w-full border p-2 mb-4 rounded-lg text-sm">{{ $settings->firstWhere('key', 'teams_subtitle')->value ?? '' }}</textarea>
                    <textarea name="settings[teams_description]" rows="3" placeholder="Texto de Descrição" class="w-full border p-2 rounded-lg text-sm">{{ $settings->firstWhere('key', 'teams_description')->value ?? '' }}</textarea>
                </div></div>
                <div id="content-loja" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="store-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'store_banner') ? Storage::url($settings->firstWhere('key', 'store_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'store_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'store_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[store_banner]" onchange="handleImageUpload(this, 'store-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    <input type="text" id="store_title" name="settings[store_title]" value="{{ $settings->firstWhere('key', 'store_title')->value ?? '' }}" oninput="updatePreview()" placeholder="Título" class="w-full border p-2 mb-4 rounded-lg">
                    <textarea id="store_subtitle" name="settings[store_subtitle]" rows="2" oninput="updatePreview()" placeholder="Subtítulo" class="w-full border p-2 mb-4 rounded-lg text-sm">{{ $settings->firstWhere('key', 'store_subtitle')->value ?? '' }}</textarea>
                    <textarea name="settings[store_description]" rows="3" placeholder="Texto de Descrição" class="w-full border p-2 rounded-lg text-sm">{{ $settings->firstWhere('key', 'store_description')->value ?? '' }}</textarea>
                </div></div>
                <div id="content-planos" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <div class="mb-4 flex items-center justify-between border-b pb-4">
                        <span class="font-bold text-gray-700">Habilitar Página de Planos</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="settings[enable_plans_page]" value="0">
                            <input type="checkbox" name="settings[enable_plans_page]" value="1" class="sr-only peer" {{ ($settings->firstWhere('key', 'enable_plans_page')->value ?? '1') == '1' ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="plans-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'plans_banner') ? Storage::url($settings->firstWhere('key', 'plans_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'plans_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'plans_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[plans_banner]" onchange="handleImageUpload(this, 'plans-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    <input type="text" id="plans_title" name="settings[plans_title]" value="{{ $settings->firstWhere('key', 'plans_title')->value ?? 'Nossos Planos' }}" oninput="updatePreview()" placeholder="Título" class="w-full border p-2 mb-4 rounded-lg">
                    <textarea id="plans_subtitle" name="settings[plans_subtitle]" rows="2" oninput="updatePreview()" placeholder="Subtítulo" class="w-full border p-2 mb-4 rounded-lg text-sm">{{ $settings->firstWhere('key', 'plans_subtitle')->value ?? 'Escolha a melhor assinatura para você.' }}</textarea>
                </div></div>

                <div id="content-contato" class="tab-content hidden space-y-6"><div class="bg-white shadow p-6 rounded-lg">
                    <h2 class="text-lg font-bold mb-4">Página de Contato</h2>
                    <div class="mb-2 h-24 w-full bg-gray-50 border rounded flex items-center justify-center overflow-hidden" id="contact-banner-preview">
                        <img src="{{ $settings->firstWhere('key', 'contact_banner') ? Storage::url($settings->firstWhere('key', 'contact_banner')->value) : '' }}" class="w-full h-full object-cover {{ $settings->firstWhere('key', 'contact_banner') ? '' : 'hidden' }}">
                        @if(!$settings->firstWhere('key', 'contact_banner'))
                            <span class="text-xs text-gray-400">Sem imagem</span>
                        @endif
                    </div>
                    <input type="file" name="settings[contact_banner]" onchange="handleImageUpload(this, 'contact-banner-preview')" class="w-full border p-2 mb-4 rounded-lg text-sm">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Título da Página</label>
                            <input type="text" name="settings[contact_title]" value="{{ $settings->firstWhere('key', 'contact_title')->value ?? 'Fale Conosco' }}" class="w-full border p-2 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Subtítulo/Descrição</label>
                            <textarea name="settings[contact_subtitle]" rows="2" class="w-full border p-2 rounded-lg text-sm">{{ $settings->firstWhere('key', 'contact_subtitle')->value ?? '' }}</textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 border-t pt-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">E-mail de Contato</label>
                                <input type="email" name="settings[contact_email]" value="{{ $settings->firstWhere('key', 'contact_email')->value ?? '' }}" placeholder="Ex: contato@clube.com" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Telefone / WhatsApp</label>
                                <input type="text" name="settings[contact_phone]" value="{{ $settings->firstWhere('key', 'contact_phone')->value ?? '' }}" placeholder="Ex: (11) 99999-9999" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">Endereço Completo (Aparece no Mapa)</label>
                                <input type="text" name="settings[contact_address]" value="{{ $settings->firstWhere('key', 'contact_address')->value ?? '' }}" placeholder="Rua, Número, Bairro, Cidade - Estado" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link do Instagram</label>
                                <input type="url" name="settings[instagram_url]" value="{{ $settings->firstWhere('key', 'instagram_url')->value ?? '' }}" placeholder="https://instagram.com/seuclube" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link do Facebook</label>
                                <input type="url" name="settings[facebook_url]" value="{{ $settings->firstWhere('key', 'facebook_url')->value ?? '' }}" placeholder="https://facebook.com/seuclube" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link do YouTube</label>
                                <input type="url" name="settings[youtube_url]" value="{{ $settings->firstWhere('key', 'youtube_url')->value ?? '' }}" placeholder="https://youtube.com/seuclube" class="w-full border p-2 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Tab: Galeria -->
                <div id="content-galeria" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-2">Galeria de Fotos e Vídeos</h2>
                        <p class="text-sm text-gray-500 mb-6">Esta galeria será exibida na seção principal do seu site.</p>
                        
                        @php
                            // Fetch general gallery items for the site (galleryable_type and id are null)
                            $siteGalleryItems = \App\Models\GalleryItem::whereNull('galleryable_type')->whereNull('galleryable_id')->orderBy('sort_order')->orderBy('created_at', 'desc')->get();
                        @endphp
                        
                        <x-gallery-manager :galleryItems="$siteGalleryItems" galleryableType="" galleryableId="" />
                    </div>
                </div>

                <!-- Tab: Domínio Personalizado -->
                <div id="content-dominio" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow p-6 rounded-lg">
                        <h2 class="text-lg font-bold mb-4">Domínio Customizado</h2>
                        <p class="text-sm text-gray-600 mb-6">Aponte seu domínio próprio para o nosso sistema.</p>
                        
                        <div class="space-y-6">
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h3 class="text-xs font-bold uppercase text-blue-800 mb-2">Instruções de Apontamento</h3>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    Para usar seu domínio próprio, você deve criar um registro <strong>CNAME</strong> na sua zona de DNS:
                                    <br><br>
                                    <strong>Tipo:</strong> CNAME<br>
                                    <strong>Nome:</strong> @ (ou seu subdomínio)<br>
                                    <strong>Destino:</strong> {{ tenant()->domains()->where('is_primary', true)->first()->domain ?? 'seu-subdominio.dominio.com' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Seu Domínio</label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">https://</span>
                                    <input type="text" name="settings[custom_domain]" value="{{ $customDomain->domain ?? '' }}" class="flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 border p-2" placeholder="Ex: www.meuclube.com.br">
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1 italic">Não inclua http:// ou https:// no campo acima.</p>
                            </div>

                            @if($customDomain)
                            <div class="flex items-center space-x-2 text-sm">
                                <span class="flex-shrink-0 h-2 w-2 rounded-full {{ $customDomain->is_verified ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                <span class="{{ $customDomain->is_verified ? 'text-green-700' : 'text-yellow-700' }}">
                                    Status: {{ $customDomain->is_verified ? 'Verificado e Ativo' : 'Aguardando Apontamento/Propagação' }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="content-financeiro" class="tab-content hidden space-y-6">
                    <div class="bg-white shadow p-6 rounded-lg">
                        <h2 class="text-lg font-bold mb-4 text-red-600">Configurações de Pagamento (Asaas)</h2>
                        <p class="text-sm text-gray-600 mb-6">Configure aqui sua integração com o Asaas para receber os pagamentos da sua loja e mensalidades.</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Chave de API do Asaas (Tenant)</label>
                                <input type="password" name="settings[asaas_api_key]" value="{{ $settings->firstWhere('key', 'asaas_api_key')->value ?? '' }}" class="w-full p-2 border rounded-lg focus:ring-red-500" placeholder="Ex: $aact_...">
                                <p class="text-[10px] text-gray-500 mt-1 italic">Cole aqui a sua chave de API gerada no painel do Asaas.</p>
                            </div>

                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h3 class="text-xs font-bold uppercase text-blue-800 mb-2">Sobre o Split de Pagamentos</h3>
                                <p class="text-xs text-blue-700">A plataforma está configurada para cobrar uma taxa administrativa automática sobre cada venda. O valor líquido irá diretamente para sua conta Asaas após o desconto da taxa da plataforma.</p>
                            </div>
                            

                        </div>
                    </div>
                </div>

            </div>

            <!-- Preview Panel -->
            <div class="lg:sticky lg:top-6 lg:h-[calc(100vh-2rem)]">
                <div class="bg-white shadow rounded-lg p-6 border-2 border-blue-50 h-full flex flex-col">
                    <h2 class="text-lg font-semibold mb-4">Preview</h2>
                    <div id="live-preview" class="flex-1 border-4 border-gray-800 rounded-3xl overflow-hidden bg-white shadow-2xl scale-[0.9] origin-top">
                        <div class="bg-white border-b p-3 flex items-center space-x-2">
                            <div id="preview-logo" class="h-5 w-5 bg-gray-100 rounded"></div>
                            <span id="preview-site-name" class="text-[10px] font-bold" style="color: var(--preview-primary, #2563eb);">{{ $settings->firstWhere('key', 'site_name')->value ?? 'Clube' }}</span>
                        </div>
                        <div id="preview-hero" class="p-6 text-center text-white h-40 flex flex-col justify-center" style="background: linear-gradient(to right, var(--preview-primary, #2563eb), var(--preview-secondary, #16a34a));">
                            <h1 id="preview-banner-title" class="text-sm font-bold">{{ $settings->firstWhere('key', 'banner_title')->value ?? 'Bem-vindo' }}</h1>
                            <p id="preview-banner-subtitle" class="text-[7px] opacity-90 line-clamp-2">{{ $settings->firstWhere('key', 'banner_subtitle')->value ?? 'Descrição' }}</p>
                        </div>
                        <div class="p-4 space-y-3 bg-gray-50 h-full">
                            <div class="h-1.5 w-16 bg-gray-200 rounded"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="h-12 bg-white rounded shadow-sm border border-gray-100"></div>
                                <div class="h-12 bg-white rounded shadow-sm border border-gray-100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ global_asset('js/site-editor.js') }}"></script>
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });
        document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600');
        document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
        document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
        document.getElementById('content-' + tab).classList.remove('hidden');
        
        // Trigger preview update when tab changes
        if (typeof updatePreview === 'function') {
            updatePreview();
        }
    }

    function syncColor(textInput, colorInputId) {
        const colorInput = document.getElementById(colorInputId);
        if (colorInput && /^#[0-9A-F]{6}$/i.test(textInput.value)) {
            colorInput.value = textInput.value;
            updatePreview();
        }
    }
</script>
@endsection

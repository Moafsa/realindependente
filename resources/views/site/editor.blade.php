@extends('layouts.dashboard')

@section('title', 'Editor do Site')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editor do Site</h1>
            <p class="text-sm text-gray-600 mt-1">Personalize o site público do seu clube</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('site.home') }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Ver Site
            </a>
            <button type="button" 
                    onclick="saveSettings()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Salvar Alterações
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Editor Panel -->
        <div class="space-y-6">
            <!-- General Settings -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurações Gerais</h2>
                <div class="space-y-4">
                    <div>
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Site
                        </label>
                        <input type="text" 
                               name="settings[site_name]" 
                               id="site_name" 
                               value="{{ $settings->firstWhere('key', 'site_name')->value ?? '' }}"
                               oninput="updatePreview()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição
                        </label>
                        <textarea name="settings[site_description]" 
                                  id="site_description" 
                                  rows="3"
                                  oninput="updatePreview()"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings->firstWhere('key', 'site_description')->value ?? '' }}</textarea>
                    </div>

                    <div>
                        <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo
                        </label>
                        <div class="flex items-center space-x-4">
                            @if($settings->firstWhere('key', 'site_logo')?->value)
                            <img id="logo-preview" 
                                 src="{{ asset('storage/' . $settings->firstWhere('key', 'site_logo')->value) }}" 
                                 alt="Logo" 
                                 class="h-16 w-16 object-contain border border-gray-300 rounded">
                            @else
                            <div id="logo-preview" class="h-16 w-16 bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-400 text-xs">
                                Sem logo
                            </div>
                            @endif
                            <input type="file" 
                                   name="settings[site_logo]" 
                                   id="site_logo" 
                                   accept="image/*"
                                   onchange="handleImageUpload(this, 'logo-preview')"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colors -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Cores</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="color_primary" class="block text-sm font-medium text-gray-700 mb-2">
                            Cor Primária
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="color" 
                                   name="settings[color_primary]" 
                                   id="color_primary" 
                                   value="{{ $settings->firstWhere('key', 'color_primary')->value ?? '#2563eb' }}"
                                   onchange="updatePreview()"
                                   class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" 
                                   value="{{ $settings->firstWhere('key', 'color_primary')->value ?? '#2563eb' }}"
                                   onchange="document.getElementById('color_primary').value = this.value; updatePreview()"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="color_secondary" class="block text-sm font-medium text-gray-700 mb-2">
                            Cor Secundária
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="color" 
                                   name="settings[color_secondary]" 
                                   id="color_secondary" 
                                   value="{{ $settings->firstWhere('key', 'color_secondary')->value ?? '#16a34a' }}"
                                   onchange="updatePreview()"
                                   class="h-10 w-20 border border-gray-300 rounded cursor-pointer">
                            <input type="text" 
                                   value="{{ $settings->firstWhere('key', 'color_secondary')->value ?? '#16a34a' }}"
                                   onchange="document.getElementById('color_secondary').value = this.value; updatePreview()"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Contato</h2>
                <div class="space-y-4">
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone
                        </label>
                        <input type="text" 
                               name="settings[contact_phone]" 
                               id="contact_phone" 
                               value="{{ $settings->firstWhere('key', 'contact_phone')->value ?? '' }}"
                               oninput="updatePreview()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail
                        </label>
                        <input type="email" 
                               name="settings[contact_email]" 
                               id="contact_email" 
                               value="{{ $settings->firstWhere('key', 'contact_email')->value ?? '' }}"
                               oninput="updatePreview()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Endereço
                        </label>
                        <textarea name="settings[contact_address]" 
                                  id="contact_address" 
                                  rows="2"
                                  oninput="updatePreview()"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings->firstWhere('key', 'contact_address')->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Redes Sociais</h2>
                <div class="space-y-4">
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Facebook
                        </label>
                        <input type="url" 
                               name="settings[facebook_url]" 
                               id="facebook_url" 
                               value="{{ $settings->firstWhere('key', 'facebook_url')->value ?? '' }}"
                               placeholder="https://facebook.com/seuclube"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Instagram
                        </label>
                        <input type="url" 
                               name="settings[instagram_url]" 
                               id="instagram_url" 
                               value="{{ $settings->firstWhere('key', 'instagram_url')->value ?? '' }}"
                               placeholder="https://instagram.com/seuclube"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                            YouTube
                        </label>
                        <input type="url" 
                               name="settings[youtube_url]" 
                               id="youtube_url" 
                               value="{{ $settings->firstWhere('key', 'youtube_url')->value ?? '' }}"
                               placeholder="https://youtube.com/seuclube"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Banner -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Banner Principal</h2>
                <div class="space-y-4">
                    <div>
                        <label for="banner_image" class="block text-sm font-medium text-gray-700 mb-2">
                            Imagem do Banner
                        </label>
                        <div class="flex items-center space-x-4">
                            @if($settings->firstWhere('key', 'banner_image')?->value)
                            <img id="banner-preview" 
                                 src="{{ asset('storage/' . $settings->firstWhere('key', 'banner_image')->value) }}" 
                                 alt="Banner" 
                                 class="h-32 w-full object-cover border border-gray-300 rounded">
                            @else
                            <div id="banner-preview" class="h-32 w-full bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-400 text-sm">
                                Sem banner
                            </div>
                            @endif
                        </div>
                        <input type="file" 
                               name="settings[banner_image]" 
                               id="banner_image" 
                               accept="image/*"
                               onchange="handleImageUpload(this, 'banner-preview')"
                               class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="banner_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Título do Banner
                        </label>
                        <input type="text" 
                               name="settings[banner_title]" 
                               id="banner_title" 
                               value="{{ $settings->firstWhere('key', 'banner_title')->value ?? '' }}"
                               oninput="updatePreview()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="banner_subtitle" class="block text-sm font-medium text-gray-700 mb-2">
                            Subtítulo do Banner
                        </label>
                        <textarea name="settings[banner_subtitle]" 
                                  id="banner_subtitle" 
                                  rows="2"
                                  oninput="updatePreview()"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $settings->firstWhere('key', 'banner_subtitle')->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Panel -->
        <div class="lg:sticky lg:top-6 lg:h-screen lg:overflow-y-auto">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Preview em Tempo Real</h2>
                    <a href="{{ route('site.home') }}" 
                       target="_blank"
                       class="text-sm text-blue-600 hover:text-blue-800">
                        Ver Site Completo →
                    </a>
                </div>
                
                <!-- Live Preview -->
                <div id="live-preview" class="border-2 border-gray-200 rounded-lg overflow-hidden bg-white">
                    <!-- Header Preview -->
                    <div id="preview-header" class="bg-white border-b border-gray-200 p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div id="preview-logo" class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">
                                    Logo
                                </div>
                                <span id="preview-site-name" class="text-lg font-bold" style="color: var(--preview-primary, #2563eb);">
                                    {{ $settings->firstWhere('key', 'site_name')->value ?? 'Nome do Clube' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Section Preview -->
                    <div id="preview-hero" class="p-8 text-center text-white" style="background: linear-gradient(to right, var(--preview-primary, #2563eb), var(--preview-secondary, #16a34a));">
                        <h1 id="preview-banner-title" class="text-3xl font-bold mb-4">
                            {{ $settings->firstWhere('key', 'banner_title')->value ?? 'Bem-vindo ao Nosso Clube' }}
                        </h1>
                        <p id="preview-banner-subtitle" class="text-lg opacity-90">
                            {{ $settings->firstWhere('key', 'banner_subtitle')->value ?? 'Descrição do clube' }}
                        </p>
                    </div>

                    <!-- Content Preview -->
                    <div class="p-6 space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Descrição do Site</h3>
                            <p id="preview-description" class="text-gray-700 text-sm">
                                {{ $settings->firstWhere('key', 'site_description')->value ?? 'Descrição do clube aparecerá aqui' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg" style="background-color: var(--preview-primary, #2563eb); opacity: 0.1;">
                                <div class="text-2xl font-bold mb-1" style="color: var(--preview-primary, #2563eb);">247</div>
                                <div class="text-xs text-gray-600">Atletas</div>
                            </div>
                            <div class="p-4 rounded-lg" style="background-color: var(--preview-secondary, #16a34a); opacity: 0.1;">
                                <div class="text-2xl font-bold mb-1" style="color: var(--preview-secondary, #16a34a);">15</div>
                                <div class="text-xs text-gray-600">Anos</div>
                            </div>
                        </div>

                        <!-- Contact Preview -->
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-3">Informações de Contato</h3>
                            <div class="space-y-2 text-sm">
                                <div id="preview-phone" class="text-gray-700">
                                    📞 {{ $settings->firstWhere('key', 'contact_phone')->value ?? '(00) 0000-0000' }}
                                </div>
                                <div id="preview-email" class="text-gray-700">
                                    ✉️ {{ $settings->firstWhere('key', 'contact_email')->value ?? 'contato@clube.com' }}
                                </div>
                                <div id="preview-address" class="text-gray-700">
                                    📍 {{ $settings->firstWhere('key', 'contact_address')->value ?? 'Endereço do clube' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="settings-form" method="POST" action="{{ route('site.update') }}" enctype="multipart/form-data" style="display: none;">
    @csrf
    <div id="form-fields"></div>
</form>

<script src="{{ asset('js/site-editor.js') }}"></script>
@endsection


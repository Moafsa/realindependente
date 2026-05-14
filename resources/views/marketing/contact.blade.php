@extends('layouts.app')

@section('title', 'Contato - Nexts')

@section('content')
<!-- Header -->
<header id="main-header" class="fixed w-full top-0 z-50 transition-all duration-300 bg-white/0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
            <div class="flex items-center">
                <a href="{{ route('marketing.home') }}" class="text-2xl font-bold text-white transition-colors duration-300" id="header-logo">Nexts</a>
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('marketing.home') }}#features" class="text-white/80 hover:text-white transition-colors nav-link">Funcionalidades</a>
                <a href="{{ route('marketing.home') }}#pricing" class="text-white/80 hover:text-white transition-colors nav-link">Preços</a>
                <a href="{{ route('marketing.contact') }}" class="text-white/80 hover:text-white transition-colors nav-link">Contato</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-white/80 hover:text-white transition-colors nav-link">Login</a>
                <a href="{{ route('tenant.register') }}" class="bg-white text-blue-600 px-4 py-2 rounded-md hover:bg-gray-100 transition-all duration-300 shadow-lg" id="header-cta">Começar Grátis</a>
            </div>
        </div>
    </div>
</header>

<script>
    window.addEventListener('scroll', function() {
        const header = document.getElementById('main-header');
        const logo = document.getElementById('header-logo');
        const links = document.querySelectorAll('.nav-link');
        const cta = document.getElementById('header-cta');
        
        if (window.scrollY > 50) {
            header.classList.remove('bg-white/0');
            header.classList.add('bg-white/80', 'backdrop-blur-md', 'shadow-lg', 'py-4');
            logo.classList.remove('text-white');
            logo.classList.add('text-blue-600');
            links.forEach(link => {
                link.classList.remove('text-white/80');
                link.classList.add('text-gray-600');
            });
            cta.classList.remove('bg-white', 'text-blue-600');
            cta.classList.add('bg-blue-600', 'text-white');
        } else {
            header.classList.add('bg-white/0');
            header.classList.remove('bg-white/80', 'backdrop-blur-md', 'shadow-lg', 'py-4');
            logo.classList.add('text-white');
            logo.classList.remove('text-blue-600');
            links.forEach(link => {
                link.classList.add('text-white/80');
                link.classList.remove('text-gray-600');
            });
            cta.classList.add('bg-white', 'text-blue-600');
            cta.classList.remove('bg-blue-600', 'text-white');
        }
    });
</script>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">Entre em Contato</h1>
        <p class="text-xl text-blue-100">Estamos aqui para ajudar você a transformar seu clube</p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Envie sua Mensagem</h2>
                
                @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
                @endif

                <form method="POST" action="{{ route('marketing.contact.submit') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Seu nome completo">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                E-mail <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email') }}" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                   placeholder="seu@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone" 
                                   value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                   placeholder="(00) 00000-0000">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Assunto <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="subject" 
                               id="subject" 
                               value="{{ old('subject') }}" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('subject') border-red-500 @enderror"
                               placeholder="Assunto da sua mensagem">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Mensagem <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" 
                                  id="message" 
                                  rows="6" 
                                  required
                                  maxlength="1000"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"
                                  placeholder="Sua mensagem...">{{ old('message') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Máximo de 1000 caracteres</p>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Enviar Mensagem
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Informações de Contato</h2>
                    <p class="text-gray-600 mb-8">
                        Estamos sempre prontos para ajudar. Entre em contato conosco através dos canais abaixo ou preencha o formulário ao lado.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-start mb-6">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">E-mail</h3>
                            <p class="text-gray-600">{{ $contact['email'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-start mb-6">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Telefone / WhatsApp</h3>
                            <p class="text-gray-600">{{ $contact['phone'] }}</p>
                            @if($contact['whatsapp'])
                                <p class="text-gray-600">{{ $contact['whatsapp'] }} (WhatsApp)</p>
                            @endif
                            <p class="text-sm text-gray-500 mt-1">Segunda a Sexta, 9h às 18h</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Endereço</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $contact['address'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Redes Sociais</h3>
                    <div class="flex space-x-4">
                        @if($contact['instagram'] !== '#')
                        <a href="{{ $contact['instagram'] }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.266.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.848 0-3.204.012-3.584.07-4.849.149-3.225 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        @endif

                        @if($contact['facebook'] !== '#')
                        <a href="{{ $contact['facebook'] }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                            </svg>
                        </a>
                        @endif

                        @if($contact['linkedin'] !== '#')
                        <a href="{{ $contact['linkedin'] }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">{{ $settings['site_name'] ?? 'Nexts' }}</h3>
                <p class="text-gray-400">A plataforma completa para gestão de clubes de futebol.</p>
                <div class="flex gap-4 mt-6">
                    @if($contact['instagram'] !== '#')
                    <a href="{{ $contact['instagram'] }}" class="text-gray-400 hover:text-white transition-all"><i class="fab fa-instagram text-xl"></i></a>
                    @endif
                    @if($contact['facebook'] !== '#')
                    <a href="{{ $contact['facebook'] }}" class="text-gray-400 hover:text-white transition-all"><i class="fab fa-facebook-f text-xl"></i></a>
                    @endif
                    @if($contact['linkedin'] !== '#')
                    <a href="{{ $contact['linkedin'] }}" class="text-gray-400 hover:text-white transition-all"><i class="fab fa-linkedin-in text-xl"></i></a>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Produto</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('marketing.home') }}#features" class="hover:text-white">Funcionalidades</a></li>
                    <li><a href="{{ route('marketing.home') }}#pricing" class="hover:text-white">Preços</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Suporte</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="{{ route('marketing.contact') }}" class="hover:text-white">Contato</a></li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-sm">{{ $contact['email'] }}</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Termos de Uso</a></li>
                    <li><a href="#" class="hover:text-white">Privacidade</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Nexts' }}. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
@endsection

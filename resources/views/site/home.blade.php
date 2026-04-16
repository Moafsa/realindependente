@extends('layouts.site')

@section('title', 'Início')
@section('description', 'Bem-vindo ao nosso clube de futebol')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 text-white">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Real Independent
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                Clube de Futebol Profissional
            </p>
            <p class="text-lg mb-12 max-w-3xl mx-auto">
                Formando campeões através de treinamento de excelência, valores sólidos e tecnologia de ponta. 
                Nossa missão é desenvolver atletas completos, tanto dentro quanto fora dos campos.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#teams" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Nossas Equipes
                </a>
                <a href="#contact" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    Entre em Contato
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-blue-600">247</div>
                <div class="text-gray-600">Atletas Ativos</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-green-600">15</div>
                <div class="text-gray-600">Anos de História</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-purple-600">8</div>
                <div class="text-gray-600">Categorias</div>
            </div>
            <div class="space-y-2">
                <div class="text-3xl md:text-4xl font-bold text-orange-600">45</div>
                <div class="text-gray-600">Títulos Conquistados</div>
            </div>
        </div>
    </div>
</section>

<!-- Teams Section -->
<section id="teams" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Nossas Equipes
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Desenvolvemos atletas em todas as categorias, desde a base até o profissional, 
                com metodologia própria e acompanhamento individualizado.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Sub-13 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Sub-13</h3>
                    <p class="text-blue-100">Categoria de Base</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">32</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnico:</span>
                            <span class="font-semibold">Carlos Silva</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">18h às 19h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sub-15 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Sub-15</h3>
                    <p class="text-green-100">Categoria de Base</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">28</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnico:</span>
                            <span class="font-semibold">Maria Santos</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">19h às 20h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sub-17 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Sub-17</h3>
                    <p class="text-purple-100">Categoria de Base</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">25</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnico:</span>
                            <span class="font-semibold">João Oliveira</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">20h às 21h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sub-20 -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Sub-20</h3>
                    <p class="text-orange-100">Categoria Pré-Profissional</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">22</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnico:</span>
                            <span class="font-semibold">Pedro Costa</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">17h às 18h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profissional -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Profissional</h3>
                    <p class="text-red-100">Categoria Principal</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">18</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnico:</span>
                            <span class="font-semibold">Roberto Silva</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">16h às 17h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>

            <!-- Feminino -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Feminino</h3>
                    <p class="text-pink-100">Categoria Feminina</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Atletas:</span>
                            <span class="font-semibold">24</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Técnica:</span>
                            <span class="font-semibold">Ana Lima</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Horário:</span>
                            <span class="font-semibold">15h às 16h30</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Temporada Ativa
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Nossa Metodologia
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Utilizamos tecnologia de ponta e metodologia própria para desenvolver 
                atletas completos e preparados para o alto rendimento.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- IA Training -->
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Treinamento com IA</h3>
                <p class="text-gray-600">Planos personalizados gerados por inteligência artificial para cada atleta.</p>
            </div>

            <!-- Performance -->
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Acompanhamento Individual</h3>
                <p class="text-gray-600">Monitoramento constante da performance e evolução de cada atleta.</p>
            </div>

            <!-- Nutrition -->
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Nutrição Personalizada</h3>
                <p class="text-gray-600">Planos alimentares específicos para cada atleta e objetivo.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Entre em Contato
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Interessado em fazer parte da nossa família? Entre em contato conosco 
                e descubra como podemos ajudar no desenvolvimento do seu atleta.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Endereço</h3>
                        <p class="text-gray-600">Rua das Flores, 123<br>Centro - São Paulo/SP<br>CEP: 01234-567</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Telefone</h3>
                        <p class="text-gray-600">(11) 3456-7890<br>(11) 98765-4321</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">E-mail</h3>
                        <p class="text-gray-600">contato@realindependent.com.br<br>info@realindependent.com.br</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">Horário de Funcionamento</h3>
                        <p class="text-gray-600">Segunda a Sexta: 14h às 22h<br>Sábado: 8h às 18h<br>Domingo: Fechado</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Envie sua Mensagem</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Seu nome completo">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="seu@email.com">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                        <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="(11) 99999-9999">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoria de Interesse</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option>Sub-13</option>
                            <option>Sub-15</option>
                            <option>Sub-17</option>
                            <option>Sub-20</option>
                            <option>Profissional</option>
                            <option>Feminino</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Conte-nos sobre seu interesse..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                        Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
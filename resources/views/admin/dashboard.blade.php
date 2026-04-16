@extends('layouts.admin')

@section('content')
<!-- Dashboard Header -->
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
        <p class="text-gray-600 mt-2">Bem-vindo de volta! Aqui está um resumo do seu clube.</p>
    </div>
    <div class="flex space-x-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Novo Atleta
        </button>
        <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Relatórios
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Athletes -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total de Atletas</p>
                <p class="text-3xl font-bold text-gray-900">247</p>
                <p class="text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    +12% este mês
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Teams -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Equipes Ativas</p>
                <p class="text-3xl font-bold text-gray-900">8</p>
                <p class="text-blue-600 text-sm mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Todas ativas
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Receita Mensal</p>
                <p class="text-3xl font-bold text-gray-900">R$ 45.230</p>
                <p class="text-green-600 text-sm mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                    +8% este mês
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- AI Plans Generated -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Planos IA Gerados</p>
                <p class="text-3xl font-bold text-gray-900">156</p>
                <p class="text-purple-600 text-sm mt-1">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Este mês
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Athletes -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Atletas Recentes</h2>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver todos</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Athlete 1 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face" 
                             alt="João Silva" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">João Silva</h3>
                            <p class="text-gray-600 text-sm">Sub-15 • Atacante</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Cadastrado há</p>
                            <p class="font-semibold text-gray-900">2 dias</p>
                        </div>
                    </div>

                    <!-- Athlete 2 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face" 
                             alt="Pedro Santos" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Pedro Santos</h3>
                            <p class="text-gray-600 text-sm">Sub-17 • Meio-campo</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Cadastrado há</p>
                            <p class="font-semibold text-gray-900">5 dias</p>
                        </div>
                    </div>

                    <!-- Athlete 3 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=50&h=50&fit=crop&crop=face" 
                             alt="Ana Costa" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Ana Costa</h3>
                            <p class="text-gray-600 text-sm">Feminino • Atacante</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Cadastrado há</p>
                            <p class="font-semibold text-gray-900">1 semana</p>
                        </div>
                    </div>

                    <!-- Athlete 4 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=50&h=50&fit=crop&crop=face" 
                             alt="Carlos Lima" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Carlos Lima</h3>
                            <p class="text-gray-600 text-sm">Sub-20 • Zagueiro</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Cadastrado há</p>
                            <p class="font-semibold text-gray-900">1 semana</p>
                        </div>
                    </div>

                    <!-- Athlete 5 -->
                    <div class="flex items-center space-x-4">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=50&h=50&fit=crop&crop=face" 
                             alt="Rafael Oliveira" 
                             class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">Rafael Oliveira</h3>
                            <p class="text-gray-600 text-sm">Profissional • Goleiro</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Cadastrado há</p>
                            <p class="font-semibold text-gray-900">2 semanas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & AI Stats -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Ações Rápidas</h2>
            </div>
            <div class="p-6 space-y-4">
                <button class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Cadastrar Atleta
                </button>
                <button class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Gerar Relatório
                </button>
                <button class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-purple-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Criar Plano IA
                </button>
                <button class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-700 transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Cobrança Mensal
                </button>
            </div>
        </div>

        <!-- AI Performance -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Performance da IA</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Planos de Treino</span>
                    <span class="font-semibold text-gray-900">89 gerados</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Planos Nutricionais</span>
                    <span class="font-semibold text-gray-900">67 gerados</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Taxa de Aprovação</span>
                    <span class="font-semibold text-green-600">94%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Tempo Médio</span>
                    <span class="font-semibold text-gray-900">2.3s</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Financial Overview -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Atividade Recente</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Novo atleta cadastrado: <span class="font-semibold">João Silva</span></p>
                        <p class="text-xs text-gray-500">há 2 horas</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Plano de treino gerado para <span class="font-semibold">Pedro Santos</span></p>
                        <p class="text-xs text-gray-500">há 4 horas</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Cobrança processada: <span class="font-semibold">R$ 150,00</span></p>
                        <p class="text-xs text-gray-500">há 6 horas</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-orange-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Relatório mensal enviado por email</p>
                        <p class="text-xs text-gray-500">ontem</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Nova equipe criada: <span class="font-semibold">Sub-13 Feminino</span></p>
                        <p class="text-xs text-gray-500">há 2 dias</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Visão Financeira</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver detalhes</a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Monthly Revenue Chart Placeholder -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Receita Mensal</h3>
                        <span class="text-2xl font-bold text-green-600">R$ 45.230</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full" style="width: 85%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Meta: R$ 50.000</p>
                </div>

                <!-- Payment Status -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-gray-900">Status de Pagamentos</h3>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pagamentos em dia</span>
                        <span class="font-semibold text-green-600">89%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Em atraso</span>
                        <span class="font-semibold text-red-600">11%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Pendentes</span>
                        <span class="font-semibold text-yellow-600">5</span>
                    </div>
                </div>

                <!-- Quick Financial Actions -->
                <div class="space-y-2">
                    <button class="w-full bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors">
                        Enviar Lembretes de Pagamento
                    </button>
                    <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                        Gerar Relatório Financeiro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
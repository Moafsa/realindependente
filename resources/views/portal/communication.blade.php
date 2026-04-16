@extends('layouts.portal')

@section('title', 'Comunicação')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Comunicação</h1>
                    <p class="mt-1 text-sm text-gray-600">Mural, mensagens e notificações</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Tabs -->
        <div class="bg-white shadow-lg rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button onclick="showTab('announcements')" 
                            id="tab-announcements"
                            class="tab-button active px-6 py-4 text-sm font-medium text-center border-b-2 border-blue-500 text-blue-600">
                        Mural
                    </button>
                    <button onclick="showTab('messages')" 
                            id="tab-messages"
                            class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Mensagens
                    </button>
                    <button onclick="showTab('notifications')" 
                            id="tab-notifications"
                            class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Notificações
                    </button>
                </nav>
            </div>
        </div>

        <!-- Announcements Tab -->
        <div id="content-announcements" class="tab-content">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Mural de Avisos</h3>
                    <p class="text-sm text-gray-600">Avisos e comunicados do clube</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4" id="announcements-list">
                        <!-- Sample Announcement -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            Importante
                                        </span>
                                        <span class="text-xs text-gray-500">{{ now()->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Treino Cancelado</h4>
                                    <p class="text-sm text-gray-600">
                                        O treino de hoje foi cancelado devido às condições climáticas. 
                                        Retomaremos as atividades amanhã no horário normal.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State (hidden by default) -->
                        <div id="announcements-empty" class="text-center py-12 hidden">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum aviso</h3>
                            <p class="mt-1 text-sm text-gray-500">Não há avisos no momento.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Tab -->
        <div id="content-messages" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Messages List -->
                <div class="lg:col-span-1 bg-white shadow-lg rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Conversas</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @if($coach)
                        <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick="selectConversation({{ $coach->id }}, '{{ $coach->name }}')">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-semibold">{{ substr($coach->name, 0, 1) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $coach->name }}</p>
                                    <p class="text-xs text-gray-500">Técnico</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="p-4 text-center text-gray-500 text-sm">
                            <p>Nenhuma outra conversa</p>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="lg:col-span-2 bg-white shadow-lg rounded-lg flex flex-col">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 id="chat-title" class="text-lg font-medium text-gray-900">Selecione uma conversa</h3>
                    </div>
                    <div id="chat-messages" class="flex-1 p-6 overflow-y-auto" style="max-height: 500px;">
                        <div class="text-center text-gray-500 py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p>Selecione uma conversa para começar</p>
                        </div>
                    </div>
                    <div id="chat-input-container" class="hidden px-6 py-4 border-t border-gray-200">
                        <form id="message-form" class="flex items-center space-x-4">
                            @csrf
                            <input type="hidden" id="recipient-id" name="recipient_id">
                            <input type="text" 
                                   id="message-input" 
                                   name="message" 
                                   placeholder="Digite sua mensagem..."
                                   required
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                                Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div id="content-notifications" class="tab-content hidden">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Notificações</h3>
                        <p class="text-sm text-gray-600">Suas notificações e alertas</p>
                    </div>
                    <button onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800">
                        Marcar todas como lidas
                    </button>
                </div>
                <div class="divide-y divide-gray-200">
                    <!-- Sample Notification -->
                    <div class="p-6 hover:bg-gray-50 transition cursor-pointer" onclick="markAsRead(1)">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">Novo plano de treino disponível</p>
                                    <span class="text-xs text-gray-500">{{ now()->subHours(2)->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">
                                    Seu novo plano de treino personalizado foi gerado e está disponível para visualização.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="notifications-empty" class="text-center py-12 hidden">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma notificação</h3>
                        <p class="mt-1 text-sm text-gray-500">Você está em dia!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/portal-communication.js') }}"></script>
@endsection


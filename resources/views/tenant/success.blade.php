@extends('layouts.app')

@section('title', 'Cadastro Realizado com Sucesso')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-lg shadow-xl p-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Cadastro Realizado com Sucesso!</h1>
            
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
            @endif

            <p class="text-lg text-gray-600 mb-8">
                Seu clube foi criado com sucesso! Em breve você receberá um e-mail de boas-vindas com instruções de acesso.
            </p>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Próximos Passos:</h2>
                <ol class="space-y-3 text-gray-700">
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">1</span>
                        <span>Verifique seu e-mail para confirmar sua conta</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">2</span>
                        <span>Acesse seu painel através do subdomínio criado</span>
                    </li>
                    <li class="flex items-start">
                        <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold mr-3">3</span>
                        <span>Complete o cadastro inicial e comece a usar o sistema</span>
                    </li>
                </ol>
            </div>

            <!-- Access Link -->
            @if(session('subdomain'))
            <div class="mb-8">
                <p class="text-sm text-gray-600 mb-2">Acesse seu painel em:</p>
                <a href="https://{{ session('subdomain') }}.{{ config('tenancy.central_domains')[0] ?? 'meuclube.app' }}" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Acessar Painel
                </a>
            </div>
            @endif

            <!-- Support -->
            <div class="border-t pt-6">
                <p class="text-sm text-gray-600 mb-4">
                    Precisa de ajuda? Entre em contato conosco:
                </p>
                <div class="flex justify-center space-x-6">
                    <a href="{{ route('marketing.contact') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Central de Ajuda
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="mailto:suporte@realindependent.com" class="text-blue-600 hover:text-blue-700 font-medium">
                        E-mail de Suporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


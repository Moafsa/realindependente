<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a Senha - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-600 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">RI</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Nexts</h1>
            <p class="text-blue-100">Recuperação de Acesso</p>
        </div>

        <!-- Forgot Password Form -->
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <div class="mb-6 text-center">
                <p class="text-gray-600 text-sm">
                    Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para você escolher uma nova.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-mail de Cadastro
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="seu@email.com"
                           required 
                           autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                    Enviar Link de Recuperação
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar para o Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>

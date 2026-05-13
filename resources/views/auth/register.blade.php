<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-br from-green-600 via-teal-600 to-cyan-600 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-3xl font-bold bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent">RI</span>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Nexts</h1>
            <p class="text-green-100">Crie sua conta</p>
        </div>

        <!-- Register Form -->
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="Seu nome completo"
                           required 
                           autofocus>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="seu@email.com"
                           required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-6">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefone (Opcional)
                    </label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                           placeholder="(11) 99999-9999">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Conta
                    </label>
                    <select id="role" 
                            name="role"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('role') border-red-500 @enderror"
                            required>
                        <option value="">Selecione seu tipo de conta</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="coach" {{ old('role') == 'coach' ? 'selected' : '' }}>Técnico</option>
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="••••••••"
                           required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Senha
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="••••••••"
                           required>
                </div>

                <!-- Terms -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <input type="checkbox" 
                               id="terms" 
                               name="terms"
                               class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 rounded focus:ring-green-500 mt-1"
                               required>
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            Eu aceito os 
                            <a href="#" class="text-green-600 hover:text-green-800">termos de uso</a> 
                            e a 
                            <a href="#" class="text-green-600 hover:text-green-800">política de privacidade</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-teal-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                    Criar Conta
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 font-semibold">
                        Faça login aqui
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
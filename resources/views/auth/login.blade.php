@php
    $primaryColor = $settings['primary_color'] ?? '#2563eb';
    $secondaryColor = $settings['secondary_color'] ?? '#7c3aed';
    $siteName = $settings['site_name'] ?? config('app.name');
    $logo = $settings['site_logo'] ?? null;
    if (!$logo) {
        $logo = \App\Models\SiteSetting::getCentral('site_logo');
    }
    $logoUrl = $logo ? \Illuminate\Support\Facades\Storage::url($logo) : null;
    
    $faviconUrl = $logoUrl ? $logoUrl . '?v=2' : asset('favicons/nexts_favicon.png');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $siteName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <style>
        .custom-gradient {
            background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});
        }
        .custom-button {
            background: linear-gradient(to right, {{ $primaryColor }}, {{ $secondaryColor }});
        }
        .custom-button:hover {
            filter: brightness(1.1);
        }
        .custom-text {
            background: linear-gradient(to right, {{ $primaryColor }}, {{ $secondaryColor }});
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .custom-focus:focus {
            --tw-ring-color: {{ $primaryColor }};
            border-color: {{ $primaryColor }};
        }
        .custom-checkbox:checked {
            background-color: {{ $primaryColor }};
            border-color: {{ $primaryColor }};
        }
        .custom-link {
            color: {{ $primaryColor }};
        }
        .custom-link:hover {
            color: {{ $secondaryColor }};
        }
    </style>
</head>
<body class="custom-gradient min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            @if($logoUrl)
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg overflow-hidden p-2">
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="w-full h-full object-contain">
                </div>
            @else
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <span class="text-3xl font-bold custom-text">{{ substr($siteName, 0, 2) }}</span>
                </div>
            @endif
            <h1 class="text-3xl font-bold text-white mb-2">{{ $siteName }}</h1>
            <p class="text-white/80">Faça login em sua conta</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 custom-focus focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="seu@email.com"
                           required 
                           autofocus>
                    @error('email')
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 custom-focus focus:border-transparent @error('password') border-red-500 @enderror"
                           placeholder="••••••••"
                           required>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="w-4 h-4 bg-gray-100 border-gray-300 rounded custom-checkbox">
                        <label for="remember" class="ml-2 text-sm text-gray-600">
                            Lembrar de mim
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm custom-link">
                        Esqueceu a senha?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full custom-button text-white py-3 px-4 rounded-lg font-semibold focus:outline-none focus:ring-2 custom-focus focus:ring-offset-2 transition-all duration-200">
                    Entrar
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Quer gerenciar seu clube?
                    <a href="{{ route('register') }}" class="custom-link font-semibold">
                        Crie sua conta aqui
                    </a>
                </p>
            </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
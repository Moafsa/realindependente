<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    @php
        $favicon = null;
        if (tenancy()->initialized) {
            $favicon = \App\Models\SiteSetting::get('site_logo') ?? tenant('logo');
        } else {
            $favicon = \App\Models\SiteSetting::getCentral('site_logo');
        }
        
        $faviconUrl = asset('favicons/nexts_favicon.png');
        if ($favicon) {
            if (str_starts_with($favicon, 'http')) {
                $faviconUrl = $favicon;
            } elseif (tenancy()->initialized) {
                $faviconUrl = route('tenant.assets', ['path' => $favicon]);
            } else {
                $faviconUrl = \Illuminate\Support\Facades\Storage::url($favicon);
            }
        }
    @endphp
    <link rel="icon" type="image/png" href="{{ $faviconUrl }}">

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                },
            },
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        @yield('content')
    </div>
</body>
</html>

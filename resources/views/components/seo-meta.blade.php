@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'url' => null,
])

@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name'));
    $siteDescription = \App\Models\SiteSetting::get('site_description', '');
    $siteUrl = url('/');
    $defaultImage = \App\Models\SiteSetting::get('site_logo') ? route('tenant.assets', ['path' => \App\Models\SiteSetting::get('site_logo')]) : asset('images/default-og.jpg');
    
    $finalTitle = $title ? $title . ' - ' . $siteName : $siteName;
    $finalDescription = $description ?? $siteDescription ?? '';
    $finalImage = $image ?? $defaultImage;
    $finalUrl = $url ?? request()->url();
@endphp

<!-- Primary Meta Tags -->
<title>{{ $finalTitle }}</title>
<meta name="title" content="{{ $finalTitle }}">
<meta name="description" content="{{ $finalDescription }}">
<meta name="keywords" content="futebol, clube, atletas, treinamento, esportes">
<meta name="author" content="{{ $siteName }}">
<meta name="robots" content="index, follow">
<meta name="language" content="Portuguese">
<meta name="revisit-after" content="7 days">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $finalUrl }}">
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:image" content="{{ $finalImage }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="pt_BR">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $finalUrl }}">
<meta name="twitter:title" content="{{ $finalTitle }}">
<meta name="twitter:description" content="{{ $finalDescription }}">
<meta name="twitter:image" content="{{ $finalImage }}">

<!-- Additional Meta Tags -->
<link rel="canonical" href="{{ $finalUrl }}">
<meta name="theme-color" content="#2563eb">


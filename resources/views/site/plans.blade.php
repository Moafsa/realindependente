@extends('layouts.site')

@section('title', 'Planos de Assinatura')
@section('description', 'Escolha o plano ideal para a sua jornada')

@section('content')
<!-- Hero Section -->
@php
    $plansBanner = $settings['plans_banner'] ?? null;
    $plansTitle = $settings['plans_title'] ?? 'Planos de Assinatura';
    $plansSubtitle = $settings['plans_subtitle'] ?? 'Escolha o melhor plano e faça parte da nossa comunidade.';
@endphp
<section class="relative text-white py-20 bg-gray-900" 
         style="@if($plansBanner) background-image: url('{{ Storage::url($plansBanner) }}'); @else background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); @endif background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-6">{{ $plansTitle }}</h1>
        <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
            {{ $plansSubtitle }}
        </p>
    </div>
</section>

<!-- Plans Pricing Cards -->
<section class="py-20 bg-gray-50">
    @include('site.partials.plans_grid', ['showTitle' => false])
</section>
@endsection

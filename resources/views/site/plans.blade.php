@extends('layouts.site')

@section('title', 'Planos de Assinatura')
@section('description', 'Escolha o plano ideal para a sua jornada')

@section('content')
<!-- Hero Section -->
<section class="relative text-white py-20 bg-gray-900" 
         style="background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Planos de Assinatura</h1>
        <p class="text-xl md:text-2xl opacity-90 max-w-3xl mx-auto">
            Escolha o melhor plano e faça parte da nossa comunidade.
        </p>
    </div>
</section>

<!-- Plans Pricing Cards -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($plans as $plan)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow flex flex-col border border-gray-100 {{ $plan->is_featured ? 'ring-4 ring-primary transform scale-105' : '' }}">
                @if($plan->is_featured)
                <div class="bg-primary text-white text-center py-2 text-sm font-bold uppercase tracking-widest">
                    Mais Popular
                </div>
                @endif
                <div class="p-8 text-center border-b border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    <div class="mt-4 flex items-baseline justify-center text-5xl font-extrabold text-gray-900">
                        <span class="text-3xl font-medium text-gray-500 mr-2">R$</span>
                        {{ number_format($plan->price, 2, ',', '.') }}
                    </div>
                    @php
                        $cycleLabels = [
                            'MONTHLY' => 'por mês',
                            'QUARTERLY' => 'a cada 3 meses',
                            'SEMIANNUALLY' => 'a cada 6 meses',
                            'YEARLY' => 'por ano'
                        ];
                        $cycle = $plan->attributes['cycle'] ?? 'MONTHLY';
                    @endphp
                    <p class="text-gray-500 mt-2 font-medium">{{ $cycleLabels[$cycle] ?? 'mensal' }}</p>
                </div>
                
                <div class="p-8 flex flex-col flex-1 bg-gray-50">
                    @php
                        $features = $plan->attributes['features'] ?? [];
                        $training = $plan->attributes['training_details'] ?? [];
                        
                        $featureLabels = [
                            'insurance' => 'Seguro Atleta',
                            'evaluation' => 'Avaliação Médica/Física',
                            'training_plan' => 'Treino Personalizado',
                            'diet_plan' => 'Dieta Personalizada',
                            'whatsapp_support' => 'Suporte WhatsApp',
                        ];
                    @endphp

                    <div class="space-y-4 mb-8">
                        @foreach($featureLabels as $key => $label)
                            @if($features[$key] ?? false)
                            <div class="flex items-center text-sm text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $label }}
                            </div>
                            @endif
                        @endforeach

                        @if($training['days_per_week'] ?? false)
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="far fa-calendar-check text-blue-500 mr-3 w-5 text-center"></i>
                            {{ $training['days_per_week'] }} dias de treino / semana
                        </div>
                        @endif

                        @if($training['hours_per_day'] ?? false)
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="far fa-clock text-blue-500 mr-3 w-5 text-center"></i>
                            {{ $training['hours_per_day'] }} de duração
                        </div>
                        @endif

                        @if($training['uniform'] ?? false)
                        <div class="flex items-center text-sm text-gray-700">
                            <i class="fas fa-tshirt text-blue-500 mr-3 w-5 text-center"></i>
                            {{ $training['uniform'] }}
                        </div>
                        @endif

                        @if($training['other_details'] ?? false)
                        <div class="flex items-center text-sm text-gray-600 italic">
                            <i class="fas fa-info-circle text-gray-400 mr-3 w-5 text-center"></i>
                            {{ $training['other_details'] }}
                        </div>
                        @endif
                    </div>
                    
                    @if($plan->description)
                    <div class="text-gray-500 mb-8 prose text-xs border-t border-gray-100 pt-4">
                        {!! nl2br(e($plan->description)) !!}
                    </div>
                    @endif
                    
                    <div class="mt-auto">
                        @if($plan->is_active)
                        <a href="{{ route('site.subscribe', $plan) }}" 
                           class="block w-full py-4 px-8 rounded-xl text-center font-bold text-lg text-white transition-all transform hover:scale-105 {{ $plan->is_featured ? 'bg-primary hover:bg-opacity-90 shadow-lg' : 'bg-gray-800 hover:bg-gray-700' }}">
                            @php
                                $hasSub = Auth::check() && Auth::user()->athlete && Auth::user()->athlete->asaas_subscription_id;
                            @endphp
                            {{ $hasSub ? 'Mudar de Plano' : 'Matricular-se' }}
                        </a>
                        @else
                        <button disabled class="w-full bg-gray-300 text-gray-500 py-4 px-8 rounded-xl font-bold text-lg cursor-not-allowed">
                            Indisponível
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-4 text-xl font-bold text-gray-900">Nenhum plano disponível</h3>
                <p class="mt-2 text-gray-500">Ainda não temos planos de assinatura cadastrados.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@extends('layouts.site')

@section('title', 'Nossa Identidade')
@section('description', 'Conheça a história e os valores por trás do Nexts')

@section('content')
<!-- Hero Section Premium -->
<section class="relative py-32 flex items-center bg-[#050505] text-white overflow-hidden">
    <div class="absolute inset-0 opacity-30">
        @if($settings['site_hero_image'] ?? null)
            <img src="{{ Storage::url($settings['site_hero_image']) }}" alt="Background" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-2xl">
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black bg-primary uppercase tracking-widest mb-6 anim-fade-in">
                Nossa Identidade
            </span>
            <h1 class="text-5xl md:text-7xl font-black mb-8 leading-tight anim-slide-up">
                {{ $settings['site_name'] ?? 'Nexts' }}
            </h1>
            <p class="text-xl text-gray-400 font-medium leading-relaxed mb-8 anim-slide-up-delay">
                {{ $settings['site_description'] ?? 'Formando não apenas atletas de elite, mas cidadãos comprometidos com a excelência.' }}
            </p>
        </div>
    </div>
</section>

<!-- Nossa História - Layout de Revista -->
<section class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-1">
                <div class="flex lg:flex-col items-center gap-8 justify-center overflow-x-auto lg:overflow-visible py-4">
                    <span class="text-4xl font-black text-gray-100 rotate-0 lg:-rotate-90 origin-center">HIS.</span>
                    <span class="text-4xl font-black text-gray-100 rotate-0 lg:-rotate-90 origin-center">TOR.</span>
                    <span class="text-4xl font-black text-gray-100 rotate-0 lg:-rotate-90 origin-center">Y.</span>
                </div>
            </div>
            
            <div class="lg:col-span-5">
                <h2 class="text-4xl font-black text-gray-900 mb-8 leading-tight">
                    {{ $settings['about_title'] ?? 'Um Legado' }} <br>
                    <span class="text-primary">{{ $settings['about_subtitle'] ?? 'em cada jogada.' }}</span>
                </h2>
                <div class="space-y-6 text-lg text-gray-600 leading-relaxed font-medium">
                    <p>
                        {{ $settings['about_history'] ?? 'O Nexts nasceu da visão de que o esporte é a ferramenta mais poderosa de transformação social. Nossa trajetória é pavimentada por suor, técnica e uma fé inabalável no potencial humano.' }}
                    </p>
                    <div class="pl-6 border-l-4 border-primary">
                        "{{ $settings['about_quote'] ?? 'Onde outros veem apenas jogadores, nós enxergamos o futuro campeão e o cidadão exemplar.' }}"
                    </div>
                    <p>
                        {{ $settings['about_mission'] ?? 'Nossa missão evoluiu para além dos campos. Hoje, somos um ecossistema completo de desenvolvimento esportivo, unindo ciência, tecnologia e paixão.' }}
                    </p>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="relative group">
                    <div class="absolute -inset-4 bg-blue-600/10 rounded-[3rem] -z-10 group-hover:scale-105 transition duration-700"></div>
                    @if($settings['about_image'] ?? null)
                        <img src="{{ Storage::url($settings['about_image']) }}" alt="História" class="rounded-[2.5rem] shadow-2xl w-full h-[500px] object-cover">
                    @else
                        <div class="rounded-[2.5rem] shadow-2xl w-full h-[500px] bg-gray-100 flex items-center justify-center overflow-hidden">
                             <div class="text-8xl font-black text-gray-200">RI</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Métricas de Impacto -->
<section class="py-20 bg-gray-50 border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
                <p class="text-5xl font-black text-primary mb-2">500+</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Atletas Ativos</p>
            </div>
            <div class="text-center p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
                <p class="text-5xl font-black text-gray-900 mb-2">15</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Equipes</p>
            </div>
            <div class="text-center p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
                <p class="text-5xl font-black text-primary mb-2">25</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Títulos Oficiais</p>
            </div>
            <div class="text-center p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
                <p class="text-5xl font-black text-gray-900 mb-2">10</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Filiais</p>
            </div>
        </div>
    </div>
</section>

<!-- Nossos Pilares / Valores -->
<section class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-gray-900 mb-4">Nossos Pilares</h2>
            <p class="text-gray-500 font-medium">O que nos mantém firmes em direção ao topo</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pilar 1 -->
            <div class="group p-10 bg-gray-50 rounded-[2.5rem] hover:bg-primary hover:text-white transition-all duration-500">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-md flex items-center justify-center mb-8 group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">{{ $settings['about_pillar1_title'] ?? 'Transparência' }}</h3>
                <p class="opacity-80 leading-relaxed">{{ $settings['about_pillar1_text'] ?? 'Gestão profissional e clara em todos os níveis, garantindo confiança.' }}</p>
            </div>

            <!-- Pilar 2 -->
            <div class="group p-10 bg-gray-50 rounded-[2.5rem] hover:bg-gray-900 hover:text-white transition-all duration-500">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-md flex items-center justify-center mb-8 group-hover:scale-110 transition text-gray-900">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">{{ $settings['about_pillar2_title'] ?? 'Inovação' }}</h3>
                <p class="opacity-80 leading-relaxed">{{ $settings['about_pillar2_text'] ?? 'Uso de inteligência artificial e análise de dados para potencializar resultados.' }}</p>
            </div>

            <!-- Pilar 3 -->
            <div class="group p-10 bg-gray-50 rounded-[2.5rem] hover:bg-primary hover:text-white transition-all duration-500">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-md flex items-center justify-center mb-8 group-hover:scale-110 transition text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4">{{ $settings['about_pillar3_title'] ?? 'Família' }}</h3>
                <p class="opacity-80 leading-relaxed">{{ $settings['about_pillar3_text'] ?? 'Desenvolvimento humano focado na união entre atleta, clube e responsáveis.' }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="max-w-7xl mx-auto px-4 mb-24">
    <div class="bg-primary rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        <div class="relative z-10 max-w-2xl mx-auto">
            <h2 class="text-4xl md:text-5xl font-black mb-6">{{ $settings['about_cta_title'] ?? 'Pronto para escrever sua história?' }}</h2>
            <p class="text-gray-100 text-lg mb-10 font-medium">{{ $settings['about_cta_text'] ?? 'Junte-se ao Nexts e transforme seu talento em excelência tecnológica e esportiva.' }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('site.contact') }}" class="px-8 py-4 bg-white text-primary font-black rounded-2xl hover:bg-gray-50 transition shadow-xl">Quero ser Avaliado</a>
                <a href="{{ route('site.teams') }}" class="px-8 py-4 bg-white/10 text-white font-black rounded-2xl hover:bg-white/20 transition">Ver Equipes</a>
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .anim-fade-in { animation: slideUp 0.8s ease-out forwards; }
    .anim-slide-up { animation: slideUp 1s ease-out forwards; }
    .anim-slide-up-delay { animation: slideUp 1.2s ease-out forwards; opacity: 0; }
</style>
@endsection


@extends('layouts.site')

@section('title', 'Contato')

@section('content')
<!-- Header -->
<section class="relative py-32 flex items-center bg-[#050505] text-white overflow-hidden">
    <div class="absolute inset-0 opacity-40">
        @if($settings['contact_banner'] ?? null)
            <img src="{{ Storage::url($settings['contact_banner']) }}" alt="Background Contato" class="w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-primary to-secondary"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-transparent"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-2xl">
            <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black bg-primary uppercase tracking-widest mb-6 animate__animated animate__fadeInUp">
                Fale Conosco
            </span>
            <h1 class="text-5xl md:text-7xl font-black mb-8 leading-tight animate__animated animate__fadeInUp">
                {{ $settings['contact_title'] ?? 'Entre em Contato' }}
            </h1>
            <p class="text-xl text-gray-300 font-medium leading-relaxed mb-8 animate__animated animate__fadeInUp">
                {{ $settings['contact_subtitle'] ?? 'Estamos aqui para ajudar e responder suas dúvidas' }}
            </p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-24 relative overflow-hidden bg-white">
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-secondary/5 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <!-- Contact Form Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200/50 p-8 lg:p-12 border border-gray-100 animate__animated animate__fadeInLeft">
                <div class="mb-10">
                    <h2 class="text-3xl font-black text-gray-900 mb-4">Envie uma Mensagem</h2>
                    <p class="text-gray-500 font-medium">Preencha o formulário abaixo e entraremos em contato o mais breve possível.</p>
                </div>
                
                <form method="POST" action="{{ route('site.contact.submit') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-bold text-gray-700 ml-1">Nome Completo</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all outline-none font-medium text-gray-900"
                                   placeholder="Como devemos te chamar?">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-bold text-gray-700 ml-1">E-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all outline-none font-medium text-gray-900"
                                   placeholder="seu@email.com">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="phone" class="text-sm font-bold text-gray-700 ml-1">WhatsApp</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all outline-none font-medium text-gray-900"
                                   placeholder="(00) 00000-0000">
                        </div>
                        
                        <div class="space-y-2">
                            <label for="subject" class="text-sm font-bold text-gray-700 ml-1">Assunto</label>
                            <select name="subject" id="subject" required
                                    class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all outline-none font-medium text-gray-900 appearance-none">
                                <option value="Matrícula">Desejo me matricular</option>
                                <option value="Dúvida">Dúvida sobre treinos</option>
                                <option value="Parceria">Parcerias / Patrocínio</option>
                                <option value="Outro">Outros assuntos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="message" class="text-sm font-bold text-gray-700 ml-1">Mensagem</label>
                        <textarea name="message" id="message" rows="5" required
                                  class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all outline-none font-medium text-gray-900 resize-none"
                                  placeholder="Escreva detalhadamente o que você precisa...">{{ old('message') }}</textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-white py-5 rounded-2xl font-black text-lg hover:bg-primary-dark shadow-xl shadow-primary/20 transition-all hover:-translate-y-1 flex items-center justify-center gap-3">
                        <span>Enviar Mensagem</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>
            </div>
            
            <!-- Information & Map -->
            <div class="space-y-10 animate__animated animate__fadeInRight">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 gap-6">
                    @if($settings['contact_phone'] ?? false)
                    <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:border-primary/20 transition-colors group">
                        <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Telefone / WhatsApp</h3>
                            <p class="text-xl font-black text-gray-900 tracking-tight">{{ $settings['contact_phone'] }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($settings['contact_email'] ?? false)
                    <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:border-primary/20 transition-colors group">
                        <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-secondary group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">E-mail</h3>
                            <p class="text-xl font-black text-gray-900 tracking-tight">{{ $settings['contact_email'] }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($settings['contact_address'] ?? false)
                    <div class="flex items-center gap-6 p-6 bg-gray-50 rounded-3xl border border-gray-100 hover:border-primary/20 transition-colors group">
                        <div class="h-14 w-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Localização</h3>
                            <p class="text-lg font-black text-gray-900 tracking-tight leading-tight">{{ $settings['contact_address'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Map Container -->
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-[2.5rem] blur opacity-25"></div>
                    <div class="relative bg-gray-100 rounded-[2.5rem] overflow-hidden h-[400px] shadow-inner border border-gray-100">
                        @if($mapboxToken && ($settings['contact_address'] ?? false))
                            <div id="map" class="w-full h-full"></div>
                        @elseif($settings['contact_address'] ?? false)
                            <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q={{ urlencode($settings['contact_address']) }}&t=&z=15&ie=UTF8&iwloc=&output=embed">
                            </iframe>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                <p class="font-bold uppercase tracking-widest text-[10px]">Mapa indisponível</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($mapboxToken && ($settings['contact_address'] ?? false))
@push('scripts')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = '{{ $mapboxToken }}';
    const address = '{{ $settings['contact_address'] }}';
    
    // Geocoding manual via API (simples)
    fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}`)
        .then(response => response.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const coords = data.features[0].center;
                const map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/light-v11',
                    center: coords,
                    zoom: 15,
                    pitch: 45,
                    bearing: -17
                });

                // Add marker
                new mapboxgl.Marker({ color: '{{ $settings['color_primary'] ?? '#2563eb' }}' })
                    .setLngLat(coords)
                    .addTo(map);

                // Add controls
                map.addControl(new mapboxgl.NavigationControl());
            }
        });
</script>
@endpush
@endif
@endsection

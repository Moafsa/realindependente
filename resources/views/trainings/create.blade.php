@extends('layouts.dashboard')

@section('title', 'Nova Atividade')

@section('styles')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
<style>
    .mapboxgl-ctrl-geocoder { min-width: 100%; border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: none; z-index: 1; }
    #map { height: 350px; border-radius: 1rem; border: 1px solid #e5e7eb; position: relative; overflow: hidden; }
    .mapboxgl-marker { cursor: pointer; }
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('trainings.index') }}" class="mr-4 p-2 rounded-full hover:bg-gray-100 transition">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Agendar Nova Atividade</h1>
    </div>

    <form action="{{ route('trainings.store') }}" method="POST" id="trainingForm" class="bg-white shadow-xl rounded-2xl border border-gray-100 overflow-hidden">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="address" id="address">

        <div class="p-8 space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Título da Atividade</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ex: Treino de Finalização / Jogo Amistoso"
                       class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Tipo</label>
                    <select name="type" required class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>Treino</option>
                        <option value="match" {{ old('type') == 'match' ? 'selected' : '' }}>Jogo</option>
                        <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Evento</option>
                    </select>
                </div>

                <!-- Team -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Equipe (Opcional)</label>
                    <select name="team_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                        <option value="">Geral (Todos os Atletas)</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Data</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                </div>

                <!-- Time -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Hora</label>
                    <input type="time" name="time" value="{{ old('time', '09:00') }}" required
                           class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                </div>
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Nome do Local</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Ex: CT Principal / Estádio Municipal"
                       class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition mb-4">
                
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Endereço (Busca no Mapa)</label>
                <div id="geocoder" class="mb-4"></div>
                <div id="map"></div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Descrição / Notas</label>
                <textarea name="description" rows="4" placeholder="Detalhes adicionais, requisitos, etc."
                          class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">{{ old('description') }}</textarea>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start space-x-3">
                <svg class="w-6 h-6 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="text-xs text-blue-800">
                    <p class="font-bold uppercase tracking-widest mb-1">Dica de Notificação</p>
                    <p>Ao agendar esta atividade, os atletas vinculados serão notificados via portal. (Integração com WhatsApp disponível via eventos).</p>
                </div>
            </div>
        </div>

        <div class="p-8 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
            <a href="{{ route('trainings.index') }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 uppercase tracking-widest transition">Cancelar</a>
            <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl text-sm font-black uppercase tracking-[0.2em] hover:bg-blue-700 transition shadow-lg shadow-blue-600/20">
                Agendar Atividade
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<script>
(function () {
    const mapboxToken = '{{ $settings['mapbox_public_token'] ?? '' }}';

    if (!mapboxToken) {
        document.getElementById('map').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#fef3c7;border-radius:1rem;color:#92400e;font-size:14px;font-weight:600;padding:1rem;text-align:center;">⚠️ Token do Mapbox não configurado.<br>Acesse <strong>Admin → Configurações</strong> e insira o Mapbox Public Token.</div>';
        document.getElementById('geocoder').style.display = 'none';
        return;
    }

    mapboxgl.accessToken = mapboxToken;

    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/dark-v11',
        center: [-46.6333, -23.5505],
        zoom: 12
    });

    const geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
        placeholder: 'Pesquise o endereço do treino...',
        countries: 'br'
    });

    document.getElementById('geocoder').appendChild(geocoder.onAdd(map));

    let marker = null;

    geocoder.on('result', (e) => {
        const coords = e.result.geometry.coordinates;
        const address = e.result.place_name;

        document.getElementById('latitude').value = coords[1];
        document.getElementById('longitude').value = coords[0];
        document.getElementById('address').value = address;

        if (marker) marker.remove();
        marker = new mapboxgl.Marker({ color: '#3b82f6' })
            .setLngLat(coords)
            .addTo(map);
    });
})();
</script>
@endsection

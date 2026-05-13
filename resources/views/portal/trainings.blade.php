@extends('layouts.portal')

@section('title', 'Agenda de Treinos')
@section('body_class', 'bg-[#0f172a]')
@section('main_class', 'bg-[#0f172a]')

@section('header_styles')
<style>
    .glass-card {
        background: rgba(30, 41, 59, 0.5);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .training-row:hover {
        background: rgba(255, 255, 255, 0.03);
    }
    .modal-backdrop {
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(8px);
    }
</style>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
@endsection

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight uppercase">Agenda <span class="text-blue-500">Completa</span></h2>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-1">
                {{ $athlete->team->name ?? 'Sem Categoria' }} • Cronograma de atividades
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.dashboard') }}" class="px-6 py-3 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-widest text-gray-300 hover:bg-white/10 transition-all">
                Voltar ao Início
            </a>
        </div>
    </div>

    <!-- Trainings List -->
    <div class="glass-card rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Data & Horário</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Atividade</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Local</th>
                        <th class="px-6 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($trainings as $training)
                        <tr class="training-row transition-all cursor-pointer group" 
                            onclick="showTrainingDetails({{ json_encode($training) }})">
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-blue-600/10 border border-blue-500/20 text-blue-500">
                                        <span class="text-[10px] font-black leading-none">{{ \Carbon\Carbon::parse($training->date)->translatedFormat('d') }}</span>
                                        <span class="text-[8px] font-black uppercase tracking-tighter">{{ \Carbon\Carbon::parse($training->date)->translatedFormat('M') }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black text-white uppercase">{{ \Carbon\Carbon::parse($training->date)->translatedFormat('l') }}</p>
                                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $training->time ?? '--:--' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-white uppercase tracking-tight">{{ $training->title }}</span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $training->type ?? 'Treino' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-2 text-gray-400">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest">{{ $training->location ?? 'Centro de Treinamento' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                @php
                                    $isPast = \Carbon\Carbon::parse($training->date)->isPast();
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest {{ $isPast ? 'bg-gray-500/10 text-gray-500 border border-gray-500/20' : 'bg-green-500/10 text-green-500 border border-green-500/20 animate-pulse' }}">
                                    {{ $isPast ? 'Finalizado' : 'Confirmado' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="opacity-20 mb-4">
                                    <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Nenhum treino agendado para esta equipe</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trainings->hasPages())
            <div class="px-6 py-4 border-t border-white/5 bg-white/5">
                {{ $trainings->links() }}
            </div>
        @endif
    </div>
    </div>
</div>

<!-- Training Details Modal -->
<div id="trainingModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity modal-backdrop" aria-hidden="true" onclick="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom glass-card rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-white/10 animate-in fade-in zoom-in duration-300">
            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-2xl font-black text-white tracking-tight uppercase italic" id="modal-title">Detalhes da Atividade</h3>
                        <p id="modal-date" class="text-blue-400 text-xs font-bold uppercase tracking-widest mt-1"></p>
                    </div>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2">Localização</p>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-green-400/10 flex items-center justify-center text-green-400 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                </div>
                                <div>
                                    <p id="modal-location" class="text-white font-bold text-sm"></p>
                                    <p id="modal-address" class="text-gray-500 text-xs mt-1"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2">Horário</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-400/10 flex items-center justify-center text-blue-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p id="modal-time" class="text-white font-bold text-sm"></p>
                            </div>
                        </div>

                        <div id="distance-container" class="hidden">
                            <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-2">Sua Distância</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-400/10 flex items-center justify-center text-purple-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <p id="modal-distance" class="text-white font-bold text-sm"></p>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div id="modal-map" class="h-48 w-full rounded-2xl bg-white/5 border border-white/5"></div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a id="btn-modal-route" href="#" target="_blank" class="flex-1 flex items-center justify-center space-x-2 py-4 bg-green-500 hover:bg-green-400 rounded-2xl text-xs font-black text-black uppercase tracking-widest transition-all shadow-lg shadow-green-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <span>ABRIR NO GOOGLE MAPS</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script>
    let map = null;
    let marker = null;
    let userMarker = null;

    function showTrainingDetails(training) {
        const modal = document.getElementById('trainingModal');
        document.getElementById('modal-title').innerText = training.title;
        document.getElementById('modal-date').innerText = new Date(training.date).toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('modal-location').innerText = training.location || 'Centro de Treinamento';
        document.getElementById('modal-address').innerText = training.address || 'Endereço não informado';
        document.getElementById('modal-time').innerText = training.time || '--:--';
        
        modal.classList.remove('hidden');

        if (training.latitude && training.longitude) {
            initModalMap([training.longitude, training.latitude]);
            updateUserDistance([training.longitude, training.latitude]);
        } else {
            document.getElementById('modal-map').classList.add('hidden');
            document.getElementById('distance-container').classList.add('hidden');
        }
    }

    function closeModal() {
        document.getElementById('trainingModal').classList.add('hidden');
        if (map) {
            map.remove();
            map = null;
        }
    }

    function initModalMap(coords) {
        document.getElementById('modal-map').classList.remove('hidden');
        mapboxgl.accessToken = '{{ $settings['mapbox_public_token'] ?? '' }}';
        
        map = new mapboxgl.Map({
            container: 'modal-map',
            style: 'mapbox://styles/mapbox/dark-v11',
            center: coords,
            zoom: 15,
            attributionControl: false
        });

        marker = new mapboxgl.Marker({ color: '#4ade80' })
            .setLngLat(coords)
            .addTo(map);
            
        document.getElementById('btn-modal-route').href = `https://www.google.com/maps/dir/?api=1&destination=${coords[1]},${coords[0]}`;
    }

    function updateUserDistance(destCoords) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const userLoc = [position.coords.longitude, position.coords.latitude];
                
                if (map) {
                    if (userMarker) userMarker.remove();
                    userMarker = new mapboxgl.Marker({ color: '#3b82f6' })
                        .setLngLat(userLoc)
                        .addTo(map);

                    const bounds = new mapboxgl.LngLatBounds()
                        .extend(userLoc)
                        .extend(destCoords);
                    map.fitBounds(bounds, { padding: 40 });
                }

                const dist = calculateDistance(userLoc[1], userLoc[0], destCoords[1], destCoords[0]);
                document.getElementById('distance-container').classList.remove('hidden');
                document.getElementById('modal-distance').innerText = dist.toFixed(1) + ' km de você';
                
                document.getElementById('btn-modal-route').href = `https://www.google.com/maps/dir/?api=1&origin=${userLoc[1]},${userLoc[0]}&destination=${destCoords[1]},${destCoords[0]}`;
            });
        }
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2-lat1) * Math.PI / 180;
        const dLon = (lon2-lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Auto-open from dashboard
    window.addEventListener('load', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const trainingId = urlParams.get('id');
        if (trainingId) {
            // Find training in the table and open its details
            const trainings = @json($trainings->items());
            const training = trainings.find(t => t.id == trainingId);
            if (training) {
                showTrainingDetails(training);
            }
        }
    });
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Conectar WhatsApp')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center">
    <div class="max-w-md w-full text-center space-y-12">
        <!-- Status & Header -->
        <div class="animate-in fade-in slide-in-from-bottom duration-700">
            <div class="inline-flex items-center gap-3 px-4 py-2 bg-white/5 border border-white/10 rounded-full mb-6">
                <div class="w-2 h-2 rounded-full {{ ($status['connected'] ?? false) ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                    {{ ($status['connected'] ?? false) ? 'Sistema Conectado' : 'Aguardando Conexão' }}
                </span>
            </div>
            
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase italic">
                WhatsApp <span class="text-indigo-500">Bot</span>
            </h1>
            <p class="text-gray-500 mt-4 font-medium">Escaneie o código para ativar a inteligência artificial no seu WhatsApp.</p>
        </div>

        <!-- Action Area -->
        <div class="relative group">
            @if($status['connected'] ?? false)
                <!-- Connected State -->
                <div class="bg-white/[0.02] border border-white/5 rounded-[3rem] p-12 backdrop-blur-3xl animate-in zoom-in duration-500">
                    <div class="w-24 h-24 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-8 border border-emerald-500/20">
                        <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2 uppercase italic tracking-tight">{{ $status['pushname'] ?? 'WhatsApp Ativo' }}</h3>
                    <p class="text-gray-500 text-sm font-medium mb-10">{{ $status['phone'] ?? 'Conectado com sucesso' }}</p>
                    
                    <form action="{{ route('admin.whatsapp.disconnect') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[10px] font-black text-rose-500 hover:text-rose-400 uppercase tracking-[0.3em] transition-all">
                            Desconectar Aparelho
                        </button>
                    </form>
                </div>
            @else
                <!-- Connect State -->
                <div id="qr-section" class="hidden animate-in zoom-in duration-500">
                    <div class="bg-white p-8 rounded-[3rem] shadow-[0_0_100px_rgba(99,102,241,0.1)] inline-block relative overflow-hidden">
                        <div id="qr-placeholder" class="w-64 h-64 bg-gray-50 flex items-center justify-center rounded-2xl">
                            <div class="animate-spin h-10 w-10 border-4 border-indigo-600 border-t-transparent rounded-full"></div>
                        </div>
                        <img id="qr-image" src="" alt="QR Code" class="w-64 h-64 rounded-2xl hidden relative z-10">
                        
                        <div id="qr-expired" class="absolute inset-0 bg-white/95 z-20 flex flex-col items-center justify-center p-8 text-center hidden">
                            <p class="text-black font-black uppercase text-xs mb-4 tracking-widest">Código Expirado</p>
                            <button onclick="generateQr()" class="px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Recarregar</button>
                        </div>
                    </div>
                    <p class="mt-8 text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Escaneie com seu celular</p>
                </div>

                <div id="intro-section">
                    <button onclick="generateQr()" id="btn-main" class="group relative px-12 py-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-[2rem] font-black text-sm uppercase tracking-[0.3em] shadow-2xl shadow-indigo-600/30 transition-all hover:-translate-y-1">
                        <span class="relative z-10 flex items-center gap-4">
                            Gerar QR Code
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    let checkInterval;

    function generateQr() {
        const intro = document.getElementById('intro-section');
        const qrSection = document.getElementById('qr-section');
        const img = document.getElementById('qr-image');
        const placeholder = document.getElementById('qr-placeholder');
        const expired = document.getElementById('qr-expired');

        if (intro) intro.classList.add('hidden');
        qrSection.classList.remove('hidden');
        img.classList.add('hidden');
        placeholder.classList.remove('hidden');
        expired.classList.add('hidden');

        fetch("{{ route('admin.whatsapp.qr') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success && data.qr) {
                    img.src = data.qr;
                    img.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    startPolling();
                } else {
                    alert(data.message || 'Erro ao gerar QR Code');
                    if (intro) intro.classList.remove('hidden');
                    qrSection.classList.add('hidden');
                }
            })
            .catch(() => {
                alert('Erro de conexão com o servidor');
                if (intro) intro.classList.remove('hidden');
                qrSection.classList.add('hidden');
            });
    }

    function startPolling() {
        if (checkInterval) clearInterval(checkInterval);
        checkInterval = setInterval(() => {
            fetch("{{ route('admin.whatsapp.status') }}")
                .then(res => res.json())
                .then(data => {
                    if (data && data.connected) {
                        clearInterval(checkInterval);
                        location.reload();
                    }
                });
        }, 5000);

        // Expire after 2 mins
        setTimeout(() => {
            if (checkInterval) {
                clearInterval(checkInterval);
                document.getElementById('qr-expired').classList.remove('hidden');
            }
        }, 120000);
    }
</script>
@endpush
@endsection

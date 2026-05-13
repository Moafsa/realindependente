@extends('layouts.dashboard')

@section('title', 'Comunicação')

@section('content')
<div class="h-[calc(100vh-10rem)] flex flex-col bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
    
    <!-- Header -->
    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        @if($chatTarget)
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-3">
                    {{ substr($chatTarget->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ $chatTarget->name }}</h3>
                    <span class="text-[10px] text-green-600 font-bold">Online</span>
                </div>
            </div>
        @elseif(isset($targetAthlete))
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-gray-400 flex items-center justify-center text-white font-bold mr-3">
                    {{ substr($targetAthlete->full_name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-red-600">{{ $targetAthlete->full_name }} (Sem Conta)</h3>
                    <span class="text-[10px] text-red-500 font-bold italic">Atleta não possui conta de usuário para mensagens diretas</span>
                </div>
            </div>
        @else
            <h3 class="text-sm font-bold text-gray-400">Selecione um atleta para conversar</h3>
        @endif
        
        <div class="flex items-center space-x-4">
            <button onclick="document.getElementById('mural-section').classList.remove('hidden')" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-all border border-blue-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <span class="text-[10px] font-black uppercase tracking-widest">Mural</span>
            </button>
        </div>
    </div>

    <!-- Chat Messages -->
    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-100/50">
        @if($chatTarget)
            @foreach($messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-2 shadow-sm {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none' }}">
                    @if($msg->attachment_path)
                        <div class="mb-2">
                            @if($msg->attachment_type === 'image')
                                <img src="{{ Storage::url($msg->attachment_path) }}" class="max-w-full rounded-lg shadow-sm" onclick="window.open(this.src)">
                            @else
                                <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank" class="flex items-center p-2 bg-black/5 rounded text-[10px] font-bold">📎 ARQUIVO</a>
                            @endif
                        </div>
                    @endif
                    <p class="text-sm">{{ $msg->content }}</p>
                    <div class="flex justify-end mt-1 space-x-1 opacity-50">
                        <span class="text-[9px]">{{ $msg->created_at->format('H:i') }}</span>
                        @if($msg->sender_id === auth()->id())
                            <span class="text-[10px]">{{ $msg->read_at ? '✓✓' : '✓' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    <!-- Input -->
    @if($chatTarget)
    <div class="p-3 bg-white border-t border-gray-200">
        <form id="chat-form" onsubmit="sendMessage(event)" class="flex items-center space-x-2">
            @csrf
            <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $chatTarget->id ?? '' }}">
            <input type="file" name="attachment" id="attachment" class="hidden" onchange="handleFileSelect(this)">
            <button type="button" onclick="document.getElementById('attachment').click()" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
            <input type="text" name="content" id="message-content" placeholder="Escreva aqui..." autocomplete="off"
                   class="flex-1 bg-gray-100 border-none rounded-full px-4 py-2 text-sm text-black focus:ring-2 focus:ring-blue-500 outline-none" style="color: black !important;">
            <button type="submit" class="p-2 text-blue-600 transform hover:scale-110 transition-all">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
            </button>
        </form>
    </div>
    @endif

    <!-- Athlete Selector at Bottom -->
    <div class="bg-gray-50 border-t border-gray-200 p-2 overflow-x-auto flex space-x-3 scrollbar-hide">
        @foreach($athletes as $ath)
        <a href="{{ route('communication.index', ['athlete_id' => $ath->id]) }}" 
           class="flex flex-col items-center flex-shrink-0 group">
            <div class="h-12 w-12 rounded-full border-2 {{ (isset($chatTarget) && $chatTarget->id === ($ath->user->id ?? 0)) ? 'border-blue-600 p-0.5' : 'border-transparent' }} transition-all">
                <div class="h-full w-full rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-sm overflow-hidden shadow-sm">
                    @if($ath->profile_photo)
                        <img src="{{ Storage::url($ath->profile_photo) }}" class="h-full w-full object-cover">
                    @else
                        {{ substr($ath->full_name, 0, 1) }}
                    @endif
                </div>
            </div>
            <span class="text-[9px] mt-1 font-bold text-gray-500 uppercase tracking-tighter truncate w-12 text-center {{ (isset($chatTarget) && $chatTarget->id === ($ath->user->id ?? 0)) ? 'text-blue-600' : '' }}">{{ explode(' ', $ath->full_name)[0] }}</span>
            @if($ath->unread_count > 0)
                <span class="absolute -mt-14 ml-8 bg-red-500 text-white text-[8px] font-bold px-1.5 rounded-full border border-white">{{ $ath->unread_count }}</span>
            @endif
        </a>
        @endforeach
    </div>
</div>
<!-- Mural Section (Overlay) -->
<div id="mural-section" class="fixed inset-0 z-50 bg-white flex flex-col hidden animate-in slide-in-from-bottom-full duration-300">
    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
        <h2 class="font-bold text-lg">Mural de Avisos</h2>
        <button onclick="document.getElementById('mural-section').classList.add('hidden')" class="p-2 text-gray-400">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-4">
        @foreach($notices as $notice)
        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <div class="flex justify-between items-center mb-2">
                <span class="text-[10px] font-black uppercase text-blue-600">{{ $notice->priority }}</span>
                <span class="text-[10px] text-gray-400">{{ $notice->created_at->diffForHumans() }}</span>
            </div>
            <h4 class="font-bold text-gray-900">{{ $notice->title }}</h4>
            <p class="text-sm text-gray-600">{{ $notice->content }}</p>
        </div>
        @endforeach
    </div>
    <div class="p-4 border-t bg-gray-50">
        <form action="{{ route('communication.mural.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="title" placeholder="Título" required class="border-gray-200 rounded-xl p-3 text-sm focus:ring-blue-500">
                <select name="priority" required class="border-gray-200 rounded-xl p-3 text-sm focus:ring-blue-500 bg-white">
                    <option value="medium">Prioridade Média</option>
                    <option value="low">Baixa</option>
                    <option value="high">Alta</option>
                    <option value="important">Importante 🚨</option>
                </select>
            </div>
            <select name="team_id" class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-blue-500 bg-white">
                <option value="">Todos os Atletas (Global)</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            <textarea name="content" placeholder="Escreva o aviso aqui..." required class="w-full border-gray-200 rounded-xl p-3 text-sm focus:ring-blue-500 h-24"></textarea>
            <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all uppercase tracking-widest text-xs">Publicar no Mural</button>
        </form>
    </div>
</div>

<script>
    function sendMessage(e) {
        e.preventDefault();
        const form = e.target;
        const content = document.getElementById('message-content').value.trim();
        if (!content && !document.getElementById('attachment').files.length) return;

        fetch("{{ route('communication.store') }}", {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('message-content').value = '';
                document.getElementById('attachment').value = '';
                pollMessages();
            }
        });
    }

    function pollMessages() {
        const id = document.getElementById('receiver_id')?.value;
        if (!id) return;
        fetch(`{{ route('communication.messages') }}?target_id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) renderMessages(data.messages);
        });
    }

    function renderMessages(messages) {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        let html = '';
        messages.forEach(msg => {
            html += `
            <div class="flex ${msg.is_own ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-[80%] rounded-2xl px-4 py-2 shadow-sm ${msg.is_own ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none'}">
                    ${msg.attachment_url ? `<div class="mb-2"><img src="${msg.attachment_url}" class="rounded-lg max-w-full"></div>` : ''}
                    <p class="text-sm">${msg.content || ''}</p>
                    <div class="flex justify-end mt-1 space-x-1 opacity-50">
                        <span class="text-[9px]">${msg.time}</span>
                        ${msg.is_own ? `<span class="text-[10px]">${msg.read_at ? '✓✓' : '✓'}</span>` : ''}
                    </div>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
    }

    setInterval(pollMessages, 5000);
    window.onload = () => {
        const m = document.getElementById('chat-messages');
        if (m) m.scrollTop = m.scrollHeight;
    };
</script>
@endsection

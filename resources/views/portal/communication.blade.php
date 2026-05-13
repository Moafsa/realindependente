@extends('layouts.portal')

@section('title', 'Mensagens')

@section('content')
<div class="h-[calc(100vh-10rem)] flex flex-col bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden mx-auto max-w-4xl">
    
    <!-- Header -->
    <div class="p-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center">
            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-3">
                {{ substr($coach->name ?? 'T', 0, 1) }}
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $coach->name ?? 'Treinador' }}</h3>
                <span class="text-[10px] text-green-600 font-bold">Online Agora</span>
            </div>
        </div>
        <button onclick="window.location.reload()" class="p-2 text-gray-400 hover:text-blue-600 transition-all">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </button>
    </div>

    <!-- Tab Content Container -->
    <div class="flex-1 flex flex-col min-h-0 relative">
        
        <!-- View 1: Chat -->
        <div id="chat-view" class="flex-1 flex flex-col min-h-0">
            <!-- Messages Area -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-100/50">
                @foreach($messages as $msg)
                <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-2xl px-4 py-2 shadow-sm {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none' }}">
                        @if($msg->attachment_path)
                            <div class="mb-2">
                                @if($msg->attachment_type === 'image')
                                    <img src="{{ Storage::url($msg->attachment_path) }}" class="max-w-full rounded-lg shadow-sm" onclick="window.open(this.src)">
                                @else
                                    <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank" class="flex items-center p-2 bg-black/5 rounded text-[10px] font-bold">📎 ARQUIVO</a>
                                @endif
                            </div>
                        @endif
                        <p class="text-sm leading-relaxed">{{ $msg->content }}</p>
                        <div class="flex justify-end mt-1 space-x-1 opacity-50">
                            <span class="text-[9px]">{{ $msg->created_at->format('H:i') }}</span>
                            @if($msg->sender_id === auth()->id())
                                <span class="text-[10px]">{{ $msg->read_at ? '✓✓' : '✓' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="p-3 bg-white border-t border-gray-200">
                <form id="chat-form" onsubmit="sendMessage(event)" class="flex items-center space-x-2">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiver_id" value="{{ $chatTarget->id ?? '' }}">
                    <input type="file" name="attachment" id="attachment" class="hidden" onchange="handleFileSelect(this)">
                    <button type="button" onclick="document.getElementById('attachment').click()" class="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                    <input type="text" name="content" id="message-content" placeholder="Escreva sua mensagem..." autocomplete="off"
                           class="flex-1 bg-gray-100 border-none rounded-full px-4 py-2 text-sm text-black focus:ring-2 focus:ring-blue-500 outline-none" style="color: black !important;">
                    <button type="submit" class="p-2 text-blue-600 transform hover:scale-110 transition-all">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- View 2: Mural -->
        <div id="mural-view" class="hidden absolute inset-0 bg-white flex flex-col z-10">
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Histórico de Avisos</h3>
                @forelse($notices as $notice)
                    <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full {{ $notice->priority === 'important' ? 'bg-red-500' : 'bg-blue-500' }}"></div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[9px] font-bold uppercase {{ $notice->priority === 'important' ? 'text-red-500' : 'text-blue-500' }}">{{ $notice->priority }}</span>
                            <span class="text-[10px] text-gray-400">{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm mb-1">{{ $notice->title }}</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ $notice->content }}</p>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center opacity-20 text-center">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <p class="text-xs font-bold uppercase tracking-widest">Nenhum aviso no momento</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Bottom Navigation (Simplified Tabs) -->
    <div class="h-16 bg-gray-50 border-t border-gray-200 flex items-center justify-around flex-shrink-0 px-6">
        <button onclick="switchTab('chat')" id="tab-chat" class="flex flex-col items-center space-y-1 text-blue-600">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            <span class="text-[10px] font-black uppercase tracking-widest">Chat</span>
        </button>
        <button onclick="switchTab('mural')" id="tab-mural" class="flex flex-col items-center space-y-1 text-gray-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            <span class="text-[10px] font-black uppercase tracking-widest">Mural</span>
        </button>
    </div>
</div>

<script>
    function switchTab(tab) {
        const chatView = document.getElementById('chat-view');
        const muralView = document.getElementById('mural-view');
        const tabChat = document.getElementById('tab-chat');
        const tabMural = document.getElementById('tab-mural');

        if (tab === 'chat') {
            chatView.classList.remove('hidden');
            muralView.classList.add('hidden');
            tabChat.classList.add('text-blue-600');
            tabChat.classList.remove('text-gray-400');
            tabMural.classList.add('text-gray-400');
            tabMural.classList.remove('text-blue-600');
        } else {
            muralView.classList.remove('hidden');
            chatView.classList.add('hidden');
            tabMural.classList.add('text-blue-600');
            tabMural.classList.remove('text-gray-400');
            tabChat.classList.add('text-gray-400');
            tabChat.classList.remove('text-blue-600');
            
            // Mark mural as read
            fetch("{{ route('portal.notifications.mural-read') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => {
                // Update global badge immediately if possible
                const badge = document.getElementById('notification-badge');
                if (badge) badge.classList.add('hidden');
            });
        }
    }

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
                <div class="max-w-[85%] rounded-2xl px-4 py-2 shadow-sm ${msg.is_own ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none'}">
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

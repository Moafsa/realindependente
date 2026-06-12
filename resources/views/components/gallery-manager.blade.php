<div class="mt-8 bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Galeria de Mídia</h3>
            <p class="text-sm text-gray-500">Adicione fotos e vídeos (YouTube) à galeria.</p>
        </div>
        <button type="button" onclick="document.getElementById('add-media-modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
            Adicionar Mídia
        </button>
    </div>

    <div class="p-6">
        @if($galleryItems->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($galleryItems as $item)
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 bg-gray-50 aspect-video flex items-center justify-center">
                        @if($item->type === 'image')
                            <img src="{{ Storage::url($item->url) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        @elseif($item->type === 'video')
                            @php
                                // Extrair ID do YouTube para thumbnail (básico)
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $item->url, $match);
                                $youtubeId = $match[1] ?? null;
                            @endphp
                            @if($youtubeId)
                                <img src="https://img.youtube.com/vi/{{ $youtubeId }}/mqdefault.jpg" alt="Video" class="w-full h-full object-cover opacity-70">
                            @else
                                <div class="text-center text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-xs">Vídeo</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-black/50 rounded-full p-2 text-white">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                </div>
                            </div>
                        @endif

                        <!-- Botão de Excluir -->
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <form action="{{ route('admin.galleries.destroy', $item) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta mídia?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-600 text-white rounded-full hover:bg-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                Nenhuma mídia adicionada ainda.
            </div>
        @endif
    </div>
</div>

<!-- Modal Adicionar Mídia -->
<div id="add-media-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('add-media-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="galleryable_type" value="{{ $galleryableType ?? '' }}">
                <input type="hidden" name="galleryable_id" value="{{ $galleryableId ?? '' }}">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Adicionar Mídia
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Mídia</label>
                            <select name="type" id="media_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="toggleMediaType()">
                                <option value="image">Foto (Upload)</option>
                                <option value="video">Vídeo (YouTube)</option>
                            </select>
                        </div>

                        <div id="image_upload_field">
                            <label class="block text-sm font-medium text-gray-700">Arquivo da Imagem</label>
                            <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <div id="video_url_field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700">Link do YouTube</label>
                            <input type="url" name="url" placeholder="https://youtube.com/watch?v=..." class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Título (Opcional)</label>
                            <input type="text" name="title" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Salvar Mídia
                    </button>
                    <button type="button" onclick="document.getElementById('add-media-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleMediaType() {
    const type = document.getElementById('media_type').value;
    if (type === 'image') {
        document.getElementById('image_upload_field').classList.remove('hidden');
        document.getElementById('video_url_field').classList.add('hidden');
    } else {
        document.getElementById('image_upload_field').classList.add('hidden');
        document.getElementById('video_url_field').classList.remove('hidden');
    }
}
</script>

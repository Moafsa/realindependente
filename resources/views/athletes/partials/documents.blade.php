<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Documentação do Atleta</h3>
            <p class="text-sm text-gray-500">Envie e gerencie os documentos comprobatórios.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.athletes.documents.update', $athlete) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Documento do Atleta -->
            <div class="bg-white border rounded-lg p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-md font-medium text-gray-900">Documento do Atleta</h4>
                        <p class="text-xs text-gray-500 mt-1">RG, CPF ou CNH. (PDF, JPG, PNG)</p>
                    </div>
                    @if($athlete->athlete_document_path)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enviado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pendente
                        </span>
                    @endif
                </div>
                
                @if($athlete->athlete_document_path)
                    <div class="mt-4 mb-2">
                        <a href="{{ $athlete->athlete_document_url }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Visualizar Arquivo Atual
                        </a>
                    </div>
                @endif
                
                <div class="mt-3">
                    <input type="file" name="athlete_document" id="athlete_document" accept=".pdf,image/*"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    @error('athlete_document')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Comprovante de Residência -->
            <div class="bg-white border rounded-lg p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-md font-medium text-gray-900">Comprovante de Residência</h4>
                        <p class="text-xs text-gray-500 mt-1">Conta de água, luz, etc. (PDF, JPG, PNG)</p>
                    </div>
                    @if($athlete->residence_proof_path)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enviado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pendente
                        </span>
                    @endif
                </div>
                
                @if($athlete->residence_proof_path)
                    <div class="mt-4 mb-2">
                        <a href="{{ $athlete->residence_proof_url }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Visualizar Arquivo Atual
                        </a>
                    </div>
                @endif
                
                <div class="mt-3">
                    <input type="file" name="residence_proof" id="residence_proof" accept=".pdf,image/*"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    @error('residence_proof')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Documento do Responsável -->
            <div class="bg-white border rounded-lg p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-md font-medium text-gray-900">Documento do Responsável</h4>
                        <p class="text-xs text-gray-500 mt-1">Obrigatório para menores. (PDF, JPG, PNG)</p>
                    </div>
                    @if($athlete->guardian_document_path)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enviado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Não Enviado
                        </span>
                    @endif
                </div>
                
                @if($athlete->guardian_document_path)
                    <div class="mt-4 mb-2">
                        <a href="{{ $athlete->guardian_document_url }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Visualizar Arquivo Atual
                        </a>
                    </div>
                @endif
                
                <div class="mt-3">
                    <input type="file" name="guardian_document" id="guardian_document" accept=".pdf,image/*"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    @error('guardian_document')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Atestado Médico -->
            <div class="bg-white border rounded-lg p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h4 class="text-md font-medium text-gray-900">Atestado Médico</h4>
                        <p class="text-xs text-gray-500 mt-1">Liberação para esportes. (PDF, JPG, PNG)</p>
                    </div>
                    @if($athlete->medical_certificate_path)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Enviado
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pendente
                        </span>
                    @endif
                </div>
                
                @if($athlete->medical_certificate_path)
                    <div class="mt-4 mb-2">
                        <a href="{{ $athlete->medical_certificate_url }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Visualizar Arquivo Atual
                        </a>
                    </div>
                @endif
                
                <div class="mt-3">
                    <input type="file" name="medical_certificate" id="medical_certificate" accept=".pdf,image/*"
                           class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    @error('medical_certificate')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 text-sm font-medium rounded-lg hover:bg-blue-700 transition focus:ring-4 focus:ring-blue-200">
                Salvar Documentos
            </button>
        </div>
    </form>
</div>

@extends('layouts.dashboard')

@section('title', 'Perfil do SuperAdmin')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Informações do Perfil</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Gerencie seus dados de acesso e as informações de contato que aparecerão no site principal.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="shadow sm:rounded-md sm:overflow-hidden">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mail de Login</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <!-- Contact Info (Public) -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Informações de Contato (Site Público)</h4>
                            
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700">E-mail de Contato</label>
                                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $contact_email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $contact_phone) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="contact_whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                                    <input type="text" name="contact_whatsapp" id="contact_whatsapp" value="{{ old('contact_whatsapp', $contact_whatsapp) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6">
                                    <label for="contact_address" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                                    <textarea name="contact_address" id="contact_address" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ old('contact_address', $contact_address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Social Networks -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Redes Sociais</h4>
                            
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="social_instagram" class="block text-sm font-medium text-gray-700">Instagram (URL)</label>
                                    <input type="text" name="social_instagram" id="social_instagram" value="{{ old('social_instagram', $social_instagram) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="https://instagram.com/seuusuario">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="social_facebook" class="block text-sm font-medium text-gray-700">Facebook (URL)</label>
                                    <input type="text" name="social_facebook" id="social_facebook" value="{{ old('social_facebook', $social_facebook) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="social_linkedin" class="block text-sm font-medium text-gray-700">LinkedIn (URL)</label>
                                    <input type="text" name="social_linkedin" id="social_linkedin" value="{{ old('social_linkedin', $social_linkedin) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <!-- Password Update -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Alterar Senha (Opcional)</h4>
                            
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-3">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                                    <input type="password" name="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

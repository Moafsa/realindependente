@extends('layouts.site')

@section('title', $post->title . ' - ' . (tenant('name') ?? 'Clube'))

@section('meta')
    <meta name="description" content="{{ $post->meta_description }}">
@endsection

@section('content')
<article class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <header class="mb-12">
            <div class="flex items-center text-gray-500 mb-4">
                <a href="{{ route('site.blog') }}" class="hover:text-indigo-600 transition-colors">Blog</a>
                <span class="mx-2">/</span>
                <span>Notícias</span>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-6">{{ $post->title }}</h1>
            <div class="flex items-center text-sm text-gray-500">
                <i class="far fa-calendar-alt mr-2"></i>
                Publicado em {{ $post->published_at->format('d \d\e F \d\e Y') }}
            </div>
        </header>

        <!-- Featured Image -->
        @if($post->image_url)
            <div class="mb-12 rounded-xl overflow-hidden shadow-xl">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-auto">
            </div>
        @endif

        <!-- Content -->
        <div class="prose prose-lg prose-indigo max-w-none text-gray-700 leading-relaxed">
            {!! $post->content !!}
        </div>

        <!-- Footer / Sharing -->
        <footer class="mt-16 pt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-gray-600">
                    Gostou deste conteúdo? Compartilhe com outros torcedores!
                </div>
                <div class="flex gap-4">
                    <!-- Placeholder sharing buttons -->
                    <button class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook-f w-6 h-6 flex items-center justify-center"></i>
                    </button>
                    <button class="bg-blue-400 text-white p-2 rounded-full hover:bg-blue-500 transition-colors">
                        <i class="fab fa-twitter w-6 h-6 flex items-center justify-center"></i>
                    </button>
                    <button class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition-colors">
                        <i class="fab fa-whatsapp w-6 h-6 flex items-center justify-center"></i>
                    </button>
                </div>
            </div>
        </footer>

        <!-- Related Posts -->
        @if($relatedPosts->count() > 0)
            <section class="mt-20">
                <h3 class="text-2xl font-bold text-gray-900 mb-8">Outras Notícias</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $related)
                        <div class="group">
                            <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors mb-2">
                                <a href="{{ route('site.blog.show', $related->slug) }}">{{ $related->title }}</a>
                            </h4>
                            <p class="text-sm text-gray-500">{{ $related->published_at->format('d/m/Y') }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</article>
@endsection

@extends('layouts.site')

@section('title', 'Blog - ' . (tenant('name') ?? 'Clube'))

@section('content')
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl">Blog do Clube</h1>
            <p class="mt-4 text-xl text-gray-500">Acompanhe as últimas notícias, dicas e bastidores.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                    @if($post->image_url)
                        <img class="h-48 w-full object-cover" src="{{ $post->image_url }}" alt="{{ $post->title }}">
                    @else
                        <div class="h-48 w-full bg-indigo-600 flex items-center justify-center text-white">
                            <i class="fas fa-newspaper fa-4x opacity-50"></i>
                        </div>
                    @endif
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <i class="far fa-calendar-alt mr-2"></i>
                            {{ $post->published_at->format('d/m/Y') }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-3">
                            <a href="{{ route('site.blog.show', $post->slug) }}" class="hover:text-indigo-600 transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="text-gray-600 mb-4 flex-1">
                            {{ Str::limit($post->excerpt, 150) }}
                        </p>
                        <a href="{{ route('site.blog.show', $post->slug) }}" class="text-indigo-600 font-semibold hover:text-indigo-800 flex items-center">
                            Ler mais <i class="fas fa-arrow-right ml-2 text-sm"></i>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">Nenhum post publicado ainda. Volte em breve!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</section>
@endsection

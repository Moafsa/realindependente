@extends('layouts.site')

@section('title', 'Loja')
@section('description', 'Produtos oficiais do clube')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-orange-600 via-red-600 to-pink-600 text-white">
    <div class="absolute inset-0 bg-black opacity-40"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Loja</h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">Produtos Oficiais do Clube</p>
            <p class="text-lg mb-12 max-w-3xl mx-auto">
                Adquira os produtos oficiais e mostre seu apoio ao clube. 
                Camisetas, acessórios e muito mais com a qualidade que você merece.
            </p>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('site.store') }}" class="flex flex-wrap justify-center gap-4">
            <button type="button" 
                    onclick="window.location.href='{{ route('site.store') }}'"
                    class="px-6 py-3 {{ !request('type') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Todos os Produtos
            </button>
            @foreach(['physical' => 'Físicos', 'service' => 'Serviços', 'subscription' => 'Assinaturas'] as $type => $label)
            <button type="submit" 
                    name="type" 
                    value="{{ $type }}"
                    class="px-6 py-3 {{ request('type') === $type ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </form>
    </div>
</section>

<!-- Products Grid -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(request('search'))
        <div class="mb-6">
            <p class="text-gray-600">Resultados para: <strong>{{ request('search') }}</strong></p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($products as $product)
            <a href="{{ route('site.product', $product) }}" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="relative">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/400x400?text=' . urlencode($product->name) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-64 object-cover">
                    @if($product->stock_quantity !== null)
                    <div class="absolute top-4 right-4">
                        <span class="bg-{{ $product->stock_quantity > 0 ? 'green' : 'red' }}-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $product->stock_quantity > 0 ? 'Em Estoque' : 'Fora de Estoque' }}
                        </span>
                    </div>
                    @endif
                    @if($product->is_featured)
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Destaque
                        </span>
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $product->name }}</h3>
                    @if($product->description)
                    <p class="text-gray-600 mb-4 text-sm">{{ Str::limit($product->description, 80) }}</p>
                    @endif
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-2xl font-bold text-gray-900">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado</h3>
                <p class="mt-1 text-sm text-gray-500">Não há produtos disponíveis no momento.</p>
            </div>
            @endforelse
        </div>

        @if($products->hasPages())
        <div class="mt-12">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</section>
@endsection

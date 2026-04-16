@extends('layouts.site')

@section('title', $product->name)
@section('description', $product->description ?? 'Produto oficial do clube')
@section('og-image', $product->image ? asset('storage/' . $product->image) : null)
@section('og-type', 'product')

@section('content')
<!-- Product Detail -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="mb-6">
            <a href="{{ route('site.store') }}" class="text-blue-600 hover:text-blue-800">← Voltar para Loja</a>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Image -->
            <div>
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=' . urlencode($product->name) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full rounded-lg shadow-lg">
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                @if($product->description)
                <div class="mb-6">
                    <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                </div>
                @endif

                <div class="mb-6">
                    <div class="text-4xl font-bold text-gray-900 mb-2">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </div>
                    @if($product->stock_quantity !== null)
                    <div class="text-sm {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $product->stock_quantity > 0 ? 'Em estoque' : 'Fora de estoque' }}
                    </div>
                    @endif
                </div>

                @if($product->is_active && ($product->stock_quantity === null || $product->stock_quantity > 0))
                <form method="POST" action="{{ route('site.add-to-cart', $product) }}" class="mb-6">
                    @csrf
                    <div class="flex items-center space-x-4 mb-4">
                        <label for="quantity" class="text-sm font-medium text-gray-700">Quantidade:</label>
                        <input type="number" 
                               name="quantity" 
                               id="quantity" 
                               value="1" 
                               min="1" 
                               max="{{ $product->stock_quantity ?? 999 }}"
                               class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Adicionar ao Carrinho
                    </button>
                </form>
                @else
                <button disabled 
                        class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold cursor-not-allowed">
                    Produto Indisponível
                </button>
                @endif

                <!-- Product Details -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Tipo</dt>
                            <dd class="font-medium text-gray-900">{{ ucfirst($product->type) }}</dd>
                        </div>
                        @if($product->sku)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">SKU</dt>
                            <dd class="font-medium text-gray-900">{{ $product->sku }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Produtos Relacionados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($relatedProducts as $related)
            <a href="{{ route('site.product', $related) }}" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://via.placeholder.com/300' }}" 
                     alt="{{ $related->name }}" 
                     class="w-full h-48 object-cover">
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $related->name }}</h3>
                    <p class="text-lg font-bold text-gray-900">R$ {{ number_format($related->price, 2, ',', '.') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection


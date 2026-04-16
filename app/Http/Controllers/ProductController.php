<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Product::withTrashed();

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($request->status === 'out_of_stock') {
                    $query->where('stock_quantity', '<=', 0);
                }
            }

            // Filter by featured
            if ($request->filled('featured')) {
                $query->where('is_featured', $request->boolean('featured'));
            }

            $products = $query->latest()->paginate(20);
            $types = ['product', 'service', 'subscription', 'merchandise'];
        } catch (\Exception $e) {
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
            $types = ['product', 'service', 'subscription', 'merchandise'];
        }

        return view('products.index', compact('products', 'types'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $types = ['product' => 'Produto', 'service' => 'Serviço', 'subscription' => 'Assinatura', 'merchandise' => 'Mercadoria'];
        
        return view('products.create', compact('types'));
    }

    /**
     * Store a newly created product.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:product,service,subscription,merchandise',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock_quantity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'attributes' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
        ]);

        try {
            $product = new Product($request->except(['image']));
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $product->image = $path;
            }

            // Set default values
            $product->is_active = $request->boolean('is_active', true);
            $product->is_featured = $request->boolean('is_featured', false);
            
            // Stock quantity for non-physical products
            if ($product->type === 'service' || $product->type === 'subscription') {
                $product->stock_quantity = null;
            }

            $product->save();

            Log::info('Product created', [
                'product_id' => $product->id,
                'name' => $product->name,
                'type' => $product->type,
            ]);

            return redirect()->route('products.show', $product)
                ->with('success', 'Produto criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao criar produto: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        $product->load(['orderItems.order']);
        
        // Get product statistics
        $stats = [
            'total_sales' => $product->total_sales,
            'total_revenue' => $product->total_revenue,
            'stock_status' => $product->stock_status,
        ];

        return view('products.show', compact('product', 'stats'));
    }

    /**
     * Show the form for editing the product.
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $types = ['product' => 'Produto', 'service' => 'Serviço', 'subscription' => 'Assinatura', 'merchandise' => 'Mercadoria'];
        
        return view('products.edit', compact('product', 'types'));
    }

    /**
     * Update the specified product.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:product,service,subscription,merchandise',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'stock_quantity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'attributes' => 'nullable|array',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
        ]);

        try {
            $oldImage = $product->image;
            
            $product->update($request->except(['image']));

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
                
                $path = $request->file('image')->store('products', 'public');
                $product->update(['image' => $path]);
            }

            // Stock quantity for non-physical products
            if ($product->type === 'service' || $product->type === 'subscription') {
                $product->update(['stock_quantity' => null]);
            }

            Log::info('Product updated', [
                'product_id' => $product->id,
                'name' => $product->name,
            ]);

            return redirect()->route('products.show', $product)
                ->with('success', 'Produto atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar produto: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Remove the specified product.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            // Check if product has orders
            if ($product->orderItems()->count() > 0) {
                return back()->withErrors([
                    'error' => 'Não é possível excluir um produto que possui pedidos associados. Use a exclusão suave.'
                ]);
            }

            // Delete image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            Log::info('Product deleted', [
                'product_id' => $product->id,
            ]);

            return redirect()->route('products.index')
                ->with('success', 'Produto removido com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error deleting product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao remover produto: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle product status.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Product $product)
    {
        try {
            $product->update(['is_active' => !$product->is_active]);

            Log::info('Product status toggled', [
                'product_id' => $product->id,
                'is_active' => $product->is_active,
            ]);

            return redirect()->back()
                ->with('success', 'Status do produto atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error toggling product status', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update stock quantity.
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            $product->update(['stock_quantity' => $request->stock_quantity]);

            Log::info('Product stock updated', [
                'product_id' => $product->id,
                'stock_quantity' => $request->stock_quantity,
            ]);

            return redirect()->back()
                ->with('success', 'Estoque atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating product stock', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar estoque: ' . $e->getMessage()
            ]);
        }
    }
}


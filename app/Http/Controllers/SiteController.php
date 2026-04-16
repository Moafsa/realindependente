<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Order;
use App\Services\AsaasService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    private AsaasService $asaasService;
    private CartService $cartService;

    public function __construct(AsaasService $asaasService, CartService $cartService)
    {
        $this->asaasService = $asaasService;
        $this->cartService = $cartService;
    }

    /**
     * Show the public home page.
     */
    public function home()
    {
        // For now, return a simple view without database queries
        // until all migrations are complete
        return view('site.home');
    }

    /**
     * Show the about page.
     */
    public function about()
    {
        $settings = SiteSetting::getPublicSettings()->pluck('value', 'key');
        
        return view('site.about', compact('settings'));
    }

    /**
     * Show teams page.
     */
    public function teams()
    {
        try {
            $teams = Team::withCount('athletes')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabela não existir, retornar array vazio
            $teams = collect([]);
        }

        return view('site.teams', compact('teams'));
    }

    /**
     * Show specific team page.
     */
    public function team($id)
    {
        try {
            $team = Team::findOrFail($id);
            $athletes = $team->athletes()
                ->where('is_active', true)
                ->orderBy('full_name')
                ->get();
        } catch (\Exception $e) {
            abort(404);
        }

        return view('site.team', compact('team', 'athletes'));
    }

    /**
     * Show athletes page.
     */
    public function athletes()
    {
        try {
            $athletes = Athlete::with(['team'])
                ->where('is_active', true)
                ->orderBy('full_name')
                ->paginate(20);
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabela não existir, retornar array vazio
            $athletes = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
        }

        return view('site.athletes', compact('athletes'));
    }

    /**
     * Show specific athlete page.
     */
    public function athlete($id)
    {
        try {
            $athlete = Athlete::with(['team', 'branch'])->findOrFail($id);
        } catch (\Exception $e) {
            abort(404);
        }
        
        return view('site.athlete', compact('athlete'));
    }

    /**
     * Show store page.
     */
    public function store(Request $request)
    {
        try {
            $query = Product::where('is_active', true);

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }

            $products = $query->orderBy('name')->paginate(12);
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabela não existir, retornar array vazio
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                12,
                1
            );
        }

        return view('site.store', compact('products'));
    }

    /**
     * Show specific product page.
     */
    public function product(Product $product)
    {
        // Get related products (same type, excluding current)
        $relatedProducts = Product::where('type', $product->type)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('site.product', compact('product', 'relatedProducts'));
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . ($product->stock_quantity ?? 999),
        ]);

        if ($this->cartService->add($product, $request->quantity)) {
            return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
        }

        return redirect()->back()->with('error', 'Erro ao adicionar produto ao carrinho. Verifique a disponibilidade e estoque.');
    }

    /**
     * Remove product from cart.
     */
    public function removeFromCart(Request $request, Product $product)
    {
        if ($this->cartService->remove($product)) {
            return redirect()->back()->with('success', 'Produto removido do carrinho!');
        }

        return redirect()->back()->with('error', 'Erro ao remover produto do carrinho.');
    }

    /**
     * Update cart item quantity.
     */
    public function updateCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . ($product->stock_quantity ?? 999),
        ]);

        if ($this->cartService->update($product, $request->quantity)) {
            return redirect()->back()->with('success', 'Carrinho atualizado!');
        }

        return redirect()->back()->with('error', 'Erro ao atualizar carrinho. Verifique a disponibilidade e estoque.');
    }

    /**
     * Show cart page.
     */
    public function cart()
    {
        $items = $this->cartService->getItemsWithProducts();
        $total = $this->cartService->getTotal();
        $itemsCount = $this->cartService->getItemsCount();

        // Validate cart
        $validation = $this->cartService->validate();
        if (!$validation['valid']) {
            return view('site.cart', compact('items', 'total', 'itemsCount'))
                ->with('errors', $validation['errors']);
        }

        return view('site.cart', compact('items', 'total', 'itemsCount'));
    }

    /**
     * Show checkout page.
     */
    public function checkout()
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('site.store')->with('error', 'Carrinho vazio.');
        }

        // Validate cart before checkout
        $validation = $this->cartService->validate();
        if (!$validation['valid']) {
            return redirect()->route('site.cart')
                ->with('errors', $validation['errors'])
                ->with('error', 'Por favor, corrija os problemas no carrinho antes de finalizar a compra.');
        }

        $items = $this->cartService->getItemsWithProducts();
        $total = $this->cartService->getTotal();
        $itemsCount = $this->cartService->getItemsCount();

        return view('site.checkout', compact('items', 'total', 'itemsCount'));
    }

    /**
     * Process checkout.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('site.store')->with('error', 'Carrinho vazio.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['product']->price * $item['quantity'];
        }

        try {
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'status' => 'pending',
                'billing_address' => [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'address' => $request->billing_address,
                ],
            ]);

            // Create order items
            foreach ($cart as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->price,
                    'total' => $item['product']->price * $item['quantity'],
                ]);
            }

            // Cria ou busca customer no Asaas
            $customerId = null;
            if (Auth::check() && Auth::user()->asaas_customer_id) {
                $customerId = Auth::user()->asaas_customer_id;
            } else {
                // Cria novo customer no Asaas
                $customerData = [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                ];
                
                try {
                    $asaasCustomer = $this->asaasService->createCustomer($customerData);
                    $customerId = $asaasCustomer['id'];
                    
                    // Salva customer_id no usuário se estiver logado
                    if (Auth::check()) {
                        Auth::user()->update(['asaas_customer_id' => $customerId]);
                    }
                } catch (\Exception $e) {
                    Log::error('SiteController: Erro ao criar customer no Asaas', [
                        'error' => $e->getMessage(),
                    ]);
                    // Continua sem customer_id, Asaas pode criar automaticamente
                }
            }

            // Cria cobrança no Asaas
            $chargeData = [
                'customer_id' => $customerId,
                'value' => $total,
                'due_date' => now()->addDays(7)->format('Y-m-d'),
                'description' => "Pedido #{$order->id} - " . implode(', ', array_map(fn($item) => $item['product']->name, $cart)),
                'external_reference' => "order_{$order->id}",
                'billing_type' => $request->input('payment_method', 'PIX'),
            ];

            try {
                $charge = $this->asaasService->createCharge($chargeData);
                
                // Atualiza order com dados do Asaas
                $order->update([
                    'asaas_payment_id' => $charge['id'],
                    'asaas_payment_url' => $charge['invoiceUrl'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('SiteController: Erro ao criar cobrança no Asaas', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                
                // Order foi criado mas sem cobrança no Asaas
                // Pode ser processado manualmente depois
            }

            // Update order with Asaas data
            $order->update([
                'asaas_payment_id' => $charge['id'],
                'asaas_payment_url' => $charge['invoiceUrl'] ?? null,
            ]);

            // Clear cart
            session()->forget('cart');

            return redirect()->route('site.checkout.success')
                ->with('order', $order);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    /**
     * Show checkout success page.
     */
    public function checkoutSuccess()
    {
        $order = session()->get('order');
        
        if (!$order) {
            return redirect()->route('site.store');
        }

        return view('site.checkout-success', compact('order'));
    }

    /**
     * Show contact page.
     */
    public function contact()
    {
        $settings = SiteSetting::getPublicSettings()->pluck('value', 'key');
        
        return view('site.contact', compact('settings'));
    }

    /**
     * Handle contact form submission.
     */
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // TODO: Implement contact form logic
        // This would typically involve sending an email to the club

        return redirect()->back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }

    /**
     * Show site editor (admin only).
     */
    public function editor()
    {
        $settings = SiteSetting::all();
        
        return view('site.editor', compact('settings'));
    }

    /**
     * Update site settings (admin only).
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                // Determine setting type based on key
                $type = 'text';
                $isPublic = true;
                
                // Image settings
                if (str_contains($key, 'image') || str_contains($key, 'logo') || str_contains($key, 'banner')) {
                    $type = 'image';
                }
                
                // Color settings
                if (str_contains($key, 'color')) {
                    $type = 'color';
                }
                
                // Boolean settings
                if (str_contains($key, 'enable') || str_contains($key, 'show') || str_contains($key, 'active')) {
                    $type = 'boolean';
                }
                
                // Number settings
                if (str_contains($key, 'limit') || str_contains($key, 'count') || str_contains($key, 'max')) {
                    $type = 'number';
                }
                
                // Handle file uploads
                if ($request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    $path = $file->store('site', 'public');
                    $value = $path;
                }
                
                SiteSetting::set($key, $value, $type, null, $isPublic);
            }

            // Clear cache if needed
            cache()->forget('site_settings');

            return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao atualizar configurações: ' . $e->getMessage()]);
        }
    }
}

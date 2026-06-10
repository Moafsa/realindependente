<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Order;
use App\Models\Post;
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
        try {
            // [NOVO] Carregar settings manualmente se não vierem do view composer (garantir try/catch)
            try {
                $settings = SiteSetting::getPublicSettings()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                Log::warning('SiteController: Erro ao carregar settings na home: ' . $e->getMessage());
                $settings = [];
            }

            // Contagem de atletas ativos
            $athletesCount = Athlete::where('is_active', true)->count();
            
            // Contagem de categorias únicas
            $categoriesCount = Team::where('is_active', true)->distinct('category')->count('category');
            
            // Lista de categorias com soma de atletas
            $teams = Team::withCount('athletes')
                ->where('is_active', true)
                ->get()
                ->groupBy('category')
                ->map(function($categoryTeams, $category) {
                    $firstTeam = $categoryTeams->first();
                    return (object)[
                        'id' => $firstTeam->id, // Mantemos o ID do primeiro time para o link por enquanto
                        'name' => $category, // O nome principal agora é a Categoria
                        'category' => $category,
                        'athletes_count' => $categoryTeams->sum('athletes_count'),
                        'level' => $firstTeam->level,
                        'description' => $firstTeam->description,
                    ];
                })->values();
                
            // Estatísticas para a home
            $stats = [
                'athletes' => $athletesCount,
                'teams' => $categoriesCount, // Agora mostra o número real de categorias
                'history_years' => SiteSetting::get('history_years', 10),
                'titles' => SiteSetting::get('titles_count', 0),
            ];

            // Últimos posts do blog
            $latestPosts = Post::published()->limit(3)->get();

            return view('site.home', compact('teams', 'stats', 'latestPosts', 'settings'));
        } catch (\Throwable $e) {
            // Fallback em caso de erro (ex: banco não migrado)
            Log::error('SiteController@home: ' . $e->getMessage(), ['exception' => $e]);
            return view('site.home', [
                'teams' => collect([]),
                'stats' => [
                    'athletes' => 0,
                    'teams' => 0,
                    'history_years' => 0,
                    'titles' => 0,
                ],
                'settings' => [],
                'latestPosts' => collect([]),
            ]);
        }
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
            $teams = Team::with('coach')->withCount('athletes')
                ->where('is_active', true)
                ->get()
                ->groupBy('category')
                ->map(function($categoryTeams, $category) {
                    $firstTeam = $categoryTeams->first();
                    return (object)[
                        'id' => $firstTeam->id,
                        'name' => $category,
                        'category' => $category,
                        'athletes_count' => $categoryTeams->sum('athletes_count'),
                        'level' => $firstTeam->level,
                        'description' => $firstTeam->description,
                        'logo' => $firstTeam->logo,
                        'color_primary' => $firstTeam->color_primary,
                        'coach' => $firstTeam->coach,
                    ];
                })->values();
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
     * Show coaches page.
     */
    public function coaches()
    {
        $coaches = \App\Models\User::where('role', 'coach')
            ->where('is_active', true)
            ->with('teams')
            ->get();
        
        return view('site.coaches', compact('coaches'));
    }

    /**
     * Show single coach page.
     */
    public function coach($id)
    {
        $coach = \App\Models\User::where('role', 'coach')
            ->with(['teams.athletes'])
            ->findOrFail($id);
            
        return view('site.coach_details', compact('coach'));
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
            
            $performanceRecords = $athlete->performanceRecords()
                ->orderBy('recorded_at', 'asc')
                ->get();
                
            // Group for chart
            $chartData = $performanceRecords->groupBy('metric')->map(function($records) {
                return $records->map(function($r) {
                    return [
                        'x' => $r->recorded_at->format('Y-m-d'),
                        'y' => (float)$r->value
                    ];
                });
            });

        } catch (\Exception $e) {
            abort(404);
        }
        
        return view('site.athlete', compact('athlete', 'chartData'));
    }

    /**
     * Show store page.
     */
    public function store(Request $request)
    {
        try {
            $query = Product::where('is_active', true)
                ->where('type', '!=', 'subscription');

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
     * Show plans page.
     */
    public function plans()
    {
        $settings = \App\Models\SiteSetting::getPublicSettings();
        if (($settings->firstWhere('key', 'enable_plans_page')->value ?? '1') == '0') {
            abort(404);
        }

        try {
            $plans = Product::where('is_active', true)
                ->where('type', 'subscription')
                ->orderBy('price')
                ->get();
        } catch (\Exception $e) {
            $plans = collect([]);
        }

        return view('site.plans', compact('plans'));
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
     * Handle direct plan subscription.
     */
    public function subscribe(Product $product)
    {
        if ($product->type !== 'subscription') {
            return redirect()->route('site.store')->with('error', 'Este produto não é um plano de assinatura.');
        }

        if (!$product->is_active) {
            return redirect()->route('site.store')->with('error', 'Este plano não está disponível no momento.');
        }

        // Clear cart first for subscriptions
        $this->cartService->clear();
        $added = $this->cartService->add($product, 1);

        if (!$added) {
            return redirect()->route('site.store')->with('error', 'Não foi possível adicionar o plano ao carrinho. Verifique se o produto está disponível.');
        }

        if (!Auth::check()) {
            return redirect()->route('site.register', ['plan_id' => $product->id]);
        }

        return redirect()->route('site.checkout');
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
            'customer_document' => 'required|string|max:20',
            'billing_address' => 'required|string|max:500',
        ]);

        $items = $this->cartService->getItemsWithProducts();
        
        if (empty($items)) {
            return redirect()->route('site.store')->with('error', 'Carrinho vazio.');
        }

        $total = $this->cartService->getTotal();

        try {
            $athleteId = null;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->athlete) {
                    $athleteId = $user->athlete->id;
                } elseif ($user->role === 'guardian') {
                    $firstAthlete = \App\Models\Athlete::where('guardian_email', $user->email)->first();
                    if ($firstAthlete) {
                        $athleteId = $firstAthlete->id;
                    }
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'athlete_id' => $athleteId,
                'total_amount' => $total,
                'status' => 'pending',
                'billing_address' => [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'document' => $request->customer_document,
                    'address' => $request->billing_address,
                ],
            ]);

            // Create order items
            foreach ($items as $item) {
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
                    'cpf_cnpj' => $request->customer_document,
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

            // Busca as taxas administrativas do plano do tenant
            $adminFee = 0;
            $ecommerceTax = 0;
            if (tenancy()->initialized) {
                $plan = \App\Models\Plan::find(tenant('plan_id'));
                if ($plan) {
                    $adminFee = $plan->admin_fee_percentage;
                    $ecommerceTax = $plan->ecommerce_tax_rate;
                }
            }

            // Verifica se há alguma assinatura no carrinho
            $subscriptionPlan = null;
            foreach ($items as $item) {
                if ($item['product']->type === 'subscription') {
                    $subscriptionPlan = $item['product'];
                    break; // Assumimos que só há uma assinatura por checkout por enquanto
                }
            }

            try {
                if ($subscriptionPlan) {
                    // Cria assinatura no Asaas
                    $cycle = $subscriptionPlan->attributes['cycle'] ?? 'MONTHLY';
                    $setupFee = floatval($subscriptionPlan->attributes['setup_fee'] ?? 0);
                    $basePrice = $subscriptionPlan->price;

                    // Calcular próxima data da assinatura recorrente
                    $firstDueDate = now()->addDays(7);
                    
                    if ($setupFee > 0) {
                        $firstInvoiceTotal = $basePrice + $setupFee;
                        $nextDueDate = $firstDueDate->copy();
                        switch ($cycle) {
                            case 'MONTHLY': $nextDueDate->addMonth(); break;
                            case 'QUARTERLY': $nextDueDate->addMonths(3); break;
                            case 'SEMIANNUALLY': $nextDueDate->addMonths(6); break;
                            case 'YEARLY': $nextDueDate->addYear(); break;
                        }

                        // 1. Cria cobrança da primeira fatura (Mensalidade + Setup Fee)
                        $chargeData = [
                            'customer_id' => $customerId,
                            'value' => $firstInvoiceTotal,
                            'due_date' => $firstDueDate->format('Y-m-d'),
                            'description' => "Primeira Mensalidade + Inscrição: {$subscriptionPlan->name} - " . (tenant('name') ?? ''),
                            'external_reference' => "order_{$order->id}_first",
                            'billing_type' => $request->payment_method === 'asaas' ? 'PIX' : $request->payment_method,
                            'split_percentage' => $adminFee,
                        ];
                        $firstCharge = $this->asaasService->createCharge($chargeData);

                        $paymentId = $firstCharge['id'];
                        $paymentUrl = $firstCharge['invoiceUrl'] ?? null;

                        // 2. Cria a assinatura iniciando no próximo ciclo
                        $subscriptionData = [
                            'customer_id' => $customerId,
                            'value' => $basePrice,
                            'next_due_date' => $nextDueDate->format('Y-m-d'),
                            'description' => "Assinatura: {$subscriptionPlan->name} - " . (tenant('name') ?? ''),
                            'external_reference' => "order_{$order->id}_sub",
                            'billing_type' => $request->payment_method === 'asaas' ? 'PIX' : $request->payment_method,
                            'cycle' => $cycle,
                            'split_percentage' => $adminFee,
                        ];
                        $response = $this->asaasService->createSubscription($subscriptionData);
                        $subscriptionId = $response['id'];
                    } else {
                        // Sem taxa de inscrição, cria assinatura pra agora
                        $subscriptionData = [
                            'customer_id' => $customerId,
                            'value' => $basePrice,
                            'next_due_date' => $firstDueDate->format('Y-m-d'),
                            'description' => "Assinatura: {$subscriptionPlan->name} - " . (tenant('name') ?? ''),
                            'external_reference' => "order_{$order->id}",
                            'billing_type' => $request->payment_method === 'asaas' ? 'PIX' : $request->payment_method,
                            'cycle' => $cycle,
                            'split_percentage' => $adminFee,
                        ];
                        $response = $this->asaasService->createSubscription($subscriptionData);
                        $subscriptionId = $response['id'];
                        $paymentId = $response['id'];

                        $paymentUrl = null;
                        try {
                            $payments = $this->asaasService->getSubscriptionPayments($subscriptionId);
                            if (!empty($payments['data'])) {
                                $paymentUrl = $payments['data'][0]['invoiceUrl'] ?? null;
                            }
                        } catch (\Exception $e) {
                            Log::warning("Erro ao buscar faturas da assinatura: " . $e->getMessage());
                        }
                    }

                    // Atualiza order com dados do Asaas
                    $order->update([
                        'asaas_payment_id' => $paymentId,
                        'asaas_payment_url' => $paymentUrl,
                    ]);
                
                // Associa a assinatura ao atleta logado ou do responsável
                $athlete = null;
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user->athlete) {
                        $athlete = $user->athlete;
                    } elseif ($user->role === 'guardian') {
                        $athlete = \App\Models\Athlete::where('guardian_email', $user->email)->first();
                    }
                }

                if ($athlete) {

                    // Se já existe uma assinatura, cancela a anterior (Upgrade/Downgrade)
                    if ($athlete->asaas_subscription_id) {
                        try {
                            $this->asaasService->cancelSubscription($athlete->asaas_subscription_id);
                        } catch (\Exception $e) {
                            Log::warning("Erro ao cancelar assinatura anterior ({$athlete->asaas_subscription_id}): " . $e->getMessage());
                        }
                    }

                    $athlete->update([
                        'asaas_subscription_id' => $response['id'],
                        'subscription_plan_id' => $subscriptionPlan->id,
                    ]);
                }
                } else {
                    // Cria cobrança avulsa no Asaas
                    $chargeData = [
                        'customer_id' => $customerId,
                        'value' => $total,
                        'due_date' => now()->addDays(7)->format('Y-m-d'),
                        'description' => "Pedido #{$order->id} - Lojinha " . (tenant('name') ?? ''),
                        'external_reference' => "order_{$order->id}",
                        'billing_type' => $request->payment_method === 'asaas' ? 'PIX' : $request->payment_method,
                        'split_percentage' => $ecommerceTax,
                    ];

                    $charge = $this->asaasService->createCharge($chargeData);
                    
                    // Atualiza order com dados do Asaas
                    $order->update([
                        'asaas_payment_id' => $charge['id'],
                        'asaas_payment_url' => $charge['invoiceUrl'] ?? null,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('SiteController: Erro ao criar transação no Asaas', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                
                return redirect()->back()->withInput()->with('error', 'Erro ao gerar cobrança no Asaas: ' . $e->getMessage());
            }

            // Clear cart
            $this->cartService->clear();

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
        $settings = SiteSetting::getPublicSettings()->pluck('value', 'key')->toArray();
        $mapboxToken = SiteSetting::getCentral('mapbox_public_token');
        
        return view('site.contact', compact('settings', 'mapboxToken'));
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
     * Display blog posts.
     */
    public function blog()
    {
        $posts = Post::published()->orderBy('published_at', 'desc')->paginate(9);
        return view('site.blog.index', compact('posts'));
    }

    /**
     * Display single blog post.
     */
    public function post($slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        
        // Relacionados (recentes)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->limit(3)
            ->get();

        return view('site.blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Show site editor (admin only).
     */
    public function editor()
    {
        $settings = collect(SiteSetting::all());
        $tenant = tenant();
        $customDomain = $tenant->domains()->where('is_primary', false)->first();
        
        return view('site.editor', compact('settings', 'customDomain', 'tenant'));
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
            Log::info('SiteController@update: Recebendo configurações', ['settings' => array_keys($request->settings)]);
            
            // Handle Domain Update
            if (isset($request->settings['custom_domain'])) {
                $domainName = strtolower(trim($request->settings['custom_domain']));
                $tenant = tenant();
                
                if (!empty($domainName)) {
                    // Basic validation
                    if (!preg_match('/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}$/', $domainName)) {
                        return redirect()->back()->withErrors(['error' => 'Formato de domínio inválido.']);
                    }

                    // Check if already exists for ANOTHER tenant
                    $exists = \App\Models\Domain::where('domain', $domainName)
                        ->where('tenant_id', '!=', $tenant->id)
                        ->exists();
                    
                    if ($exists) {
                        return redirect()->back()->withErrors(['error' => 'Este domínio já está sendo usado por outro clube.']);
                    }

                    // Update or create
                    $tenant->domains()->updateOrCreate(
                        ['is_primary' => false],
                        ['domain' => $domainName, 'is_verified' => false]
                    );
                } else {
                    // If empty, remove custom domain
                    $tenant->domains()->where('is_primary', false)->delete();
                }
            }

            $settingKeys = array_unique(array_merge(
                array_keys($request->settings ?? []),
                array_keys($request->file('settings') ?? [])
            ));

            foreach ($settingKeys as $key) {
                if ($key === 'custom_domain') continue; // Handled above
                
                $value = $request->input("settings.{$key}");
                
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
                    $path = $file->storeOptimized('site');
                    $value = $path;
                    Log::info("SiteController@update: Arquivo salvo: {$key} -> {$path}");
                } elseif ($type === 'image' && empty($value)) {
                    // Skip updating image if no new file and value is empty (to avoid clearing existing)
                    continue;
                }
                
                SiteSetting::set($key, $value, $type, null, $isPublic);
                
                // If we are updating the site name, also update the tenant's name in the central database
                if ($key === 'site_name' && !empty($value)) {
                    $tenantId = tenant('id');
                    if ($tenantId) {
                        \App\Models\Tenant::where('id', $tenantId)->update(['name' => $value]);
                    }
                }
                
                Log::info("SiteController@update: Configuração salva: {$key} = " . (is_string($value) ? $value : 'OBJECT') . " (tipo: {$type})");
            }

            // Clear cache
            SiteSetting::clearPublicSettingsCache();
            Log::info('SiteController@update: Cache limpo com sucesso');

            return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            Log::error('SiteController@update: Erro ao salvar', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao atualizar configurações: ' . $e->getMessage()]);
        }
    }
}

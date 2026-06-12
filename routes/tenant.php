<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Default route
    Route::get('/', [SiteController::class, 'home'])->name('site.home');

    // Site Routes
    Route::get('/force-fix', function () {
        \App\Models\SiteSetting::whereIn('key', ['site_name', 'contact_email', 'contact_phone', 'contact_whatsapp', 'contact_address', 'instagram_url', 'facebook_url', 'youtube_url'])->update(['is_public' => true]);
        $cacheKey = 'site_settings_public_' . (tenant('id') ?? 'central');
        \Illuminate\Support\Facades\Cache::forget($cacheKey);
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        return 'Fixed';
    });
    Route::get('/sobre', [SiteController::class, 'about'])->name('site.about');
    Route::get('/equipes', [SiteController::class, 'teams'])->name('site.teams');
    Route::get('/equipe/{id}', [SiteController::class, 'team'])->name('site.team');
    Route::get('/atletas', [SiteController::class, 'athletes'])->name('site.athletes');
    Route::get('/atleta/{id}', [SiteController::class, 'athlete'])->name('site.athlete');
    Route::get('/treinadores', [SiteController::class, 'coaches'])->name('site.coaches');
    Route::get('/treinador/{id}', [SiteController::class, 'coach'])->name('site.coach');
    Route::get('/loja', [SiteController::class, 'store'])->name('site.store');
    Route::get('/planos', [SiteController::class, 'plans'])->name('site.plans');
    Route::get('/planos/{product}/assinar', [SiteController::class, 'subscribe'])->name('site.subscribe');
    Route::get('/produto/{product}', [SiteController::class, 'product'])->name('site.product');
    Route::get('/contato', [SiteController::class, 'contact'])->name('site.contact');
    Route::post('/contato', [SiteController::class, 'contactSubmit'])->name('site.contact.submit');
    Route::post('/webhooks/whatsapp', [\App\Http\Controllers\WebhookController::class, 'handleWhatsApp'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    // Blog Routes
    Route::get('/blog', [SiteController::class, 'blog'])->name('site.blog');
    Route::get('/blog/{slug}', [SiteController::class, 'post'])->name('site.blog.show');

    // Cart & Checkout
    Route::post('/carrinho/adicionar/{product}', [SiteController::class, 'addToCart'])->name('site.add-to-cart');
    Route::post('/carrinho/remover/{product}', [SiteController::class, 'removeFromCart'])->name('site.remove-from-cart');
    Route::post('/carrinho/atualizar/{product}', [SiteController::class, 'updateCart'])->name('site.update-cart');
    Route::get('/carrinho', [SiteController::class, 'cart'])->name('site.cart');
    Route::get('/checkout', [SiteController::class, 'checkout'])->name('site.checkout');
    Route::post('/checkout', [SiteController::class, 'processCheckout'])->name('site.process-checkout');
    Route::get('/checkout/sucesso', [SiteController::class, 'checkoutSuccess'])->name('site.checkout.success');

    // Authentication Routes (Tenant Context)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/registrar', [\App\Http\Controllers\Auth\TenantRegisterController::class, 'showRegistrationForm'])->name('site.register');
    Route::post('/registrar', [\App\Http\Controllers\Auth\TenantRegisterController::class, 'register'])->name('site.register.submit');

    // Dashboard Routes (Admin & Coach)
    Route::middleware(['auth', 'role:admin|coach'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/metrics', [DashboardController::class, 'getMetrics'])->name('dashboard.metrics');
        Route::get('/dashboard/recent-payments', [DashboardController::class, 'getRecentPayments'])->name('dashboard.recent-payments');
        Route::get('/dashboard/athlete-evolution', [DashboardController::class, 'getAthleteEvolution'])->name('dashboard.athlete-evolution');
    
        // Athletes Management
        Route::resource('athletes', AthleteController::class)->names([
            'index' => 'admin.athletes.index',
            'create' => 'admin.athletes.create',
            'store' => 'admin.athletes.store',
            'show' => 'admin.athletes.show',
            'edit' => 'admin.athletes.edit',
            'update' => 'admin.athletes.update',
            'destroy' => 'admin.athletes.destroy',
        ])->where(['athlete' => '[0-9]+']);
        Route::post('/athletes/{athlete}/toggle-status', [AthleteController::class, 'toggleStatus'])->name('admin.athletes.toggle-status');
        Route::post('/athletes/{athlete}/documents', [AthleteController::class, 'updateDocuments'])->name('admin.athletes.documents.update');
        Route::get('/athletes/{athlete}/performance-data', [AthleteController::class, 'getPerformanceData'])->name('admin.athletes.performance-data');
        Route::get('/athletes/{athlete}/financial-history', [AthleteController::class, 'getFinancialHistory'])->name('admin.athletes.financial-history');
        Route::get('/athletes/{athlete}/ai-plans', [AthleteController::class, 'getAiPlans'])->name('admin.athletes.ai-plans');
        Route::post('/athletes/{athlete}/ai-plans/generate', [AthleteController::class, 'generateAiPlan'])->name('admin.athletes.ai-plans.generate');
        Route::get('/athletes/{athlete}/ai-plans/{plan}', [AthleteController::class, 'showAiPlan'])->name('admin.athletes.ai-plans.show');
        Route::delete('/athletes/{athlete}/ai-plans/{plan}', [AthleteController::class, 'deletePlan'])->name('athletes.ai-plans.delete');
        Route::post('/athletes/{athlete}/ai-plans/{plan}/update', [AthleteController::class, 'updateAiPlan'])->name('athletes.ai-plans.update');
        Route::post('/athletes/{athlete}/ai-plans/{plan}/toggle-suspend', [AthleteController::class, 'toggleSuspendPlan'])->name('athletes.ai-plans.toggle-suspend');
        Route::post('/athletes/{athlete}/evaluate', [AthleteController::class, 'evaluate'])->name('admin.athletes.evaluate');
        
        // Teams Management
        Route::resource('teams', TeamController::class)->names([
            'index' => 'admin.teams.index',
            'create' => 'admin.teams.create',
            'store' => 'admin.teams.store',
            'show' => 'admin.teams.show',
            'edit' => 'admin.teams.edit',
            'update' => 'admin.teams.update',
            'destroy' => 'admin.teams.destroy',
        ])->where(['team' => '[0-9]+']);
        Route::post('/teams/{team}/ai-plans/generate', [TeamController::class, 'generateTeamAiPlan'])->name('admin.teams.ai-plans.generate');
        Route::post('/teams/{team}/toggle-status', [TeamController::class, 'toggleStatus'])->name('admin.teams.toggle-status');

        // Trainings & Tournaments
        Route::resource('trainings', \App\Http\Controllers\TrainingController::class);
        Route::resource('tournaments', \App\Http\Controllers\TournamentController::class);
        Route::post('/tournaments/{tournament}/matches/generate', [\App\Http\Controllers\TournamentMatchController::class, 'generate'])->name('tournaments.matches.generate');
        Route::post('/matches/{match}/update-score', [\App\Http\Controllers\TournamentMatchController::class, 'updateScore'])->name('matches.update-score');

        // Extract for Coach
        Route::get('/coaches/extract', [CoachController::class, 'extract'])->name('admin.coaches.extract');
        Route::get('/coach/profile', [CoachController::class, 'profile'])->name('admin.coach.profile');
        Route::post('/coach/profile', [CoachController::class, 'updateProfile'])->name('admin.coach.profile.update');

        // Galleries
        Route::post('/galleries', [\App\Http\Controllers\Tenant\GalleryItemController::class, 'store'])->name('admin.galleries.store');
        Route::delete('/galleries/{galleryItem}', [\App\Http\Controllers\Tenant\GalleryItemController::class, 'destroy'])->name('admin.galleries.destroy');
    });

    // Admin ONLY Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        // Coaches Management (Admin can manage coaches)
        Route::resource('coaches', CoachController::class)->names([
            'index' => 'admin.coaches.index',
            'create' => 'admin.coaches.create',
            'store' => 'admin.coaches.store',
            'show' => 'admin.coaches.show',
            'edit' => 'admin.coaches.edit',
            'update' => 'admin.coaches.update',
            'destroy' => 'admin.coaches.destroy',
        ])->where(['coach' => '[0-9]+']);
        Route::post('/coaches/{coach}/toggle-status', [CoachController::class, 'toggleStatus'])->name('admin.coaches.toggle-status');
        Route::post('/coaches/{coach}/pay', [CoachController::class, 'payCoach'])->name('admin.coaches.pay');
        Route::post('/coaches/{coach}/transaction', [CoachController::class, 'addTransaction'])->name('admin.coaches.add-transaction');
        
        // Branches Management
        Route::resource('branches', BranchController::class)->where(['branch' => '[0-9]+']);
        Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
        
        // Financial Management
        Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
        Route::get('/financial/charges', [FinancialController::class, 'charges'])->name('financial.charges');
        Route::get('/financial/charges/{order}', [FinancialController::class, 'showCharge'])->name('financial.charge-details');
        Route::post('/financial/generate-charges', [FinancialController::class, 'generateCharges'])->name('financial.generate-charges');
        Route::post('/financial/charges/{order}/cancel', [FinancialController::class, 'cancelCharge'])->name('financial.cancel-charge');
        Route::get('/financial/summary', [FinancialController::class, 'summary'])->name('financial.summary');
        
        // Cash Flow Management
        Route::resource('cash-flow', \App\Http\Controllers\CashFlowController::class)->names([
            'index' => 'admin.cash-flow.index',
            'store' => 'admin.cash-flow.store',
            'update' => 'admin.cash-flow.update',
            'destroy' => 'admin.cash-flow.destroy',
        ]);

        // Tenant Billing
        Route::get('/billing', [\App\Http\Controllers\TenantBillingController::class, 'index'])->name('admin.billing.index');
        Route::get('/billing/pay', [\App\Http\Controllers\TenantBillingController::class, 'pay'])->name('admin.billing.pay');
        
        // AI Integration & Reports
        Route::get('/ai', [AIController::class, 'getUsageStats'])->name('ai.stats');
        Route::get('/ai/reports', [\App\Http\Controllers\AIReportController::class, 'index'])->name('ai.reports.index');
        
        // Blog Posts Management
        Route::prefix('posts')->name('admin.posts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\PostController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\PostController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\PostController::class, 'store'])->name('store');
            Route::post('/ai-generate', [\App\Http\Controllers\PostController::class, 'aiGenerate'])->name('ai-generate');
            Route::get('/{post}/edit', [\App\Http\Controllers\PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [\App\Http\Controllers\PostController::class, 'update'])->name('update');
            Route::post('/{post}/approve', [\App\Http\Controllers\PostController::class, 'approve'])->name('approve');
            Route::delete('/{post}', [\App\Http\Controllers\PostController::class, 'destroy'])->name('destroy');
            Route::post('/settings', [\App\Http\Controllers\PostController::class, 'updateSettings'])->name('settings.update');
        });
        
        // Products & Orders
        Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
        Route::resource('products', ProductController::class)->where(['product' => '[0-9]+']);

        // Subscription Plans
        Route::resource('subscription-plans', \App\Http\Controllers\SubscriptionPlanController::class)
            ->names('admin.subscription-plans')
            ->parameters(['subscription-plans' => 'subscription_plan']);
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show')->where(['order' => '[0-9]+']);
        
        // Site Editor
        Route::get('/site/editor', [SiteController::class, 'editor'])->name('site.editor');
        Route::post('/site/update', [SiteController::class, 'update'])->name('site.update');

        // WhatsApp Session Management
        Route::prefix('whatsapp')->name('admin.whatsapp.')->group(function () {
            Route::get('/', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('index');
            Route::get('/qr', [\App\Http\Controllers\WhatsAppController::class, 'qrCode'])->name('qr');
            Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('status');
            Route::post('/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnect'])->name('disconnect');
            Route::post('/settings', [\App\Http\Controllers\WhatsAppController::class, 'saveSettings'])->name('settings.save');
        });
    });

    // Communication (Shared)
    Route::middleware(['auth'])->prefix('communication')->name('communication.')->group(function () {
        Route::get('/', [CommunicationController::class, 'index'])->name('index');
        Route::get('/messages', [CommunicationController::class, 'getMessages'])->name('messages');
        Route::post('/store', [CommunicationController::class, 'store'])->name('store');
        Route::post('/read/{message}', [CommunicationController::class, 'markAsRead'])->name('read');
        Route::post('/mural', [CommunicationController::class, 'muralStore'])->name('mural.store');
        Route::post('/mural/read', [CommunicationController::class, 'muralRead'])->name('notifications.mural-read');
        Route::post('/team-plan', [CommunicationController::class, 'storeTeamPlan'])->name('team-plan.store');
        Route::get('/notifications/counts', [CommunicationController::class, 'getNotificationCounts'])->name('notifications.counts');
    });

    // Portal Routes (Athletes)
    Route::middleware(['auth', 'role:athlete|guardian|admin|coach'])->prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
        Route::put('/profile', [PortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/performance', [PortalController::class, 'performance'])->name('performance');
        Route::get('/performance-data', [PortalController::class, 'getPerformanceData'])->name('performance-data');
        Route::get('/ai-plans', [PortalController::class, 'aiPlans'])->name('ai-plans');
        Route::get('/ai-plans/{plan}', [PortalController::class, 'showAiPlan'])->name('ai-plans.show');
        Route::get('/ai-plans/{content}/json', [PortalController::class, 'getContent'])->name('ai-plans.json');
        Route::post('/ai-plans/request', [PortalController::class, 'requestPlan'])->name('ai-plans.request');
        Route::post('/ai-plans/{plan}/accept', [PortalController::class, 'acceptAiPlan'])->name('ai-plans.accept');
        Route::post('/ai-plans/{content}/favorite', [PortalController::class, 'toggleFavorite'])->name('ai-plans.favorite');
        Route::post('/ai-plans/log-meal', [PortalController::class, 'logMealPhoto'])->name('ai-plans.log-meal');
        Route::get('/subscriptions', [PortalController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/invoices', [PortalController::class, 'invoices'])->name('invoices');
        Route::get('/notifications', [PortalController::class, 'getNotifications'])->name('notifications');
        Route::post('/notifications/mural-read', [PortalController::class, 'markMuralViewed'])->name('notifications.mural-read');
        Route::get('/trainings', [PortalController::class, 'trainings'])->name('trainings');
    });

    // Tenant Storage Serving Route
    Route::get('/tenant-assets/{path}', function ($path) {
        $path = str_replace('..', '', $path);
        
        \Illuminate\Support\Facades\Log::info("Attempting to serve asset: " . $path, [
            'tenant' => tenant('id'),
            'public_disk_root' => \Illuminate\Support\Facades\Storage::disk('public')->path(''),
        ]);

        // 1. Tenta no disco 'public' especificamente (onde salvamos avatares)
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Log::info("Asset found in public disk: " . $path);
            return \Illuminate\Support\Facades\Storage::disk('public')->response($path);
        }
        
        // 2. Tenta no disco padrão (geralmente 'tenant' em stancl/tenancy)
        if (\Illuminate\Support\Facades\Storage::exists($path)) {
            \Illuminate\Support\Facades\Log::info("Asset found in default disk: " . $path);
            return \Illuminate\Support\Facades\Storage::response($path);
        }
        
        // 3. Tenta com/sem prefixo 'public/' no disco public
        $altPath = str_starts_with($path, 'public/') ? substr($path, 7) : 'public/' . $path;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($altPath)) {
            \Illuminate\Support\Facades\Log::info("Asset found in public disk (alt): " . $altPath);
            return \Illuminate\Support\Facades\Storage::disk('public')->response($altPath);
        }

        // 4. Tenta com/sem prefixo 'public/' no disco padrão
        if (\Illuminate\Support\Facades\Storage::exists($altPath)) {
            \Illuminate\Support\Facades\Log::info("Asset found in default disk (alt): " . $altPath);
            return \Illuminate\Support\Facades\Storage::response($altPath);
        }
        
        \Illuminate\Support\Facades\Log::warning("Asset not found in any disk: " . $path, [
            'tried_path' => $path,
            'tried_alt' => $altPath,
            'public_root' => \Illuminate\Support\Facades\Storage::disk('public')->path(''),
        ]);
        abort(404);
    })->where('path', '.*')->name('tenant.assets');
});

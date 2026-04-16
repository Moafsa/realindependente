<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\TenantManagementController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\TenantRegistrationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Marketing Routes (Central Application)
Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.home');
Route::get('/marketing/features', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('/marketing/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/marketing/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/marketing/contact', [MarketingController::class, 'contactSubmit'])->name('marketing.contact.submit');

// Public Site Routes
Route::get('/', [SiteController::class, 'home'])->name('site.home');
Route::get('/about', [SiteController::class, 'about'])->name('site.about');
Route::get('/store', [SiteController::class, 'store'])->name('site.store');
Route::get('/store/{product}', [SiteController::class, 'product'])->name('site.product');
Route::post('/store/{product}/add-to-cart', [SiteController::class, 'addToCart'])->name('site.add-to-cart');
Route::post('/cart/{product}/remove', [SiteController::class, 'removeFromCart'])->name('site.remove-from-cart');
Route::post('/cart/{product}/update', [SiteController::class, 'updateCart'])->name('site.update-cart');
Route::get('/cart', [SiteController::class, 'cart'])->name('site.cart');
Route::get('/checkout', [SiteController::class, 'checkout'])->name('site.checkout');
Route::post('/checkout', [SiteController::class, 'processCheckout'])->name('site.process-checkout');
Route::get('/checkout/success', [SiteController::class, 'checkoutSuccess'])->name('site.checkout.success');
Route::get('/contact', [SiteController::class, 'contact'])->name('site.contact');
Route::post('/contact', [SiteController::class, 'contactSubmit'])->name('site.contact.submit');

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

    // Dashboard Routes (Admin)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/metrics', [DashboardController::class, 'getMetrics'])->name('dashboard.metrics');
        Route::get('/dashboard/recent-payments', [DashboardController::class, 'getRecentPayments'])->name('dashboard.recent-payments');
        Route::get('/dashboard/athlete-evolution', [DashboardController::class, 'getAthleteEvolution'])->name('dashboard.athlete-evolution');
    
    // Athletes Management
    Route::get('/athletes', [AthleteController::class, 'index'])->name('admin.athletes.index');
    Route::resource('athletes', AthleteController::class)->except(['index'])->names([
        'create' => 'admin.athletes.create',
        'store' => 'admin.athletes.store',
        'show' => 'admin.athletes.show',
        'edit' => 'admin.athletes.edit',
        'update' => 'admin.athletes.update',
        'destroy' => 'admin.athletes.destroy',
    ]);
    Route::post('/athletes/{athlete}/toggle-status', [AthleteController::class, 'toggleStatus'])->name('athletes.toggle-status');
    Route::get('/athletes/{athlete}/performance-data', [AthleteController::class, 'getPerformanceData'])->name('athletes.performance-data');
    Route::get('/athletes/{athlete}/financial-history', [AthleteController::class, 'getFinancialHistory'])->name('athletes.financial-history');
    Route::get('/athletes/{athlete}/ai-plans', [AthleteController::class, 'getAiPlans'])->name('athletes.ai-plans');
    
    // Teams Management
    Route::get('/teams', [TeamController::class, 'index'])->name('admin.teams.index');
    Route::resource('teams', TeamController::class)->except(['index'])->names([
        'create' => 'admin.teams.create',
        'store' => 'admin.teams.store',
        'show' => 'admin.teams.show',
        'edit' => 'admin.teams.edit',
        'update' => 'admin.teams.update',
        'destroy' => 'admin.teams.destroy',
    ]);
    Route::post('/teams/{team}/toggle-status', [TeamController::class, 'toggleStatus'])->name('admin.teams.toggle-status');
    
    // Branches Management
    Route::resource('branches', BranchController::class);
    Route::post('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
    
    // Financial Management
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    Route::get('/financial/charges', [FinancialController::class, 'charges'])->name('financial.charges');
    Route::get('/financial/charges/{order}', [FinancialController::class, 'showCharge'])->name('financial.charge-details');
    // Redirect GET requests to financial page, POST goes to generateCharges method
    Route::get('/financial/generate-charges', function () {
        return redirect()->route('financial.index');
    });
    Route::post('/financial/generate-charges', [FinancialController::class, 'generateCharges'])->name('financial.generate-charges');
    Route::post('/financial/charges/{order}/cancel', [FinancialController::class, 'cancelCharge'])->name('financial.cancel-charge');
    Route::get('/financial/summary', [FinancialController::class, 'summary'])->name('financial.summary');
    
    // AI Integration
    Route::get('/ai', [AIController::class, 'getUsageStats'])->name('ai.stats');
    Route::get('/ai/usage-by-tenant', [AIController::class, 'getUsageByTenant'])->name('ai.usage-by-tenant');
    Route::get('/ai/costs', [AIController::class, 'getCosts'])->name('ai.costs');
    
    // AI Reports
    Route::get('/ai/reports', [\App\Http\Controllers\AIReportController::class, 'index'])->name('ai.reports.index');
    Route::get('/ai/reports/export', [\App\Http\Controllers\AIReportController::class, 'export'])->name('ai.reports.export');
    Route::get('/ai/reports/costs', [\App\Http\Controllers\AIReportController::class, 'getCostsReport'])->name('ai.reports.costs');
    
    // Site Editor (Admin only)
    Route::get('/site/editor', [SiteController::class, 'editor'])->name('site.editor');
    Route::post('/site/update', [SiteController::class, 'update'])->name('site.update');
    
    // Products Management
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
    
    // Orders Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
    
    // Trainings Management
    Route::resource('trainings', \App\Http\Controllers\TrainingController::class);
    
    // Super Admin - Tenant Management
    Route::prefix('admin/tenants')->name('admin.tenants.')->group(function () {
        Route::get('/', [TenantManagementController::class, 'index'])->name('index');
        Route::get('/{tenant}', [TenantManagementController::class, 'show'])->name('show');
        Route::put('/{tenant}', [TenantManagementController::class, 'update'])->name('update');
        Route::post('/{tenant}/suspend', [TenantManagementController::class, 'suspend'])->name('suspend');
        Route::post('/{tenant}/activate', [TenantManagementController::class, 'activate'])->name('activate');
        Route::post('/{tenant}/cancel', [TenantManagementController::class, 'cancel'])->name('cancel');
    });
});

// Portal Routes (Athletes)
Route::middleware(['auth', 'role:athlete|guardian'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [PortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/performance', [PortalController::class, 'performance'])->name('performance');
    Route::get('/ai-plans', [PortalController::class, 'aiPlans'])->name('ai-plans');
    Route::get('/communication', [PortalController::class, 'communication'])->name('communication');
    
    // AI Plans
    Route::post('/ai-plans/generate', [PortalController::class, 'generatePlan'])->name('ai-plans.generate');
    Route::get('/ai-plans/content', [PortalController::class, 'getAiContent'])->name('ai-plans.content');
    Route::post('/ai-plans/{content}/favorite', [PortalController::class, 'toggleFavorite'])->name('ai-plans.favorite');
    Route::get('/ai-plans/{content}', [PortalController::class, 'getContent'])->name('ai-plans.show');
    
    // Portal API endpoints
    Route::get('/upcoming-trainings', [PortalController::class, 'getUpcomingTrainings'])->name('upcoming-trainings');
    Route::get('/notifications', [PortalController::class, 'getNotifications'])->name('notifications');
    Route::get('/performance-data', [PortalController::class, 'getPerformanceData'])->name('performance-data');
    
    // Communication
    Route::get('/communication', [\App\Http\Controllers\CommunicationController::class, 'index'])->name('communication');
    Route::post('/communication', [\App\Http\Controllers\CommunicationController::class, 'store'])->name('communication.store');
    Route::post('/communication/{message}/read', [\App\Http\Controllers\CommunicationController::class, 'markAsRead'])->name('communication.read');
});

// AI API Routes
Route::middleware(['auth'])->prefix('api/ai')->name('ai.')->group(function () {
    Route::post('/athletes/{athlete}/workout', [AIController::class, 'generateWorkout'])->name('generate-workout');
    Route::post('/athletes/{athlete}/nutrition', [AIController::class, 'generateNutrition'])->name('generate-nutrition');
    Route::get('/athletes/{athlete}/content', [AIController::class, 'getAthleteContent'])->name('athlete-content');
    Route::post('/content/{content}/favorite', [AIController::class, 'toggleFavorite'])->name('toggle-favorite');
    Route::get('/content/{content}', [AIController::class, 'getContent'])->name('content');
    Route::delete('/content/{content}', [AIController::class, 'deleteContent'])->name('delete-content');
    Route::get('/stats', [AIController::class, 'getUsageStats'])->name('stats');
});

// Webhook Routes
Route::post('/webhooks/asaas', [FinancialController::class, 'webhook'])->name('webhooks.asaas');
Route::post('/webhooks/asaas/tenant', [TenantRegistrationController::class, 'asaasWebhook'])->name('webhooks.asaas.tenant');
Route::post('/webhooks/asaas/order', [FinancialController::class, 'handleOrderPayment'])->name('webhooks.asaas.order');

// Tenant Registration Routes
Route::get('/tenant/register', [TenantRegistrationController::class, 'create'])->name('tenant.register');
Route::post('/tenant/register', [TenantRegistrationController::class, 'store'])->name('tenant.register.store');
Route::get('/tenant/success', [TenantRegistrationController::class, 'success'])->name('tenant.success');
Route::post('/api/tenant/check-subdomain', [TenantRegistrationController::class, 'checkSubdomain'])->name('api.tenant.check-subdomain');

// Public Site Routes - Teams and Athletes (must be after other routes to avoid conflicts)
// Note: /teams route is now admin-only, public teams are accessed via different path if needed
Route::get('/public/teams', [SiteController::class, 'teams'])->name('site.teams');
Route::get('/public/teams/{team:id}', [SiteController::class, 'team'])->name('site.team');
Route::get('/public/athletes', [SiteController::class, 'athletes'])->name('site.athletes');
Route::get('/athletes/{athlete:id}', [SiteController::class, 'athlete'])->name('site.athlete');
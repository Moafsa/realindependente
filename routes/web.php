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
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\TenantManagementController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\AiMonitoringController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Marketing Routes (Central Application)
Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing.home');
Route::get('/marketing/features', [MarketingController::class, 'features'])->name('marketing.features');
Route::get('/marketing/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/marketing/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::post('/marketing/contact', [MarketingController::class, 'contactSubmit'])->name('marketing.contact.submit');

// Public Site Routes
Route::get('/', [MarketingController::class, 'index'])->name('site.home');
Route::get('/about', [SiteController::class, 'about'])->name('site.about');
Route::get('/store', [SiteController::class, 'store'])->name('site.store');
Route::get('/store/{product}', [SiteController::class, 'product'])->name('site.product');
// ... other routes ...
// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showPasswordReset'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'passwordReset'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'passwordResetUpdate'])->name('password.update');

Route::get('/register', [TenantRegistrationController::class, 'create'])->name('register');
Route::post('/register', [TenantRegistrationController::class, 'store']);

// Super Admin - Tenant Management
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantManagementController::class, 'index'])->name('index');
        Route::get('/{tenant}', [TenantManagementController::class, 'show'])->name('show');
        Route::put('/{tenant}', [TenantManagementController::class, 'update'])->name('update');
        Route::post('/{tenant}/impersonate', [TenantManagementController::class, 'impersonate'])->name('impersonate');
        Route::post('/{tenant}/suspend', [TenantManagementController::class, 'suspend'])->name('suspend');
        Route::post('/{tenant}/activate', [TenantManagementController::class, 'activate'])->name('activate');
        Route::post('/{tenant}/cancel', [TenantManagementController::class, 'cancel'])->name('cancel');
        Route::delete('/{tenant}', [TenantManagementController::class, 'destroy'])->name('destroy');
    });

    // Plans Management
    Route::resource('plans', PlanController::class);

    // Legal Settings
    Route::get('/legal', [\App\Http\Controllers\Admin\LegalSettingsController::class, 'index'])->name('legal.index');
    Route::put('/legal', [\App\Http\Controllers\Admin\LegalSettingsController::class, 'update'])->name('legal.update');

    // AI Monitoring
    Route::get('/ai/monitoring', [AiMonitoringController::class, 'index'])->name('ai.monitoring');

    // General Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\GeneralSettingsController::class, 'update'])->name('settings.update');

    // WhatsApp Session Management (Super Admin)
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('index');
        Route::get('/qr', [\App\Http\Controllers\WhatsAppController::class, 'qrCode'])->name('qr');
        Route::get('/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('status');
        Route::post('/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnect'])->name('disconnect');
        Route::post('/settings', [\App\Http\Controllers\WhatsAppController::class, 'saveSettings'])->name('settings.save');
    });

    // Global Financial Management (Super Admin)
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinancialController::class, 'index'])->name('index');
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\FinancialController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/club-sales', [\App\Http\Controllers\Admin\FinancialController::class, 'clubSales'])->name('club-sales');
    });

    // Profile Management (Super Admin)
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
});

// Impersonation route for tenants
Route::middleware([
    'web',
    \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
])->group(function () {
    Route::get('/impersonate', [LoginController::class, 'impersonate'])
        ->name('tenant.impersonate')
        ->middleware('signed');
});

// Webhook Routes
Route::post('/webhooks/asaas', [FinancialController::class, 'webhook'])->name('webhooks.asaas');
Route::post('/webhooks/asaas/tenant', [TenantRegistrationController::class, 'asaasWebhook'])->name('webhooks.asaas.tenant');
Route::post('/webhooks/asaas/order', [FinancialController::class, 'handleOrderPayment'])->name('webhooks.asaas.order');

// Tenant Registration Routes
Route::get('/tenant/register', [TenantRegistrationController::class, 'create'])->name('tenant.register');
Route::post('/tenant/register', [TenantRegistrationController::class, 'store'])->name('tenant.register.store');
Route::get('/tenant/payment', [TenantRegistrationController::class, 'payment'])->name('tenant.payment');
Route::get('/tenant/success', [TenantRegistrationController::class, 'success'])->name('tenant.success');
Route::post('/api/tenant/check-subdomain', [TenantRegistrationController::class, 'checkSubdomain'])->name('api.tenant.check-subdomain');

// Public Site Routes - Teams and Athletes
Route::get('/public/teams', [SiteController::class, 'teams'])->name('site.teams');
Route::get('/public/teams/{team:id}', [SiteController::class, 'team'])->name('site.team');
Route::get('/public/athletes', [SiteController::class, 'athletes'])->name('site.athletes');
Route::get('/athletes/{athlete:id}', [SiteController::class, 'athlete'])->name('site.athlete');
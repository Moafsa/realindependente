<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AthleteController;
// use App\Http\Controllers\Api\TeamController; // TODO: Criar controller
use App\Http\Controllers\Api\AIController;
// use App\Http\Controllers\Api\FinancialController; // TODO: Criar controller
// use App\Http\Controllers\Api\ProductController; // TODO: Criar controller
use App\Http\Controllers\Api\PortalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    // Site Public Data
    // Route::get('/teams', [TeamController::class, 'publicIndex']); // TODO: Criar controller
    // Route::get('/teams/{team}', [TeamController::class, 'publicShow']); // TODO: Criar controller
    Route::get('/athletes', [AthleteController::class, 'publicIndex']);
    Route::get('/athletes/{athlete}', [AthleteController::class, 'publicShow']);
    // Route::get('/products', [ProductController::class, 'publicIndex']); // TODO: Criar controller
    // Route::get('/products/{product}', [ProductController::class, 'publicShow']); // TODO: Criar controller
    
    // Tenant Registration API
    Route::get('/tenant/check-subdomain', [\App\Http\Controllers\Api\TenantController::class, 'checkSubdomain']);
});

// Protected API Routes
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    
    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Athletes API
    Route::apiResource('athletes', AthleteController::class);
    Route::post('/athletes/{athlete}/toggle-status', [AthleteController::class, 'toggleStatus']);
    Route::get('/athletes/{athlete}/performance', [AthleteController::class, 'getPerformance']);
    Route::post('/athletes/{athlete}/performance', [AthleteController::class, 'updatePerformance']);
    Route::get('/athletes/{athlete}/ai-plans', [AthleteController::class, 'getAiPlans']);

    // Teams API
    // Route::apiResource('teams', TeamController::class); // TODO: Criar controller
    // Route::post('/teams/{team}/toggle-status', [TeamController::class, 'toggleStatus']); // TODO: Criar controller
    // Route::get('/teams/{team}/athletes', [TeamController::class, 'getAthletes']); // TODO: Criar controller
    // Route::post('/teams/{team}/athletes/{athlete}', [TeamController::class, 'addAthlete']); // TODO: Criar controller
    // Route::delete('/teams/{team}/athletes/{athlete}', [TeamController::class, 'removeAthlete']); // TODO: Criar controller

    // AI API
    Route::prefix('ai')->group(function () {
        Route::post('/athletes/{athlete}/workout', [AIController::class, 'generateWorkout']);
        Route::post('/athletes/{athlete}/nutrition', [AIController::class, 'generateNutrition']);
        Route::post('/athletes/{athlete}/recovery', [AIController::class, 'generateRecovery']);
        Route::get('/athletes/{athlete}/plans', [AIController::class, 'getAthletePlans']);
        Route::get('/plans/{plan}', [AIController::class, 'getPlan']);
        Route::post('/plans/{plan}/favorite', [AIController::class, 'toggleFavorite']);
        Route::delete('/plans/{plan}', [AIController::class, 'deletePlan']);
        Route::get('/stats', [AIController::class, 'getStats']);
    });

    // Financial API
    // Route::prefix('financial')->group(function () { // TODO: Criar controller
    //     Route::get('/transactions', [FinancialController::class, 'getTransactions']);
    //     Route::post('/transactions', [FinancialController::class, 'createTransaction']);
    //     Route::get('/transactions/{transaction}', [FinancialController::class, 'getTransaction']);
    //     Route::post('/transactions/{transaction}/cancel', [FinancialController::class, 'cancelTransaction']);
    //     Route::get('/summary', [FinancialController::class, 'getSummary']);
    //     Route::post('/generate-charges', [FinancialController::class, 'generateCharges']);
    //     Route::get('/reports/monthly', [FinancialController::class, 'getMonthlyReport']);
    // });

    // Products API (Admin)
    // Route::middleware(['role:admin'])->group(function () { // TODO: Criar controller
    //     Route::apiResource('products', ProductController::class);
    //     Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus']);
    //     Route::get('/products/{product}/analytics', [ProductController::class, 'getAnalytics']);
    // });

    // Dashboard Analytics
    Route::get('/dashboard/stats', function () {
        return response()->json([
            'athletes_count' => \App\Models\Athlete::count(),
            'teams_count' => \App\Models\Team::count(),
            'ai_plans_count' => \App\Models\AiPlan::count(),
            'revenue_monthly' => \App\Models\FinancialTransaction::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ]);
    });

    // Portal API (Athletes and Guardians)
    Route::middleware(['role:athlete|guardian'])->prefix('portal')->group(function () {
        Route::get('/performance-data', [PortalController::class, 'getPerformanceData']);
        Route::get('/upcoming-trainings', [PortalController::class, 'getUpcomingTrainings']);
        Route::get('/notifications', [PortalController::class, 'getNotifications']);
    });

});

// Webhook Routes (no auth required)
// Route::prefix('webhooks')->group(function () { // TODO: Criar controller
//     Route::post('/asaas', [FinancialController::class, 'handleAsaasWebhook']);
//     Route::post('/asaas/payment-update', [FinancialController::class, 'handlePaymentUpdate']);
// });
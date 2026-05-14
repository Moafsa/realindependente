<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Domain;
use App\Services\AsaasService;
use App\Services\TenantRegistrationService;
use App\Jobs\CreateTenantDatabase;
use App\Jobs\CreateTenantAdmin;
use App\Mail\TenantWelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stancl\Tenancy\Facades\Tenancy;

class TenantRegistrationController extends Controller
{
    protected AsaasService $asaasService;
    protected TenantRegistrationService $registrationService;

    public function __construct(AsaasService $asaasService, TenantRegistrationService $registrationService)
    {
        $this->asaasService = $asaasService;
        $this->registrationService = $registrationService;
    }

    /**
     * Show the tenant registration form.
     */
    public function create(Request $request)
    {
        $plans = Plan::active()->ordered()->get();
        $selectedPlanId = $request->query('plan');
        
        return view('tenant.register', compact('plans', 'selectedPlanId'));
    }

    /**
     * Handle tenant registration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'club_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:tenants,domain|regex:/^[a-z0-9-]+$/',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'plan_id' => 'required|exists:plans,id',
            'admin_phone' => 'required|string|max:20',
            'admin_cpf_cnpj' => 'required|string|max:20',
            'terms' => 'required|accepted',
        ]);

        try {
            $plan = Plan::findOrFail($request->plan_id);
            $frequency = $request->input('frequency', 'monthly');
            $price = $plan->getCalculatedPrice($frequency);
            
            // Gera ID de sessão para armazenar dados temporários
            $sessionId = $this->registrationService->generateSessionId();
            
            // Armazena dados em cache aguardando confirmação de pagamento
            $registrationData = [
                'club_name' => $request->club_name,
                'subdomain' => $request->subdomain,
                'admin_name' => $request->admin_name,
                'admin_email' => $request->admin_email,
                'admin_password' => $request->admin_password,
                'admin_phone' => $request->admin_phone,
                'admin_cpf_cnpj' => $request->admin_cpf_cnpj,
                'plan_id' => $request->plan_id,
                'plan_price' => $price,
                'frequency' => $frequency,
            ];
            
            $this->registrationService->storeRegistrationData($sessionId, $registrationData, 60); // 60 minutos

            // Cria customer no Asaas
            $customerData = [
                'name' => $request->club_name,
                'email' => $request->admin_email,
                'phone' => $request->admin_phone,
                'cpf_cnpj' => $request->admin_cpf_cnpj,
            ];
            
            $asaasCustomer = $this->asaasService->createCustomer($customerData);
            
            // Cria assinatura no Asaas
            $subscriptionData = [
                'customer_id' => $asaasCustomer['id'],
                'value' => $price,
                'next_due_date' => now()->addDays(7)->format('Y-m-d'),
                'description' => "Assinatura {$plan->name} ({$frequency}) - {$request->club_name}",
                'external_reference' => $sessionId,
                'billing_type' => 'PIX',
                'cycle' => match($frequency) {
                    'quarterly' => 'QUARTERLY',
                    'semiannual' => 'SEMIANNUAL',
                    'yearly' => 'YEARLY',
                    default => 'MONTHLY',
                },
            ];
            
            $asaasSubscription = $this->asaasService->createSubscription($subscriptionData);
            
            // Cria usuário no banco central para permitir login pelo portal principal
            \App\Models\User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->admin_password),
                'role' => 'admin',
                'phone' => $request->admin_phone,
                'is_active' => true,
            ]);

            // Cria tenant com status 'pending' (aguardando pagamento)
            $tenant = Tenant::create([
                'id' => $request->subdomain,
                'name' => $request->club_name,
                'email' => $request->admin_email,
                'domain' => $request->subdomain,
                'database_name' => 'tenant_' . Str::random(10),
                'plan_id' => $request->plan_id,
                'status' => 'pending',
                'asaas_customer_id' => $asaasCustomer['id'],
                'asaas_subscription_id' => $asaasSubscription['id'],
                'data' => [
                    'session_id' => $sessionId,
                    'registration_data' => $registrationData,
                ],
            ]);

            // Cria domínio
            $domain = Domain::create([
                'domain' => $request->subdomain . '.' . request()->getHost(),
                'tenant_id' => $tenant->id,
                'is_primary' => true,
                'is_verified' => false,
            ]);

            // [NOVO] Cria banco de dados e admin IMEDIATAMENTE (mesmo pendente)
            // para permitir acesso restrito ao dashboard
            CreateTenantDatabase::dispatch($tenant);
            CreateTenantAdmin::dispatch($tenant, [
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => $request->admin_password,
            ]);

            // Redireciona para o dashboard do subdomínio (SSO via .localhost)
            return redirect()->to($tenant->url . '/dashboard')->with('info', 'Bem-vindo! Seu clube foi criado. Realize o pagamento para desbloquear todas as funcionalidades.');

        } catch (\Exception $e) {
            Log::error('TenantRegistrationController: Erro ao criar tenant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors([
                'error' => 'Erro ao criar o clube: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Show success page.
     */
    public function success()
    {
        return view('tenant.success');
    }

    /**
     * Show payment page.
     */
    public function payment(Request $request)
    {
        $tenant = Tenant::findOrFail($request->tenant);
        $subscriptionId = $request->subscription_id;

        try {
            $subscription = $this->asaasService->getSubscription($subscriptionId);
            
            // Log do objeto para debug se necessário
            Log::info('Pagamento: Assinatura recuperada', ['subscription_id' => $subscriptionId]);
            
            // Tenta encontrar a URL de pagamento (pode variar dependendo do tipo de cobrança ou ambiente)
            $paymentUrl = $subscription['invoiceUrl'] ?? 
                         $subscription['bankSlipUrl'] ?? 
                         ($subscription['externalReference'] ? "https://sandbox.asaas.com/i/{$subscription['id']}" : null);

            if (!$paymentUrl) {
                Log::warning('Pagamento: URL de faturamento não encontrada na assinatura', [
                    'subscription_id' => $subscriptionId,
                    'subscription_data' => $subscription
                ]);
            }

            return view('tenant.payment', [
                'tenant' => $tenant,
                'subscription' => $subscription,
                'payment_url' => $paymentUrl,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tenant.register')
                ->with('error', 'Erro ao buscar informações de pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Handle Asaas webhook para confirmação de pagamento de assinatura.
     */
    public function asaasWebhook(Request $request)
    {
        try {
            $data = $request->all();
            $event = $data['event'] ?? null;
            $payment = $data['payment'] ?? null;
            $subscription = $data['subscription'] ?? null;

            Log::info('TenantRegistrationController: Webhook recebido do Asaas', [
                'event' => $event,
                'payment_id' => $payment['id'] ?? null,
                'subscription_id' => $subscription['id'] ?? null,
            ]);

            // Processa apenas eventos de pagamento confirmado de assinatura
            if ($event === 'PAYMENT_CONFIRMED' && $subscription) {
                $subscriptionId = $subscription['id'];
                
                // Busca o tenant pela subscription_id
                $tenant = Tenant::where('asaas_subscription_id', $subscriptionId)
                    ->where('status', 'pending')
                    ->first();

                if (!$tenant) {
                    Log::warning('TenantRegistrationController: Tenant não encontrado para subscription', [
                        'subscription_id' => $subscriptionId,
                    ]);
                    return response()->json(['status' => 'ok', 'message' => 'Tenant não encontrado']);
                }

                // Ativa o tenant
                $this->activateTenant($tenant);

                Log::info('TenantRegistrationController: Tenant ativado com sucesso via webhook', [
                    'tenant_id' => $tenant->id,
                    'subscription_id' => $subscriptionId,
                ]);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('TenantRegistrationController: Erro ao processar webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ativa o tenant após confirmação de pagamento.
     */
    private function activateTenant(Tenant $tenant): void
    {
        DB::beginTransaction();

        try {
            // Recupera dados do registro do cache
            $sessionId = $tenant->data['session_id'] ?? null;
            $registrationData = $sessionId ? $this->registrationService->getRegistrationData($sessionId) : null;

            if (!$registrationData) {
                // Se não encontrar no cache, tenta usar os dados do tenant
                $registrationData = $tenant->data['registration_data'] ?? null;
            }

            if (!$registrationData) {
                throw new \Exception('Dados de registro não encontrados');
            }

            // Atualiza status do tenant
            $tenant->update([
                'status' => 'active',
                'trial_ends_at' => now()->addDays(14),
            ]);

            // Marca domínio como verificado
            $tenant->domains()->update(['is_verified' => true]);

            // Dispara jobs para criar banco de dados e admin
            CreateTenantDatabase::dispatch($tenant);
            CreateTenantAdmin::dispatch($tenant, [
                'name' => $registrationData['admin_name'],
                'email' => $registrationData['admin_email'],
                'password' => $registrationData['admin_password'],
            ]);

            // Envia e-mail de boas-vindas
            Mail::to($registrationData['admin_email'])->send(
                new TenantWelcomeMail($tenant, $tenant->domain, $registrationData['admin_email'])
            );

            // Remove dados do cache
            if ($sessionId) {
                $this->registrationService->clearRegistrationData($sessionId);
            }

            DB::commit();

            Log::info('TenantRegistrationController: Tenant ativado com sucesso', [
                'tenant_id' => $tenant->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('TenantRegistrationController: Erro ao ativar tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Create tenant database.
     */
    private function createTenantDatabase(Tenant $tenant)
    {
        // This would typically be handled by the tenancy package
        // The actual implementation depends on your tenancy configuration
        
        tenancy()->initialize($tenant);
        
        // Run tenant migrations
        \Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
        ]);
        
        // Run tenant seeders
        \Artisan::call('db:seed', [
            '--class' => 'TenantSeeder',
        ]);
    }

    /**
     * Create tenant admin user.
     */
    private function createTenantAdmin(Tenant $tenant, Request $request)
    {
        // This would be handled by the tenant seeder
        // The admin user is created in the TenantSeeder
    }


    /**
     * Check subdomain availability.
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');
        
        if (empty($subdomain)) {
            return response()->json(['available' => false, 'message' => 'Subdomínio é obrigatório']);
        }

        // Check if subdomain is already taken
        $exists = Tenant::where('domain', $subdomain)->exists();
        
        if ($exists) {
            return response()->json(['available' => false, 'message' => 'Subdomínio já está em uso']);
        }

        // Check if subdomain is valid
        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            return response()->json(['available' => false, 'message' => 'Subdomínio deve conter apenas letras minúsculas, números e hífens']);
        }

        return response()->json(['available' => true, 'message' => 'Subdomínio disponível']);
    }
}

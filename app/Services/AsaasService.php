<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private ?string $apiKey;
    private ?string $baseUrl;
    private ?string $environment;
    private ?string $walletId;

    public function __construct()
    {
        // Removed eager loading of config because this service can be injected
        // in a controller constructor before tenancy is initialized.
        // We will fetch the config dynamically when needed.
    }

    private function getApiKey(): ?string
    {
        $dbApiKey = \App\Models\SiteSetting::get('asaas_api_key');
        return $dbApiKey ?: config('services.asaas.api_key');
    }

    private function getBaseUrl(): ?string
    {
        $dbBaseUrl = \App\Models\SiteSetting::get('asaas_api_url');
        return $dbBaseUrl ?: config('services.asaas.base_url', 'https://sandbox.asaas.com/api/v3');
    }

    private function getWalletId(): ?string
    {
        $dbApiKey = \App\Models\SiteSetting::get('asaas_api_key');
        
        $dbWalletId = null;
        if (function_exists('tenancy') && tenancy()->initialized) {
            $dbWalletId = tenancy()->central(function () {
                return \App\Models\SiteSetting::get('asaas_wallet_id');
            });
        } else {
            $dbWalletId = \App\Models\SiteSetting::get('asaas_wallet_id');
        }

        $platformWalletId = $dbWalletId ?: config('services.asaas.wallet_id');
        $tenantWalletId = \App\Models\SiteSetting::get('asaas_wallet_id') ?: config('services.asaas.wallet_id');
        $platformApiKey = config('services.asaas.api_key');

        if ($tenantWalletId === $platformWalletId || empty($dbApiKey) || $dbApiKey === $platformApiKey || !(function_exists('tenancy') && tenancy()->initialized)) {
            return null;
        }

        return $platformWalletId;
    }

    /**
     * Create a new customer in Asaas.
     */
    public function createCustomer(array $data): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post($this->getBaseUrl() . '/customers', [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'cpfCnpj' => $data['cpf_cnpj'] ?? null,
            'postalCode' => $data['postal_code'] ?? null,
            'address' => $data['address'] ?? null,
            'addressNumber' => $data['address_number'] ?? null,
            'complement' => $data['complement'] ?? null,
            'province' => $data['province'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Create Customer', [
            'status' => $response->status(),
            'response' => $response->body(),
            'data' => $data,
        ]);

        throw new \Exception('Erro ao criar cliente no Asaas: ' . $response->body());
    }

    /**
     * Create a new charge in Asaas.
     */
    public function createCharge(array $data): array
    {
        $payload = [
            'customer' => $data['customer_id'],
            'billingType' => $data['billing_type'] ?? 'PIX',
            'value' => $data['value'],
            'dueDate' => $data['due_date'],
            'description' => $data['description'] ?? null,
            'externalReference' => $data['external_reference'] ?? null,
            'installmentCount' => $data['installment_count'] ?? null,
            'installmentValue' => $data['installment_value'] ?? null,
            'discount' => $data['discount'] ?? null,
            'interest' => $data['interest'] ?? null,
            'fine' => $data['fine'] ?? null,
            'postalService' => $data['postal_service'] ?? false,
        ];

        // Adiciona split se houver configuração de taxa administrativa
        $walletId = $this->getWalletId();
        if (isset($data['split_percentage']) && $data['split_percentage'] > 0 && $walletId) {
            $payload['split'] = [
                [
                    'walletId' => $walletId,
                    'percentualValue' => $data['split_percentage'],
                ]
            ];
        }

        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post($this->getBaseUrl() . '/payments', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Create Charge', [
            'status' => $response->status(),
            'response' => $response->body(),
            'data' => $data,
        ]);

        throw new \Exception('Erro ao criar cobrança no Asaas: ' . $response->body());
    }

    /**
     * Get charge details from Asaas.
     */
    public function getCharge(string $chargeId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->get($this->getBaseUrl() . '/payments/' . $chargeId);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Get Charge', [
            'status' => $response->status(),
            'response' => $response->body(),
            'charge_id' => $chargeId,
        ]);

        throw new \Exception('Erro ao buscar cobrança no Asaas: ' . $response->body());
    }

    /**
     * Cancel a charge in Asaas.
     */
    public function cancelCharge(string $chargeId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->delete($this->getBaseUrl() . '/payments/' . $chargeId);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Cancel Charge', [
            'status' => $response->status(),
            'response' => $response->body(),
            'charge_id' => $chargeId,
        ]);

        throw new \Exception('Erro ao cancelar cobrança no Asaas: ' . $response->body());
    }

    /**
     * Get customer details from Asaas.
     */
    public function getCustomer(string $customerId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->get($this->getBaseUrl() . '/customers/' . $customerId);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Get Customer', [
            'status' => $response->status(),
            'response' => $response->body(),
            'customer_id' => $customerId,
        ]);

        throw new \Exception('Erro ao buscar cliente no Asaas: ' . $response->body());
    }

    /**
     * List charges for a customer.
     */
    public function getCustomerCharges(string $customerId, array $filters = []): array
    {
        $params = array_merge([
            'customer' => $customerId,
        ], $filters);

        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->get($this->getBaseUrl() . '/payments', $params);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Get Customer Charges', [
            'status' => $response->status(),
            'response' => $response->body(),
            'customer_id' => $customerId,
            'filters' => $filters,
        ]);

        throw new \Exception('Erro ao buscar cobranças do cliente no Asaas: ' . $response->body());
    }

    /**
     * Create a subscription in Asaas.
     */
    public function createSubscription(array $data): array
    {
        $payload = [
            'customer' => $data['customer_id'],
            'billingType' => $data['billing_type'] ?? 'PIX',
            'value' => $data['value'],
            'nextDueDate' => $data['next_due_date'],
            'description' => $data['description'] ?? null,
            'externalReference' => $data['external_reference'] ?? null,
            'cycle' => $data['cycle'] ?? 'MONTHLY',
        ];

        // Adiciona split se houver configuração de taxa administrativa
        $walletId = $this->getWalletId();
        if (isset($data['split_percentage']) && $data['split_percentage'] > 0 && $walletId) {
            $payload['split'] = [
                [
                    'walletId' => $walletId,
                    'percentualValue' => $data['split_percentage'],
                ]
            ];
        }

        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post($this->getBaseUrl() . '/subscriptions', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Create Subscription', [
            'status' => $response->status(),
            'response' => $response->body(),
            'data' => $data,
        ]);

        throw new \Exception('Erro ao criar assinatura no Asaas: ' . $response->body());
    }

    /**
     * Get subscription details from Asaas.
     */
    public function getSubscription(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->get($this->getBaseUrl() . '/subscriptions/' . $subscriptionId);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Get Subscription', [
            'status' => $response->status(),
            'response' => $response->body(),
            'subscription_id' => $subscriptionId,
        ]);

        throw new \Exception('Erro ao buscar assinatura no Asaas: ' . $response->body());
    }

    public function getSubscriptionPayments(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->get($this->getBaseUrl() . "/subscriptions/{$subscriptionId}/payments");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Get Subscription Payments', [
            'status' => $response->status(),
            'response' => $response->body(),
            'subscription_id' => $subscriptionId,
        ]);

        return [];
    }

    /**
     * Cancel a subscription in Asaas.
     */
    public function cancelSubscription(string $subscriptionId): array
    {
        $response = Http::withHeaders([
            'access_token' => $this->getApiKey(),
        ])->delete($this->getBaseUrl() . '/subscriptions/' . $subscriptionId);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Asaas API Error - Cancel Subscription', [
            'status' => $response->status(),
            'response' => $response->body(),
            'subscription_id' => $subscriptionId,
        ]);

        throw new \Exception('Erro ao cancelar assinatura no Asaas: ' . $response->body());
    }

    /**
     * Handle webhook from Asaas.
     */
    public function handleWebhook(array $data): void
    {
        $event = $data['event'] ?? null;
        $payment = $data['payment'] ?? null;

        if (!$event || !$payment) {
            Log::warning('Asaas Webhook - Invalid data', $data);
            return;
        }

        // Handle different events
        switch ($event) {
            case 'PAYMENT_CONFIRMED':
                $this->handlePaymentConfirmed($payment);
                break;
            case 'PAYMENT_RECEIVED':
                $this->handlePaymentReceived($payment);
                break;
            case 'PAYMENT_OVERDUE':
                $this->handlePaymentOverdue($payment);
                break;
            case 'PAYMENT_DELETED':
                $this->handlePaymentDeleted($payment);
                break;
            default:
                Log::info('Asaas Webhook - Unhandled event', [
                    'event' => $event,
                    'payment' => $payment,
                ]);
        }
    }

    /**
     * Handle payment confirmed event.
     */
    private function handlePaymentConfirmed(array $payment): void
    {
        // Update order status to paid
        $order = \App\Models\Order::where('asaas_payment_id', $payment['id'])->first();
        
        if ($order) {
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Registra como receita no fluxo de caixa
            \App\Models\CashFlow::create([
                'description' => "Pedido #{$order->id} - " . ($order->athlete->full_name ?? $order->user->name ?? 'Cliente'),
                'amount' => $order->total_amount,
                'type' => 'entry',
                'date' => now(),
                'category' => $order->athlete_id ? 'Assinatura' : 'Loja',
                'status' => 'completed',
                'notes' => "Registrado automaticamente via webhook Asaas",
                'created_by' => null,
            ]);
        }
    }

    /**
     * Handle payment received event.
     */
    private function handlePaymentReceived(array $payment): void
    {
        // Similar to payment confirmed
        $this->handlePaymentConfirmed($payment);
    }

    /**
     * Handle payment overdue event.
     */
    private function handlePaymentOverdue(array $payment): void
    {
        // Update order status to overdue
        $order = \App\Models\Order::where('asaas_payment_id', $payment['id'])->first();
        
        if ($order) {
            $order->update(['status' => 'overdue']);
        }
    }

    /**
     * Handle payment deleted event.
     */
    private function handlePaymentDeleted(array $payment): void
    {
        // Update order status to cancelled
        $order = \App\Models\Order::where('asaas_payment_id', $payment['id'])->first();
        
        if ($order) {
            $order->update(['status' => 'cancelled']);
        }
    }
}

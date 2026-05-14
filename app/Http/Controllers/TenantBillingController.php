<?php

namespace App\Http\Controllers;

use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantBillingController extends Controller
{
    protected AsaasService $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Show tenant's billing history and plans.
     */
    public function index()
    {
        $tenant = tenant();
        $invoices = [];
        $latestPendingInvoice = null;

        try {
            if ($tenant->asaas_customer_id) {
                // Fetch charges from Asaas (limit to 20)
                $response = $this->asaasService->getCustomerCharges($tenant->asaas_customer_id, [
                    'limit' => 20,
                    'order' => 'desc'
                ]);
                
                $invoices = $response['data'] ?? [];
                
                // Find latest pending invoice
                foreach ($invoices as $invoice) {
                    if ($invoice['status'] === 'PENDING' || $invoice['status'] === 'OVERDUE') {
                        $latestPendingInvoice = $invoice;
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('TenantBillingController@index Error: ' . $e->getMessage());
        }

        return view('admin.billing.index', compact('tenant', 'invoices', 'latestPendingInvoice'));
    }

    /**
     * Redirect to the latest pending payment URL.
     */
    public function pay()
    {
        $tenant = tenant();

        try {
            if (!$tenant->asaas_customer_id) {
                return back()->with('error', 'Configuração de pagamento não encontrada.');
            }

            $response = $this->asaasService->getCustomerCharges($tenant->asaas_customer_id, [
                'status' => 'PENDING',
                'limit' => 1
            ]);

            $invoice = $response['data'][0] ?? null;

            if ($invoice && isset($invoice['invoiceUrl'])) {
                return redirect()->away($invoice['invoiceUrl']);
            }
            
            // If no pending, check overdue
            $response = $this->asaasService->getCustomerCharges($tenant->asaas_customer_id, [
                'status' => 'OVERDUE',
                'limit' => 1
            ]);
            
            $invoice = $response['data'][0] ?? null;
            
            if ($invoice && isset($invoice['invoiceUrl'])) {
                return redirect()->away($invoice['invoiceUrl']);
            }

            return back()->with('info', 'Não há faturas pendentes de pagamento no momento.');

        } catch (\Exception $e) {
            Log::error('TenantBillingController@pay Error: ' . $e->getMessage());
            return back()->with('error', 'Erro ao processar pagamento. Por favor, contate o suporte.');
        }
    }
}

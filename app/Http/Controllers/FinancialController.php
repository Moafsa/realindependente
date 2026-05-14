<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Order;
use App\Models\CashFlow;
use App\Models\User;
use App\Services\AsaasService;
use App\Events\ChargeGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    private AsaasService $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Display the financial dashboard.
     */
    public function index()
    {
        try {
            $totalSalaries = User::where('role', 'coach')->where('is_active', true)->sum('salary');

            // Busca o percentual de taxa administrativa do plano do tenant
            $adminFeePercent = 0;
            $ecommerceTaxPercent = 0;
            if (tenancy()->initialized) {
                $plan = \App\Models\Plan::find(tenant('plan_id'));
                if ($plan) {
                    $adminFeePercent = $plan->admin_fee_percentage;
                    $ecommerceTaxPercent = $plan->ecommerce_tax_rate;
                }
            }

            $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
            
            // Calcula as taxas da plataforma sobre a receita paga
            // Nota: Isso é uma estimativa baseada nos percentuais atuais. 
            // Em um sistema real, poderíamos salvar o split real de cada transação.
            $subscriptionRevenue = Order::where('status', 'paid')
                ->whereNotNull('athlete_id')
                ->sum('total_amount');
            
            $shopRevenue = Order::where('status', 'paid')
                ->whereNull('athlete_id')
                ->sum('total_amount');
            
            $platformFees = ($subscriptionRevenue * ($adminFeePercent / 100)) + ($shopRevenue * ($ecommerceTaxPercent / 100));
            $netRevenue = $totalRevenue - $platformFees;

            // Get financial statistics
            $stats = [
                'total_revenue' => $totalRevenue,
                'pending_revenue' => Order::where('status', 'pending')->sum('total_amount'),
                'overdue_revenue' => Order::where('status', 'overdue')->sum('total_amount'),
                'total_orders' => Order::count(),
                'paid_orders' => Order::where('status', 'paid')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_expenses' => CashFlow::where('type', 'exit')->where('status', 'completed')->sum('amount'),
                'estimated_salaries' => $totalSalaries,
                'platform_fees' => $platformFees,
                'net_revenue' => $netRevenue,
            ];

            // Get monthly revenue (last 6 months)
            $monthlyRevenue = Order::select(
                    DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
                    DB::raw('SUM(total_amount) as revenue'),
                    DB::raw('COUNT(*) as orders_count')
                )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Get recent orders
            $recentOrders = Order::with(['user', 'athlete'])
                ->latest()
                ->take(10)
                ->get();

            // Get teams for charge generation
            $teams = Team::withCount('athletes')->get();
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabelas não existirem, usar valores padrão
            $stats = [
                'total_revenue' => 0,
                'pending_revenue' => 0,
                'overdue_revenue' => 0,
                'total_orders' => 0,
                'paid_orders' => 0,
                'pending_orders' => 0,
            ];
            $monthlyRevenue = collect([]);
            $recentOrders = collect([]);
            $teams = collect([]);
        }

        return view('financial.index', compact('stats', 'monthlyRevenue', 'recentOrders', 'teams'));
    }

    /**
     * Generate charges for athletes.
     */
    public function generateCharges(Request $request)
    {
        $request->validate([
            'teams' => 'required|array|min:1',
            'teams.*' => 'exists:teams,id',
            'month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after:today',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $teams = Team::whereIn('id', $request->teams)->get();
            $athletes = Athlete::whereIn('team_id', $request->teams)
                ->where('is_active', true)
                ->get();
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao buscar equipes/atletas: ' . $e->getMessage());
        }

        if ($athletes->isEmpty()) {
            return back()->with('error', 'Nenhum atleta ativo encontrado nas equipes selecionadas.');
        }

        $chargesCreated = 0;
        $errors = [];

        // Busca o percentual de taxa administrativa do plano do tenant
        $adminFee = 0;
        if (tenancy()->initialized) {
            $plan = \App\Models\Plan::find(tenant('plan_id'));
            if ($plan) {
                $adminFee = $plan->admin_fee_percentage;
            }
        }

        foreach ($athletes as $athlete) {
            try {
                // Create order in database
                $order = Order::create([
                    'user_id' => $athlete->user_id ?? null,
                    'athlete_id' => $athlete->id,
                    'total_amount' => $request->amount,
                    'status' => 'pending',
                    'billing_address' => [
                        'name' => $athlete->full_name,
                        'email' => $athlete->guardian_email,
                        'phone' => $athlete->guardian_contact,
                    ],
                ]);

                // Create charge in Asaas
                $chargeData = [
                    'customer_id' => $athlete->user->asaas_customer_id ?? null,
                    'value' => $request->amount,
                    'due_date' => $request->due_date,
                    'description' => $request->description ?? "Mensalidade - {$athlete->team->name} - {$request->month}",
                    'external_reference' => $order->id,
                    'billing_type' => 'PIX',
                    'split_percentage' => $adminFee,
                ];

                if (!$athlete->user || !$athlete->user->asaas_customer_id) {
                    $errors[] = "Atleta {$athlete->full_name} não possui conta no Asaas";
                    continue;
                }

                $charge = $this->asaasService->createCharge($chargeData);

                // Update order with Asaas data
                $order->update([
                    'asaas_payment_id' => $charge['id'],
                    'asaas_payment_url' => $charge['invoiceUrl'] ?? null,
                ]);

                // Dispara evento de cobrança gerada
                event(new ChargeGenerated($order));

                $chargesCreated++;

            } catch (\Exception $e) {
                $errors[] = "Erro ao criar cobrança para {$athlete->full_name}: " . $e->getMessage();
            }
        }

        $message = "Cobranças criadas: {$chargesCreated}";
        if (!empty($errors)) {
            $message .= " | Erros: " . count($errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Display charges list.
     */
    public function charges(Request $request)
    {
        try {
            $query = Order::with(['user', 'athlete']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Search
            if ($request->filled('search')) {
                $query->whereHas('athlete', function ($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%');
                });
            }

            $orders = $query->latest()->paginate(20);
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabelas não existirem, usar valores padrão
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
        }

        return view('financial.charges', compact('orders'));
    }

    /**
     * Display charge details.
     */
    public function showCharge(Order $order)
    {
        $order->load(['user', 'athlete', 'orderItems.product']);

        // Get charge details from Asaas
        $chargeDetails = null;
        if ($order->asaas_payment_id) {
            try {
                $chargeDetails = $this->asaasService->getCharge($order->asaas_payment_id);
            } catch (\Exception $e) {
                // Log error but don't break the page
                \Log::error('Error fetching charge details from Asaas', [
                    'order_id' => $order->id,
                    'asaas_payment_id' => $order->asaas_payment_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('financial.charge-details', compact('order', 'chargeDetails'));
    }

    /**
     * Cancel a charge.
     */
    public function cancelCharge(Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('error', 'Não é possível cancelar uma cobrança já paga.');
        }

        if ($order->asaas_payment_id) {
            try {
                $this->asaasService->cancelCharge($order->asaas_payment_id);
            } catch (\Exception $e) {
                return back()->with('error', 'Erro ao cancelar cobrança no Asaas: ' . $e->getMessage());
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Cobrança cancelada com sucesso!');
    }

    /**
     * Handle Asaas webhook.
     */
    public function webhook(Request $request)
    {
        $data = $request->all();

        try {
            $this->asaasService->handleWebhook($data);
        } catch (\Exception $e) {
            \Log::error('Asaas Webhook Error', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle Asaas webhook for order payments.
     */
    public function handleOrderPayment(Request $request)
    {
        $data = $request->all();
        $event = $data['event'] ?? null;
        $payment = $data['payment'] ?? null;

        Log::info('FinancialController: Webhook de pagamento de pedido recebido', [
            'event' => $event,
            'payment_id' => $payment['id'] ?? null,
        ]);

        try {
            if (!$event || !$payment) {
                Log::warning('FinancialController: Dados inválidos no webhook de pedido', $data);
                return response()->json(['status' => 'ok', 'message' => 'Dados inválidos']);
            }

            // Busca o pedido pelo external_reference
            $externalReference = $payment['externalReference'] ?? null;
            
            if (!$externalReference || !str_starts_with($externalReference, 'order_')) {
                Log::warning('FinancialController: External reference inválido', [
                    'external_reference' => $externalReference,
                ]);
                return response()->json(['status' => 'ok', 'message' => 'External reference inválido']);
            }

            $orderId = str_replace('order_', '', $externalReference);
            $order = Order::find($orderId);

            if (!$order) {
                Log::warning('FinancialController: Pedido não encontrado', [
                    'order_id' => $orderId,
                    'payment_id' => $payment['id'] ?? null,
                ]);
                return response()->json(['status' => 'ok', 'message' => 'Pedido não encontrado']);
            }

            // Processa o evento
            switch ($event) {
                case 'PAYMENT_CONFIRMED':
                case 'PAYMENT_RECEIVED':
                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'asaas_payment_id' => $payment['id'],
                    ]);

                    // Registra como receita no fluxo de caixa
                    CashFlow::create([
                        'description' => "Pedido #{$order->id} - " . ($order->athlete->full_name ?? $order->user->name ?? 'Cliente'),
                        'amount' => $order->total_amount,
                        'type' => 'entry',
                        'date' => now(),
                        'category' => $order->athlete_id ? 'Assinatura' : 'Loja',
                        'status' => 'completed',
                        'notes' => "Registrado automaticamente via webhook de pagamento",
                        'created_by' => null,
                    ]);
                    
                    // Dispara evento de cobrança confirmada se for mensalidade
                    if ($order->athlete_id) {
                        event(new \App\Events\ChargeGenerated($order));
                    }
                    
                    Log::info('FinancialController: Pedido marcado como pago', [
                        'order_id' => $order->id,
                        'payment_id' => $payment['id'],
                    ]);
                    break;

                case 'PAYMENT_OVERDUE':
                    $order->update(['status' => 'overdue']);
                    
                    if ($order->athlete_id) {
                        event(new \App\Events\ChargeOverdue($order));
                    }
                    
                    Log::info('FinancialController: Pedido marcado como vencido', [
                        'order_id' => $order->id,
                    ]);
                    break;

                case 'PAYMENT_DELETED':
                    $order->update(['status' => 'cancelled']);
                    Log::info('FinancialController: Pedido cancelado', [
                        'order_id' => $order->id,
                    ]);
                    break;

                default:
                    Log::info('FinancialController: Evento não processado', [
                        'event' => $event,
                        'order_id' => $order->id,
                    ]);
            }

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('FinancialController: Erro ao processar webhook de pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get financial summary for API.
     */
    public function summary()
    {
        try {
            $summary = [
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
                'pending_revenue' => Order::where('status', 'pending')->sum('total_amount'),
                'overdue_revenue' => Order::where('status', 'overdue')->sum('total_amount'),
                'total_orders' => Order::count(),
                'paid_orders' => Order::where('status', 'paid')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'overdue_orders' => Order::where('status', 'overdue')->count(),
            ];
        } catch (\Exception $e) {
            $summary = [
                'total_revenue' => 0,
                'pending_revenue' => 0,
                'overdue_revenue' => 0,
                'total_orders' => 0,
                'paid_orders' => 0,
                'pending_orders' => 0,
                'overdue_orders' => 0,
            ];
        }

        return response()->json($summary);
    }
}

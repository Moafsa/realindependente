<?php

namespace App\Listeners;

use App\Events\ChargeOverdue;
use App\Services\WuzapiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendChargeReminderListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected WuzapiService $wuzapiService;

    /**
     * Create the event listener.
     */
    public function __construct(WuzapiService $wuzapiService)
    {
        $this->wuzapiService = $wuzapiService;
    }

    /**
     * Handle the event.
     */
    public function handle(ChargeOverdue $event): void
    {
        $order = $event->order;

        try {
            // Busca o atleta relacionado ao pedido
            $athlete = $order->athlete ?? null;

            if (!$athlete) {
                Log::warning('SendChargeReminderListener: Pedido sem atleta associado', [
                    'order_id' => $order->id,
                ]);
                return;
            }

            // Busca o telefone do responsável
            $phoneNumber = $athlete->guardian_contact ?? null;

            if (empty($phoneNumber)) {
                Log::warning('SendChargeReminderListener: Atleta sem telefone de contato', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                ]);
                return;
            }

            // Calcula dias até o vencimento
            $dueDate = $order->due_date ?? $order->created_at->addDays(7);
            $daysUntilDue = now()->diffInDays($dueDate, false);

            // Prepara os dados da cobrança
            $chargeData = [
                'athlete_name' => $athlete->full_name,
                'amount' => $order->total_amount,
                'due_date' => $dueDate,
                'description' => $order->description ?? 'Mensalidade',
            ];

            // Envia o lembrete via WhatsApp
            $sent = $this->wuzapiService->sendChargeReminder($chargeData, $phoneNumber, max(1, $daysUntilDue));

            if ($sent) {
                Log::info('SendChargeReminderListener: Lembrete de cobrança enviado com sucesso', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                    'phone' => $phoneNumber,
                    'days_until_due' => $daysUntilDue,
                ]);
            } else {
                Log::error('SendChargeReminderListener: Falha ao enviar lembrete de cobrança', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                    'phone' => $phoneNumber,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('SendChargeReminderListener: Exceção ao processar evento', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}


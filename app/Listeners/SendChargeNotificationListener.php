<?php

namespace App\Listeners;

use App\Events\ChargeGenerated;
use App\Services\WuzapiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendChargeNotificationListener implements ShouldQueue
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
    public function handle(ChargeGenerated $event): void
    {
        $order = $event->order;

        try {
            // Busca o atleta relacionado ao pedido
            $athlete = $order->athlete ?? null;

            if (!$athlete) {
                Log::warning('SendChargeNotificationListener: Pedido sem atleta associado', [
                    'order_id' => $order->id,
                ]);
                return;
            }

            // Busca o telefone do responsável
            $phoneNumber = $athlete->guardian_contact ?? null;

            if (empty($phoneNumber)) {
                Log::warning('SendChargeNotificationListener: Atleta sem telefone de contato', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                ]);
                return;
            }

            // Prepara os dados da cobrança
            $chargeData = [
                'athlete_name' => $athlete->full_name,
                'amount' => $order->total_amount,
                'due_date' => $order->due_date ?? $order->created_at->addDays(7),
                'description' => $order->description ?? 'Mensalidade',
            ];

            // Envia a notificação via WhatsApp
            $sent = $this->wuzapiService->sendChargeNotification($chargeData, $phoneNumber);

            if ($sent) {
                Log::info('SendChargeNotificationListener: Notificação de cobrança enviada com sucesso', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                    'phone' => $phoneNumber,
                ]);
            } else {
                Log::error('SendChargeNotificationListener: Falha ao enviar notificação de cobrança', [
                    'order_id' => $order->id,
                    'athlete_id' => $athlete->id,
                    'phone' => $phoneNumber,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('SendChargeNotificationListener: Exceção ao processar evento', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}


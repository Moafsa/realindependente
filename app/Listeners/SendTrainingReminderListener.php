<?php

namespace App\Listeners;

use App\Events\TrainingScheduled;
use App\Services\WuzapiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTrainingReminderListener implements ShouldQueue
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
    public function handle(TrainingScheduled $event): void
    {
        $trainingData = $event->trainingData;
        $athletes = $event->athletes;

        try {
            foreach ($athletes as $athlete) {
                // Busca o telefone do atleta ou responsável
                $phoneNumber = $athlete['phone'] ?? $athlete['guardian_contact'] ?? null;

                if (empty($phoneNumber)) {
                    Log::warning('SendTrainingReminderListener: Atleta sem telefone de contato', [
                        'athlete_id' => $athlete['id'] ?? null,
                        'athlete_name' => $athlete['name'] ?? 'Desconhecido',
                    ]);
                    continue;
                }

                // Adiciona o nome do atleta aos dados do treino
                $athleteTrainingData = array_merge($trainingData, [
                    'athlete_name' => $athlete['name'] ?? 'Atleta',
                ]);

                // Envia o lembrete via WhatsApp
                $sent = $this->wuzapiService->sendTrainingReminder($athleteTrainingData, $phoneNumber);

                if ($sent) {
                    Log::info('SendTrainingReminderListener: Lembrete de treino enviado com sucesso', [
                        'athlete_id' => $athlete['id'] ?? null,
                        'athlete_name' => $athlete['name'] ?? 'Desconhecido',
                        'phone' => $phoneNumber,
                    ]);
                } else {
                    Log::error('SendTrainingReminderListener: Falha ao enviar lembrete de treino', [
                        'athlete_id' => $athlete['id'] ?? null,
                        'athlete_name' => $athlete['name'] ?? 'Desconhecido',
                        'phone' => $phoneNumber,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('SendTrainingReminderListener: Exceção ao processar evento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}


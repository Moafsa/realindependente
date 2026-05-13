<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiGeneratedContent;
use App\Services\WuzapiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAiPlanNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'athlete:send-ai-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificações de treinos e dietas da IA via WhatsApp nos horários agendados.';

    /**
     * Execute the console command.
     */
    public function handle(WuzapiService $wuzapi)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i');
        
        $this->info("Iniciando verificação de notificações para o horário: {$currentTime}");

        // Buscar planos ativos que possuam notificações configuradas
        $activePlans = AiGeneratedContent::where('status', 'active')
            ->whereNotNull('notification_settings')
            ->where('start_date', '<=', $now->toDateString())
            ->where('end_date', '>=', $now->toDateString())
            ->with('athlete')
            ->get();

        $count = 0;

        foreach ($activePlans as $plan) {
            $settings = $plan->notification_settings;
            if (!is_array($settings)) continue;

            // Verificar se o horário atual está na lista de notificações do plano
            if (in_array($currentTime, $settings)) {
                $athlete = $plan->athlete;
                
                if (!$athlete || !$athlete->phone) {
                    Log::warning("Notificação IA pulada: Atleta não encontrado ou sem telefone para o plano ID {$plan->id}");
                    continue;
                }

                // Determinar uma tarefa atual baseada no tipo do plano
                $currentTask = $this->getCurrentTask($plan, $currentTime);

                $planData = [
                    'athlete_name' => $athlete->name,
                    'title' => $plan->title,
                    'type' => $plan->type,
                    'goal' => $plan->goal,
                    'current_task' => $currentTask
                ];

                $this->info("Enviando notificação para {$athlete->name} (Horário: {$currentTime})");
                
                $success = $wuzapi->sendAiPlanNotification($planData, $athlete->phone);

                if ($success) {
                    $count++;
                }
            }
        }

        $this->info("Processamento concluído. Total de notificações enviadas: {$count}");
        
        return Command::SUCCESS;
    }

    /**
     * Tenta identificar a tarefa específica para o horário atual.
     */
    private function getCurrentTask($plan, $time)
    {
        $content = $plan->content;
        
        if ($plan->type === 'meal_plan' && isset($content['meals'])) {
            foreach ($content['meals'] as $meal) {
                // Tenta bater o horário (considerando margem de erro ou formato)
                if (str_contains($meal['time'] ?? '', $time)) {
                    return $meal['name'] . ": " . implode(', ', $meal['foods'] ?? []);
                }
            }
        }

        if ($plan->type === 'workout_plan' && isset($content['exercises'])) {
            // Para treinos, pegamos um resumo ou o primeiro exercício relevante
            $exercises = array_slice($content['exercises'], 0, 2);
            $names = array_map(fn($e) => $e['name'], $exercises);
            return "Foco de hoje: " . implode(', ', $names) . "... e mais.";
        }

        return "Confira seu protocolo atualizado no app!";
    }
}

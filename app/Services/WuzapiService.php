<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WuzapiService
{
    private ?string $apiKey;
    private ?string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.wuzapi.api_key');
        $this->baseUrl = config('services.wuzapi.base_url', 'https://api.wuzapi.com.br');
    }

    /**
     * Envia notificação de cobrança gerada via WhatsApp.
     * 
     * @param array $chargeData Dados da cobrança
     * @param string $phoneNumber Número do telefone do responsável
     * @return bool
     */
    public function sendChargeNotification(array $chargeData, string $phoneNumber): bool
    {
        $message = $this->buildChargeNotificationMessage($chargeData);

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envia lembrete de cobrança próxima do vencimento.
     * 
     * @param array $chargeData Dados da cobrança
     * @param string $phoneNumber Número do telefone do responsável
     * @param int $daysUntilDue Dias até o vencimento
     * @return bool
     */
    public function sendChargeReminder(array $chargeData, string $phoneNumber, int $daysUntilDue = 3): bool
    {
        $message = $this->buildChargeReminderMessage($chargeData, $daysUntilDue);

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envia lembrete de treino agendado.
     * 
     * @param array $trainingData Dados do treino
     * @param string $phoneNumber Número do telefone do atleta/responsável
     * @return bool
     */
    public function sendTrainingReminder(array $trainingData, string $phoneNumber): bool
    {
        $message = $this->buildTrainingReminderMessage($trainingData);

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envia lembrete de jogo agendado.
     * 
     * @param array $gameData Dados do jogo
     * @param string $phoneNumber Número do telefone do atleta/responsável
     * @return bool
     */
    public function sendGameReminder(array $gameData, string $phoneNumber): bool
    {
        $message = $this->buildGameReminderMessage($gameData);

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Envia mensagem genérica via WhatsApp.
     * 
     * @param string $phoneNumber Número do telefone (formato: 5511999999999)
     * @param string $message Mensagem a ser enviada
     * @param bool $retry Se deve tentar novamente em caso de falha
     * @return bool
     */
    public function sendMessage(string $phoneNumber, string $message, bool $retry = true): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('WuzapiService: API key não configurada');
            return false;
        }

        // Formata o número do telefone (remove caracteres especiais)
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/message/send', [
                'phone' => $phoneNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WuzapiService: Mensagem enviada com sucesso', [
                    'phone' => $phoneNumber,
                    'message_preview' => substr($message, 0, 50) . '...',
                ]);
                return true;
            }

            // Se falhou e retry está habilitado, tenta novamente uma vez
            if ($retry) {
                Log::warning('WuzapiService: Falha ao enviar mensagem, tentando novamente', [
                    'phone' => $phoneNumber,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                sleep(2); // Aguarda 2 segundos antes de tentar novamente
                return $this->sendMessage($phoneNumber, $message, false);
            }

            Log::error('WuzapiService: Erro ao enviar mensagem', [
                'phone' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('WuzapiService: Exceção ao enviar mensagem', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Constrói mensagem de notificação de cobrança.
     */
    private function buildChargeNotificationMessage(array $chargeData): string
    {
        $athleteName = $chargeData['athlete_name'] ?? 'Atleta';
        $amount = number_format($chargeData['amount'] ?? 0, 2, ',', '.');
        $dueDate = isset($chargeData['due_date']) ? date('d/m/Y', strtotime($chargeData['due_date'])) : 'Data não informada';
        $description = $chargeData['description'] ?? 'Mensalidade';

        $message = "🏆 *Nova Cobrança Gerada*\n\n";
        $message .= "Olá! Uma nova cobrança foi gerada para *{$athleteName}*.\n\n";
        $message .= "📋 *Detalhes:*\n";
        $message .= "• Descrição: {$description}\n";
        $message .= "• Valor: R$ {$amount}\n";
        $message .= "• Vencimento: {$dueDate}\n\n";
        $message .= "Por favor, realize o pagamento até a data de vencimento.\n\n";
        $message .= "Obrigado!";

        return $message;
    }

    /**
     * Constrói mensagem de lembrete de cobrança.
     */
    private function buildChargeReminderMessage(array $chargeData, int $daysUntilDue): string
    {
        $athleteName = $chargeData['athlete_name'] ?? 'Atleta';
        $amount = number_format($chargeData['amount'] ?? 0, 2, ',', '.');
        $dueDate = isset($chargeData['due_date']) ? date('d/m/Y', strtotime($chargeData['due_date'])) : 'Data não informada';
        $description = $chargeData['description'] ?? 'Mensalidade';

        $daysText = $daysUntilDue == 1 ? '1 dia' : "{$daysUntilDue} dias";

        $message = "⏰ *Lembrete de Cobrança*\n\n";
        $message .= "Olá! Esta é uma mensagem de lembrete sobre uma cobrança pendente.\n\n";
        $message .= "📋 *Detalhes:*\n";
        $message .= "• Atleta: *{$athleteName}*\n";
        $message .= "• Descrição: {$description}\n";
        $message .= "• Valor: R$ {$amount}\n";
        $message .= "• Vencimento: {$dueDate}\n";
        $message .= "• Faltam: {$daysText}\n\n";
        $message .= "Por favor, realize o pagamento para evitar atrasos.\n\n";
        $message .= "Obrigado!";

        return $message;
    }

    /**
     * Constrói mensagem de lembrete de treino.
     */
    private function buildTrainingReminderMessage(array $trainingData): string
    {
        $athleteName = $trainingData['athlete_name'] ?? 'Atleta';
        $date = isset($trainingData['date']) ? date('d/m/Y', strtotime($trainingData['date'])) : 'Data não informada';
        $time = $trainingData['time'] ?? 'Horário não informado';
        $location = $trainingData['location'] ?? 'Local não informado';
        $team = $trainingData['team_name'] ?? 'Equipe';

        $message = "⚽ *Lembrete de Treino*\n\n";
        $message .= "Olá *{$athleteName}*!\n\n";
        $message .= "Você tem um treino agendado:\n\n";
        $message .= "📅 *Data:* {$date}\n";
        $message .= "🕐 *Horário:* {$time}\n";
        $message .= "📍 *Local:* {$location}\n";
        $message .= "👥 *Equipe:* {$team}\n\n";
        $message .= "Não se esqueça de levar seu material de treino!\n\n";
        $message .= "Nos vemos lá! 🏃‍♂️";

        return $message;
    }

    /**
     * Constrói mensagem de lembrete de jogo.
     */
    private function buildGameReminderMessage(array $gameData): string
    {
        $athleteName = $gameData['athlete_name'] ?? 'Atleta';
        $date = isset($gameData['date']) ? date('d/m/Y', strtotime($gameData['date'])) : 'Data não informada';
        $time = $gameData['time'] ?? 'Horário não informado';
        $location = $gameData['location'] ?? 'Local não informado';
        $team = $gameData['team_name'] ?? 'Equipe';
        $opponent = $gameData['opponent'] ?? 'Adversário';

        $message = "⚽ *Lembrete de Jogo*\n\n";
        $message .= "Olá *{$athleteName}*!\n\n";
        $message .= "Você tem um jogo agendado:\n\n";
        $message .= "🏆 *Jogo:* {$team} vs {$opponent}\n";
        $message .= "📅 *Data:* {$date}\n";
        $message .= "🕐 *Horário:* {$time}\n";
        $message .= "📍 *Local:* {$location}\n\n";
        $message .= "Chegue com antecedência para o aquecimento!\n\n";
        $message .= "Boa sorte! 🍀";

        return $message;
    }

    /**
     * Formata o número do telefone para o padrão internacional.
     * Remove caracteres especiais e adiciona código do país se necessário.
     * 
     * @param string $phoneNumber Número do telefone
     * @return string Número formatado
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove todos os caracteres não numéricos
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Se não começar com 55 (código do Brasil), adiciona
        if (!str_starts_with($phoneNumber, '55')) {
            // Se começar com 0, remove
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = substr($phoneNumber, 1);
            }
            // Adiciona código do país
            $phoneNumber = '55' . $phoneNumber;
        }

        return $phoneNumber;
    }
}


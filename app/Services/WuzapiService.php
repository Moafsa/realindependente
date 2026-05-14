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
        $dbApiKey = null;
        $dbBaseUrl = null;

        if (function_exists('tenancy') && tenancy()->initialized) {
            // Get from central settings if in tenant context
            $dbApiKey = tenancy()->central(function () {
                return \App\Models\SiteSetting::get('wuzapi_api_key');
            });
            $dbBaseUrl = tenancy()->central(function () {
                return \App\Models\SiteSetting::get('wuzapi_base_url');
            });
        } else {
            $dbApiKey = \App\Models\SiteSetting::get('wuzapi_api_key');
            $dbBaseUrl = \App\Models\SiteSetting::get('wuzapi_base_url');
        }

        $this->apiKey = $dbApiKey ?: config('services.wuzapi.api_key', 'admin123456');
        $this->baseUrl = $dbBaseUrl ?: 'http://wuzapi:8080';
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
     * Envia notificação de Plano IA (Treino ou Dieta).
     * 
     * @param array $planData Dados do plano e atleta
     * @param string $phoneNumber Número do telefone
     * @return bool
     */
    public function sendAiPlanNotification(array $planData, string $phoneNumber): bool
    {
        $message = $this->buildAiPlanNotificationMessage($planData);

        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Helper: Garante que o usuário existe no WuzAPI.
     */
    private function ensureUser(): bool
    {
        try {
            // Tenta listar usuários
            $url = $this->baseUrl . '/admin/users';
            $listRes = Http::withHeaders(['Authorization' => $this->apiKey])
                ->get($url);

            if ($listRes->successful()) {
                $users = $listRes->json();
                $users = is_array($users) ? $users : ($users['instances'] ?? []);
                foreach ($users as $u) {
                    if (($u['name'] ?? '') === 'admin' || ($u['token'] ?? '') === $this->apiKey) {
                        return true;
                    }
                }
            }

            // Cria usuário se não existir
            $createRes = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/admin/users', [
                'name' => 'admin',
                'token' => $this->apiKey,
                'webhook' => '',
                'expiration' => 0,
                'events' => "Message,ReadReceipt,Disconnected,Connected"
            ]);

            return $createRes->successful() || $createRes->status() === 409;
        } catch (\Exception $e) {
            Log::error('[WuzapiService] ensureUser exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém o status da sessão do WhatsApp.
     */
    public function getSessionStatus(): string
    {
        if (empty($this->apiKey)) return 'DISCONNECTED';

        try {
            $response = Http::withHeaders(['Token' => $this->apiKey])
                ->get($this->baseUrl . "/session/status");

            if (!$response->successful()) {
                // Fallback: verificar via admin/users
                if ($this->ensureUser()) {
                    $adminRes = Http::withHeaders(['Authorization' => $this->apiKey])
                        ->get($this->baseUrl . '/admin/users');
                    if ($adminRes->successful()) {
                        $data = $adminRes->json();
                        $instances = $data['instances'] ?? [];
                        foreach ($instances as $inst) {
                            if (($inst['name'] ?? '') === 'admin' && ($inst['connected'] ?? false)) {
                                return 'CONNECTED';
                            }
                        }
                    }
                }
                return 'DISCONNECTED';
            }

            $data = $response->json()['data'] ?? [];
            
            $isLoggedIn = !empty($data['LoggedIn'] ?? $data['loggedIn'] ?? $data['authenticated'] ?? false) || ($data['state'] ?? '') === 'CONNECTED';
            $isConnecting = !empty($data['Connected'] ?? $data['connected'] ?? false) || in_array($data['state'] ?? '', ['CONNECTING', 'STARTING', 'QRCODE']) || !empty($data['QRCode']);

            if ($isLoggedIn) return 'CONNECTED';
            if ($isConnecting) return 'QRCODE';
            
            return 'DISCONNECTED';
        } catch (\Exception $e) {
            Log::error('[WuzapiService] getSessionStatus exception: ' . $e->getMessage());
            return 'DISCONNECTED';
        }
    }

    /**
     * Obtém o QR Code para conexão.
     */
    public function getQrCode()
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'API Key não configurada.'];
        }

        try {
            // 1. Garante que o usuário existe
            if (!$this->ensureUser()) {
                return ['success' => false, 'message' => 'Falha ao verificar/criar usuário no WuzAPI.'];
            }

            // 2. Tenta conectar a sessão
            $connectUrl = $this->baseUrl . "/session/connect";
            Http::withHeaders(['Token' => $this->apiKey])
                ->post($connectUrl, [
                    'Subscribe' => ["Message", "ReadReceipt", "Disconnected", "Connected"],
                    'Immediate' => false
                ]);

            // 3. Poll para obter o QR Code
            $maxTries = 10;
            for ($i = 0; $i < $maxTries; $i++) {
                $response = Http::withHeaders(['Token' => $this->apiKey])
                    ->get($this->baseUrl . "/session/qr");

                if ($response->successful()) {
                    $qr = $response->json()['data']['QRCode'] ?? null;
                    if ($qr) {
                        return ['success' => true, 'qr' => $qr];
                    }
                }

                sleep(1);
            }

            return [
                'success' => false, 
                'message' => 'O servidor está preparando o QR Code. Por favor, aguarde e tente novamente.'
            ];
        } catch (\Exception $e) {
            Log::error('[WuzapiService] getQrCode exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()];
        }
    }

    /**
     * Desconecta a sessão atual.
     */
    public function disconnectSession(): bool
    {
        if (empty($this->apiKey)) return false;

        try {
            return Http::withHeaders(['Token' => $this->apiKey])
                ->post($this->baseUrl . '/session/logout')->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Envia mensagem via WhatsApp.
     */
    public function sendMessage(string $phoneNumber, string $message, bool $retry = true): bool
    {
        if (empty($this->apiKey)) return false;

        $phone = $this->formatPhoneNumber($phoneNumber);
        if (!str_ends_with($phone, '@s.whatsapp.net')) {
            $phone .= '@s.whatsapp.net';
        }

        try {
            $response = Http::withHeaders([
                'Token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/chat/send/text', [
                'Phone' => $phone,
                'Body' => $message,
            ]);

            if ($response->successful()) {
                return true;
            }

            if ($retry) {
                sleep(2);
                return $this->sendMessage($phoneNumber, $message, false);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('[WuzapiService] sendMessage exception: ' . $e->getMessage());
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
    public function buildGameReminderMessage(array $gameData): string
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
     * Constrói mensagem para Plano IA.
     */
    private function buildAiPlanNotificationMessage(array $planData): string
    {
        $athleteName = $planData['athlete_name'] ?? 'Atleta';
        $title = $planData['title'] ?? 'Plano Especialista';
        $type = ($planData['type'] ?? '') === 'workout_plan' ? '🏋️‍♂️ *HORA DO TREINO*' : '🥗 *HORA DA ALIMENTAÇÃO*';
        $goal = $planData['goal'] ?? 'Alta Performance';
        
        $message = "{$type}\n\n";
        $message .= "Olá *{$athleteName}*! Está na hora de seguir seu protocolo de elite.\n\n";
        $message .= "📌 *Plano:* {$title}\n";
        $message .= "🎯 *Objetivo:* {$goal}\n\n";
        
        if (isset($planData['current_task'])) {
            $message .= "📝 *O que fazer agora:*\n";
            $message .= "_{$planData['current_task']}_\n\n";
        }
        
        $message .= "Acesse seu perfil completo no app para mais detalhes.\n";
        $message .= "Mantenha o foco! 🚀";

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


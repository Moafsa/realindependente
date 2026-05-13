<?php

namespace App\Services;

use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChatSentinel
{
    private array $sensitiveKeywords = [
        'sexo', 'droga', 'aposta', 'dinheiro', 'pagamento fora', 'pix direto',
        'ofensivo', 'xingamento', 'assédio', 'abuso', 'violência'
    ];

    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Analyze a message for potential abuse or sensitive topics.
     * 
     * @param string $content
     * @param User $sender
     * @return array [bool 'isSafe', string|null 'warning', bool 'blockAccount']
     */
    public function analyze(string $content, User $sender): array
    {
        $contentLower = mb_strtolower($content);
        $strikesKey = "chat_strikes_" . $sender->id;
        $strikes = Cache::get($strikesKey, 0);

        // 1. Fast Keyword Check
        foreach ($this->sensitiveKeywords as $keyword) {
            if (str_contains($contentLower, $keyword)) {
                $strikes++;
                Cache::put($strikesKey, $strikes, now()->addDays(7));
                
                return $this->handleViolation($sender, $strikes, "Detectamos palavras sensíveis ({$keyword}) em sua mensagem.");
            }
        }

        // 2. AI Deep Analysis (Optional/Conditional)
        // We only call AI if message is longer than 10 chars to save tokens
        if (strlen($content) > 10) {
            try {
                $safetyReport = $this->checkSafetyWithAI($content);
                if (!$safetyReport['isSafe']) {
                    $strikes++;
                    Cache::put($strikesKey, $strikes, now()->addDays(7));
                    return $this->handleViolation($sender, $strikes, $safetyReport['reason']);
                }
            } catch (\Exception $e) {
                Log::warning("ChatSentinel: AI Analysis failed: " . $e->getMessage());
            }
        }

        return ['isSafe' => true, 'warning' => null, 'blockAccount' => false];
    }

    /**
     * Handle a detected violation.
     */
    private function handleViolation(User $sender, int $strikes, string $reason): array
    {
        Log::warning("ChatSentinel: Violation detected for User {$sender->id}", [
            'reason' => $reason,
            'strikes' => $strikes
        ]);

        if ($strikes >= 3) {
            // Block conversation or account
            return [
                'isSafe' => false,
                'warning' => 'Sua conta foi temporariamente bloqueada para envio de mensagens devido a repetidas violações de conduta.',
                'blockAccount' => true
            ];
        }

        return [
            'isSafe' => false,
            'warning' => "AVISO: Sua mensagem contém conteúdo que viola nossas diretrizes: {$reason}. Esta é sua {$strikes}ª advertência. Na 3ª, sua conta será bloqueada.",
            'blockAccount' => false
        ];
    }

    /**
     * Call AI to check for safety.
     */
    private function checkSafetyWithAI(string $content): array
    {
        $prompt = "Analise a seguinte mensagem de chat em um sistema de clube de futebol (atletas e treinadores). " .
                 "Verifique se há abuso, assédio, proposta indecente, oferta de pagamentos por fora, ou comportamento tóxico. " .
                 "Mensagem: \"{$content}\"\n\n" .
                 "Responda EXCLUSIVAMENTE em JSON: {\"isSafe\": boolean, \"reason\": \"string explicativa em português se não for seguro\"}";

        // We use callOpenAI from AIService but since it's private, we'll implement a simple one here or make it public in AIService.
        // For now, I'll use a direct Http call to avoid modifying AIService too much.
        
        $apiKey = config('services.openai.api_key');
        if (!$apiKey) {
            return ['isSafe' => true, 'reason' => null];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0,
            'max_tokens' => 100
        ]);

        if ($response->successful()) {
            $data = json_decode($response->json()['choices'][0]['message']['content'], true);
            return $data ?? ['isSafe' => true, 'reason' => null];
        }

        return ['isSafe' => true, 'reason' => null];
    }

    /**
     * Notify administrators about a blocked user.
     */
    public function notifyAdmin(User $sender, string $reason)
    {
        // Here you would send an email or database notification
        Log::critical("ALERTA DE SEGURANÇA: Usuário {$sender->name} (ID: {$sender->id}) foi bloqueado no chat. Motivo: {$reason}");
        
        // In a real app: Notification::send($admins, new ChatAbuseAlert($sender, $reason));
    }
}

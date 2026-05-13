<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\AthleteMealLog;
use App\Services\AIService;
use App\Services\WuzapiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    protected $aiService;
    protected $wuzapiService;

    public function __construct(AIService $aiService, WuzapiService $wuzapiService)
    {
        $this->aiService = $aiService;
        $this->wuzapiService = $wuzapiService;
    }

    /**
     * Handle incoming WhatsApp messages from Wuzapi.
     */
    public function handleWhatsApp(Request $request)
    {
        $data = $request->all();
        
        Log::info('WebhookController@handleWhatsApp: Recebido', ['data' => $data]);

        // Check if it's a message event
        if (!isset($data['event']) || $data['event'] !== 'message') {
            return response()->json(['status' => 'ignored']);
        }

        $message = $data['data'] ?? [];
        $from = $message['from'] ?? ''; // Format: 5511999999999@s.whatsapp.net
        $phone = explode('@', $from)[0];
        
        // Find athlete by phone
        // We need to match the phone number carefully (considering international prefix)
        $athlete = Athlete::where('phone', 'like', '%' . substr($phone, -8) . '%')->first();

        if (!$athlete) {
            Log::warning('WebhookController: Atleta não encontrado para o telefone', ['phone' => $phone]);
            return response()->json(['status' => 'athlete_not_found']);
        }

        // Check if message has an image
        if (isset($message['type']) && $message['type'] === 'image') {
            return $this->handleImageMessage($athlete, $message);
        }

        // If it's text, maybe handle commands?
        if (isset($message['text'])) {
            return $this->handleTextMessage($athlete, $message['text']);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle messages containing images (Meal Photos).
     */
    protected function handleImageMessage(Athlete $athlete, array $message)
    {
        try {
            $imageUrl = $message['url'] ?? '';
            if (!$imageUrl) {
                return response()->json(['status' => 'no_image_url']);
            }

            // Download image
            $imageContent = file_get_contents($imageUrl);
            $imageBase64 = base64_encode($imageContent);
            
            // Save locally
            $filename = 'meals/' . $athlete->id . '/' . time() . '.jpg';
            Storage::disk('public')->put($filename, $imageContent);

            // Analyze with AI
            $analysis = $this->aiService->analyzeMealPhoto($imageBase64);

            // Save log
            AthleteMealLog::create([
                'athlete_id' => $athlete->id,
                'photo_path' => $filename,
                'ai_analysis' => $analysis,
                'calories' => $analysis['total']['calories'] ?? 0,
                'proteins' => $analysis['total']['protein'] ?? 0,
                'carbs' => $analysis['total']['carbs'] ?? 0,
                'fats' => $analysis['total']['fat'] ?? 0,
                'consumed_at' => now(),
            ]);

            // Reply to athlete
            $reply = "✅ *Refeição Registrada!*\n\n";
            $reply .= "📊 *Análise:* " . ($analysis['health_score'] ?? '?') . "/10\n";
            $reply .= "🔥 *Calorias:* " . ($analysis['total']['calories'] ?? 0) . " kcal\n";
            $reply .= "💪 *Proteína:* " . ($analysis['total']['protein'] ?? 0) . "g\n";
            $reply .= "🥖 *Carbos:* " . ($analysis['total']['carbs'] ?? 0) . "g\n\n";
            $reply .= "📝 *Dica do Coach:* " . ($analysis['coach_notes'] ?? 'Continue assim!');

            $this->wuzapiService->sendMessage($athlete->phone, $reply);

            return response()->json(['status' => 'success', 'analysis' => $analysis]);

        } catch (\Exception $e) {
            Log::error('WebhookController@handleImageMessage: Erro', ['error' => $e->getMessage()]);
            $this->wuzapiService->sendMessage($athlete->phone, "❌ Erro ao analisar sua foto. Por favor, tente novamente mais tarde.");
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle text messages (Commands).
     */
    protected function handleTextMessage(Athlete $athlete, string $text)
    {
        $text = strtolower(trim($text));
        
        if ($text === 'resumo' || $text === 'status') {
            $todayLogs = AthleteMealLog::where('athlete_id', $athlete->id)
                ->whereDate('consumed_at', now())
                ->get();
            
            $totalCalories = $todayLogs->sum('calories');
            $totalProtein = $todayLogs->sum('proteins');
            
            $reply = "📅 *Resumo de Hoje*\n\n";
            $reply .= "🔥 *Calorias:* {$totalCalories} kcal\n";
            $reply .= "💪 *Proteínas:* {$totalProtein}g\n";
            $reply .= "🍽️ *Refeições:* " . $todayLogs->count() . "\n\n";
            $reply .= "Continue focado no seu objetivo! 🚀";

            $this->wuzapiService->sendMessage($athlete->phone, $reply);
        }

        return response()->json(['status' => 'ok']);
    }
}

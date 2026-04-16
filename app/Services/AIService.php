<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\AiGeneratedContent;
use App\Services\AIUsageService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIService
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;
    private AIUsageService $usageService;

    public function __construct(AIUsageService $usageService)
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
        $this->model = config('services.openai.model', 'gpt-4');
        $this->usageService = $usageService;
    }

    /**
     * Generate a workout plan for an athlete.
     */
    public function generateWorkoutPlan(Athlete $athlete): array
    {
        // Verifica se pode gerar mais planos
        if (!$this->usageService->canGeneratePlan($athlete, 'workout_plan')) {
            throw new \Exception('Limite de gerações mensais atingido. Entre em contato com o administrador.');
        }

        // Verifica cache de planos similares
        $cacheKey = $this->getCacheKey($athlete, 'workout_plan');
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            Log::info('AIService: Plano de treino retornado do cache', [
                'athlete_id' => $athlete->id,
            ]);
            return $cached;
        }

        $prompt = $this->buildWorkoutPrompt($athlete);
        
        try {
            $response = $this->callOpenAI($prompt);
            $content = $this->parseWorkoutResponse($response);
            
            $tokensUsed = $response['usage']['total_tokens'] ?? 0;
            $cost = $this->calculateCost($tokensUsed);
            
            // Save to database
            $aiContent = AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => 'workout_plan',
                'content' => $content,
                'prompt' => $prompt,
                'model_used' => $this->model,
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'generated_at' => now(),
            ]);

            // Registra uso
            $this->usageService->recordUsage($athlete, 'workout_plan', $tokensUsed, $cost);

            // Armazena no cache por 24 horas
            Cache::put($cacheKey, $content, now()->addHours(24));

            return $content;
            
        } catch (\Exception $e) {
            Log::error('AI Service Error - Generate Workout Plan', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erro ao gerar plano de treino: ' . $e->getMessage());
        }
    }

    /**
     * Generate a nutrition plan for an athlete.
     */
    public function generateNutritionPlan(Athlete $athlete): array
    {
        // Verifica se pode gerar mais planos
        if (!$this->usageService->canGeneratePlan($athlete, 'meal_plan')) {
            throw new \Exception('Limite de gerações mensais atingido. Entre em contato com o administrador.');
        }

        // Verifica cache de planos similares
        $cacheKey = $this->getCacheKey($athlete, 'meal_plan');
        $cached = Cache::get($cacheKey);
        
        if ($cached) {
            Log::info('AIService: Plano nutricional retornado do cache', [
                'athlete_id' => $athlete->id,
            ]);
            return $cached;
        }

        $prompt = $this->buildNutritionPrompt($athlete);
        
        try {
            $response = $this->callOpenAI($prompt);
            $content = $this->parseNutritionResponse($response);
            
            $tokensUsed = $response['usage']['total_tokens'] ?? 0;
            $cost = $this->calculateCost($tokensUsed);
            
            // Save to database
            $aiContent = AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => 'meal_plan',
                'content' => $content,
                'prompt' => $prompt,
                'model_used' => $this->model,
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'generated_at' => now(),
            ]);

            // Registra uso
            $this->usageService->recordUsage($athlete, 'meal_plan', $tokensUsed, $cost);

            // Gera imagens dos pratos se necessário
            if (isset($content['meals']) && is_array($content['meals'])) {
                $content['meals'] = $this->addMealImages($content['meals'], $athlete);
            }

            // Armazena no cache por 24 horas
            Cache::put($cacheKey, $content, now()->addHours(24));

            return $content;
            
        } catch (\Exception $e) {
            Log::error('AI Service Error - Generate Nutrition Plan', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erro ao gerar plano nutricional: ' . $e->getMessage());
        }
    }

    /**
     * Build workout prompt for an athlete.
     */
    private function buildWorkoutPrompt(Athlete $athlete): string
    {
        $age = $athlete->age;
        $weight = $athlete->weight;
        $height = $athlete->height;
        $position = $athlete->position;
        $team = $athlete->team->name ?? 'Sem equipe';
        
        $prompt = "Você é um preparador físico especializado em futebol. Crie um plano de treino personalizado para um atleta com as seguintes características:\n\n";
        $prompt .= "Idade: {$age} anos\n";
        $prompt .= "Peso: {$weight} kg\n";
        $prompt .= "Altura: {$height} cm\n";
        $prompt .= "Posição: {$position}\n";
        $prompt .= "Equipe: {$team}\n\n";
        
        $prompt .= "O plano deve incluir:\n";
        $prompt .= "1. Aquecimento (10-15 minutos)\n";
        $prompt .= "2. Exercícios principais (30-45 minutos)\n";
        $prompt .= "3. Alongamento (10-15 minutos)\n\n";
        
        $prompt .= "Foque em exercícios que podem ser feitos em casa, sem equipamentos especiais.\n";
        $prompt .= "Inclua exercícios específicos para a posição do atleta.\n";
        $prompt .= "Considere a idade e nível de condicionamento.\n\n";
        
        $prompt .= "Retorne a resposta em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título do plano\",\n";
        $prompt .= "  \"description\": \"Descrição geral do plano\",\n";
        $prompt .= "  \"duration\": \"Duração total\",\n";
        $prompt .= "  \"difficulty\": \"Nível de dificuldade\",\n";
        $prompt .= "  \"exercises\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"name\": \"Nome do exercício\",\n";
        $prompt .= "      \"description\": \"Descrição do exercício\",\n";
        $prompt .= "      \"sets\": \"Número de séries\",\n";
        $prompt .= "      \"reps\": \"Número de repetições\",\n";
        $prompt .= "      \"duration\": \"Duração (se aplicável)\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"tips\": [\"Dica 1\", \"Dica 2\"]\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Build nutrition prompt for an athlete.
     */
    private function buildNutritionPrompt(Athlete $athlete): string
    {
        $age = $athlete->age;
        $weight = $athlete->weight;
        $height = $athlete->height;
        $position = $athlete->position;
        $team = $athlete->team->name ?? 'Sem equipe';
        
        $prompt = "Você é um nutricionista esportivo especializado em futebol. Crie um plano nutricional personalizado para um atleta com as seguintes características:\n\n";
        $prompt .= "Idade: {$age} anos\n";
        $prompt .= "Peso: {$weight} kg\n";
        $prompt .= "Altura: {$height} cm\n";
        $prompt .= "Posição: {$position}\n";
        $prompt .= "Equipe: {$team}\n\n";
        
        $prompt .= "O plano deve incluir:\n";
        $prompt .= "1. Café da manhã\n";
        $prompt .= "2. Lanche da manhã\n";
        $prompt .= "3. Almoço\n";
        $prompt .= "4. Lanche da tarde\n";
        $prompt .= "5. Jantar\n";
        $prompt .= "6. Lanche da noite (se necessário)\n\n";
        
        $prompt .= "Considere:\n";
        $prompt .= "- A idade e fase de desenvolvimento\n";
        $prompt .= "- As necessidades energéticas para futebol\n";
        $prompt .= "- Alimentos acessíveis no Brasil\n";
        $prompt .= "- Hidratação adequada\n\n";
        
        $prompt .= "Retorne a resposta em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título do plano\",\n";
        $prompt .= "  \"description\": \"Descrição geral do plano\",\n";
        $prompt .= "  \"calories\": \"Total de calorias diárias\",\n";
        $prompt .= "  \"meals\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"name\": \"Nome da refeição\",\n";
        $prompt .= "      \"time\": \"Horário\",\n";
        $prompt .= "      \"foods\": [\"Alimento 1\", \"Alimento 2\"],\n";
        $prompt .= "      \"calories\": \"Calorias da refeição\",\n";
        $prompt .= "      \"description\": \"Descrição da refeição\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"tips\": [\"Dica 1\", \"Dica 2\"]\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(string $prompt): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erro na API da OpenAI: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Parse workout response from OpenAI.
     */
    private function parseWorkoutResponse(array $response): array
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        
        // Try to parse JSON
        $decoded = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If JSON parsing fails, create a structured response
            $decoded = [
                'title' => 'Plano de Treino Personalizado',
                'description' => $content,
                'duration' => '45-60 minutos',
                'difficulty' => 'Intermediário',
                'exercises' => [],
                'tips' => ['Mantenha-se hidratado durante o treino', 'Faça alongamentos antes e depois'],
            ];
        }
        
        return $decoded;
    }

    /**
     * Parse nutrition response from OpenAI.
     */
    private function parseNutritionResponse(array $response): array
    {
        $content = $response['choices'][0]['message']['content'] ?? '';
        
        // Try to parse JSON
        $decoded = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If JSON parsing fails, create a structured response
            $decoded = [
                'title' => 'Plano Nutricional Personalizado',
                'description' => $content,
                'calories' => '2000-2500',
                'meals' => [],
                'tips' => ['Mantenha-se hidratado', 'Consuma alimentos frescos'],
            ];
        }
        
        return $decoded;
    }

    /**
     * Calculate cost based on tokens used.
     */
    private function calculateCost(int $tokens): float
    {
        // GPT-4 pricing: $0.03 per 1K tokens (input) + $0.06 per 1K tokens (output)
        // Using average of input/output pricing
        $costPerToken = 0.045 / 1000; // $0.045 per 1K tokens
        return $tokens * $costPerToken;
    }

    /**
     * Get athlete's AI content history.
     */
    public function getAthleteContent(Athlete $athlete, string $type = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = AiGeneratedContent::where('athlete_id', $athlete->id);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('generated_at', 'desc')->get();
    }

    /**
     * Mark content as favorite.
     */
    public function toggleFavorite(AiGeneratedContent $content): bool
    {
        $content->update(['is_favorite' => !$content->is_favorite]);
        return $content->is_favorite;
    }

    /**
     * Generate training plan with custom parameters.
     */
    public function generateTrainingPlan(array $athleteData, array $goals, int $durationWeeks = 4, string $intensity = 'medium', array $restrictions = []): string
    {
        $prompt = "Generate a personalized training plan for an athlete with the following data:\n";
        foreach ($athleteData as $key => $value) {
            $prompt .= ucfirst($key) . ": " . $value . "\n";
        }
        $prompt .= "Goals: " . implode(', ', $goals) . "\n";
        $prompt .= "Duration: " . $durationWeeks . " weeks\n";
        $prompt .= "Intensity: " . $intensity . "\n";
        $prompt .= "Restrictions: " . implode(', ', $restrictions) . "\n";
        $prompt .= "The plan should be structured weekly, including exercises, sets, reps, and rest times. Provide a detailed plan.";

        try {
            $response = $this->callOpenAI($prompt);
            return $response['choices'][0]['message']['content'] ?? 'No content generated.';
        } catch (\Exception $e) {
            Log::error('AI Service - generateTrainingPlan error: ' . $e->getMessage());
            return 'Error generating training plan: ' . $e->getMessage();
        }
    }

    /**
     * Generate nutrition plan with custom parameters (legacy method).
     * 
     * @deprecated Use generateNutritionPlan(Athlete $athlete) instead
     */
    public function generateNutritionPlanCustom(array $athleteData, array $goals, array $dietaryRestrictions = [], array $allergies = [], array $preferences = [], int $mealsPerDay = 4): string
    {
        $prompt = "Generate a personalized nutrition plan for an athlete with the following data:\n";
        foreach ($athleteData as $key => $value) {
            $prompt .= ucfirst($key) . ": " . $value . "\n";
        }
        $prompt .= "Goals: " . implode(', ', $goals) . "\n";
        $prompt .= "Dietary Restrictions: " . implode(', ', $dietaryRestrictions) . "\n";
        $prompt .= "Allergies: " . implode(', ', $allergies) . "\n";
        $prompt .= "Preferences: " . implode(', ', $preferences) . "\n";
        $prompt .= "Meals per day: " . $mealsPerDay . "\n";
        $prompt .= "The plan should include daily meal suggestions (breakfast, lunch, dinner, snacks) with calorie estimates and macronutrient breakdown. Provide a detailed 7-day plan.";

        try {
            $response = $this->callOpenAI($prompt);
            return $response['choices'][0]['message']['content'] ?? 'No content generated.';
        } catch (\Exception $e) {
            Log::error('AI Service - generateNutritionPlanCustom error: ' . $e->getMessage());
            return 'Error generating nutrition plan: ' . $e->getMessage();
        }
    }

    /**
     * Generate recovery plan.
     */
    public function generateRecoveryPlan(array $athleteData, array $goals, string $injuryType = null, int $durationWeeks = 4, int $painLevel = 5): string
    {
        $prompt = "Generate a personalized recovery plan for an athlete with the following data:\n";
        foreach ($athleteData as $key => $value) {
            $prompt .= ucfirst($key) . ": " . $value . "\n";
        }
        $prompt .= "Recovery Goals: " . implode(', ', $goals) . "\n";
        if ($injuryType) {
            $prompt .= "Injury Type: " . $injuryType . "\n";
        }
        $prompt .= "Current Pain Level (1-10): " . $painLevel . "\n";
        $prompt .= "Duration: " . $durationWeeks . " weeks\n";
        $prompt .= "The plan should include rehabilitation exercises, pain management strategies, and gradual return to activity protocols.";

        try {
            $response = $this->callOpenAI($prompt);
            return $response['choices'][0]['message']['content'] ?? 'No content generated.';
        } catch (\Exception $e) {
            Log::error('AI Service - generateRecoveryPlan error: ' . $e->getMessage());
            return 'Error generating recovery plan: ' . $e->getMessage();
        }
    }

    /**
     * Gera imagem de um prato usando DALL-E.
     * 
     * @param string $mealDescription Descrição da refeição
     * @param Athlete $athlete Atleta (para contexto)
     * @return string|null URL da imagem gerada
     */
    public function generateMealImage(string $mealDescription, Athlete $athlete): ?string
    {
        try {
            $prompt = "Uma foto realista e apetitosa de um prato de comida brasileiro: {$mealDescription}. " .
                     "O prato deve ser saudável e adequado para um atleta de futebol de {$athlete->age} anos. " .
                     "Estilo fotográfico profissional, boa iluminação, fundo neutro.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/images/generations', [
                'model' => 'dall-e-3',
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
                'quality' => 'standard',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $imageUrl = $data['data'][0]['url'] ?? null;
                
                if ($imageUrl) {
                    Log::info('AIService: Imagem de prato gerada com sucesso', [
                        'athlete_id' => $athlete->id,
                        'image_url' => $imageUrl,
                    ]);
                    
                    return $imageUrl;
                }
            }

            Log::warning('AIService: Falha ao gerar imagem de prato', [
                'athlete_id' => $athlete->id,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('AIService: Erro ao gerar imagem de prato', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Adiciona imagens aos pratos do plano nutricional.
     * 
     * @param array $meals Array de refeições
     * @param Athlete $athlete Atleta
     * @return array
     */
    private function addMealImages(array $meals, Athlete $athlete): array
    {
        foreach ($meals as &$meal) {
            if (isset($meal['description']) || isset($meal['foods'])) {
                $description = $meal['description'] ?? implode(', ', $meal['foods'] ?? []);
                $imageUrl = $this->generateMealImage($description, $athlete);
                
                if ($imageUrl) {
                    $meal['image_url'] = $imageUrl;
                }
            }
        }

        return $meals;
    }

    /**
     * Gera chave de cache para planos similares.
     * 
     * @param Athlete $athlete
     * @param string $type Tipo do plano
     * @return string
     */
    private function getCacheKey(Athlete $athlete, string $type): string
    {
        // Cria chave baseada em características do atleta que afetam o plano
        $key = "ai_plan:{$type}:athlete:{$athlete->id}:";
        $key .= "age:{$athlete->age}:";
        $key .= "weight:{$athlete->weight}:";
        $key .= "position:{$athlete->position}";
        
        return $key;
    }
}

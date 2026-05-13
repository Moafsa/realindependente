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
        $this->apiKey = \App\Models\SiteSetting::get('openai_api_key', config('services.openai.api_key'));
        $this->baseUrl = \App\Models\SiteSetting::get('openai_base_url', config('services.openai.base_url', 'https://api.openai.com/v1'));
        $this->model = \App\Models\SiteSetting::get('openai_model', config('services.openai.model', 'gpt-4'));
        $this->usageService = $usageService;
    }

    /**
     * Generate a workout plan for an athlete.
     */
    /**
     * Generate a workout plan for an athlete.
     */
    public function generateWorkoutPlan(Athlete $athlete, ?string $coachInstructions = null): AiGeneratedContent
    {
        Log::info('AIService: [CHECKPOINT 1] Entrou em generateWorkoutPlan', ['athlete_id' => $athlete->id]);

        try {
            // Verifica se pode gerar mais planos
            Log::info('AIService: [CHECKPOINT 2] Verificando limite de uso...');
            if (!$this->usageService->canGeneratePlan($athlete, 'workout_plan')) {
                Log::warning('AIService: [CHECKPOINT 2.1] Limite atingido!');
                throw new \Exception('Limite de gerações mensais atingido. Entre em contato com o administrador.');
            }

            Log::info('AIService: [CHECKPOINT 3] Construindo prompt...');
            $prompt = $this->buildWorkoutPrompt($athlete, $coachInstructions);
            Log::info('AIService: [CHECKPOINT 3.1] Prompt construído com sucesso.');

            Log::info('AIService: [CHECKPOINT 4] Chamando OpenAI (Isso pode demorar)...');
            $response = $this->callOpenAI($prompt);
            Log::info('AIService: [CHECKPOINT 4.1] Resposta recebida da OpenAI.');
            
            $content = $this->parseWorkoutResponse($response);
            Log::info('AIService: [CHECKPOINT 5] Resposta parseada.');
            
            $tokensUsed = $response['usage']['total_tokens'] ?? 0;
            $cost = $this->calculateCost($tokensUsed);
            
            Log::info('AIService: [CHECKPOINT 6] Salvando no banco de dados...');
            // Save to database
            $aiContent = AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => 'workout_plan',
                'status' => 'pending',
                'content' => $content,
                'prompt' => $prompt,
                'model_used' => $this->model,
                'tokens_used' => $tokensUsed,
                'cost' => $cost,
                'generated_at' => now(),
            ]);
            Log::info('AIService: [CHECKPOINT 6.1] Salvo com ID: ' . $aiContent->id);

            // Registra uso
            $this->usageService->recordUsage($athlete, 'workout_plan', $tokensUsed, $cost);

            return $aiContent;
            
        } catch (\Exception $e) {
            Log::error('AIService Error - Generate Workout Plan', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Erro ao gerar plano de treino: ' . $e->getMessage());
        }
    }

    /**
     * Generate a meal plan for an athlete.
     */
    public function generateMealPlan(Athlete $athlete, ?string $coachInstructions = null): AiGeneratedContent
    {
        Log::info('AIService: Iniciando geração de plano nutricional', ['athlete_id' => $athlete->id]);

        // Verifica se pode gerar mais planos
        if (!$this->usageService->canGeneratePlan($athlete, 'meal_plan')) {
            throw new \Exception('Limite de gerações mensais atingido. Entre em contato com o administrador.');
        }

        $prompt = $this->buildNutritionPrompt($athlete, $coachInstructions);
        
        try {
            $response = $this->callOpenAI($prompt);
            $content = $this->parseNutritionResponse($response);
            
            $tokensUsed = $response['usage']['total_tokens'] ?? 0;
            $cost = $this->calculateCost($tokensUsed);
            
            // Save to database
            $aiContent = AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => 'meal_plan',
                'status' => 'pending',
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
                $aiContent->update(['content' => $content]);
            }

            return $aiContent;
            
        } catch (\Exception $e) {
            Log::error('AIService Error - Generate Meal Plan', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erro ao gerar plano nutricional: ' . $e->getMessage());
        }
    }

    /**
     * Build workout prompt for an athlete.
     */
    private function buildWorkoutPrompt(Athlete $athlete, ?string $coachInstructions = null): string
    {
        $age = $athlete->age;
        $weight = $athlete->weight;
        $height = $athlete->height;
        $position = $athlete->position;
        $team = $athlete->team->name ?? 'Sem equipe';
        
        // Obter métricas de performance para contexto
        $metrics = $athlete->performanceRecords()
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->groupBy('metric')
            ->map(fn($group) => $group->first()->value);

        $metricsContext = "";
        if ($metrics->count() > 0) {
            $metricsContext = "\nÚltimas métricas de desempenho (0-100%):\n";
            foreach ($metrics as $metric => $value) {
                $metricsContext .= "- {$metric}: {$value}%\n";
            }
        }
        
        $prompt = "Você é um **Preparador Físico de Elite**, especialista em futebol de alto rendimento com doutorado em fisiologia do exercício. ";
        $prompt .= "Seu objetivo é criar um plano de treino periodizado e altamente técnico para o seguinte atleta:\n\n";
        $prompt .= "DADOS DO ATLETA:\n";
        $prompt .= "Idade: {$age} anos\n";
        $prompt .= "Peso: {$weight} kg\n";
        $prompt .= "Altura: {$height} cm\n";
        $prompt .= "Posição: {$position}\n";
        $prompt .= "Equipe: {$team}\n";
        $prompt .= $metricsContext . "\n";
        
        if ($coachInstructions) {
            $prompt .= "INSTRUÇÕES ADICIONAIS DO TREINADOR:\n";
            $prompt .= "{$coachInstructions}\n\n";
        }
        
        $prompt .= "DIRETRIZES DO PLANO:\n";
        $prompt .= "1. Analise as métricas acima. Se houver notas baixas, priorize exercícios para corrigir essas fraquezas.\n";
        $prompt .= "2. Divida o treino em: Mobilidade, Força Explosiva, Técnica Específica e Core.\n";
        $prompt .= "3. Defina a DURACÃO total em dias e a FREQUÊNCIA semanal que você considera ideal para este objetivo.\n";
        $prompt .= "4. Sugira horários ideais para as notificações de lembrete no WhatsApp.\n\n";
        
        $prompt .= "Retorne a resposta em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título técnico do plano\",\n";
        $prompt .= "  \"description\": \"Análise técnica do porquê este plano foi criado baseado nos dados do atleta\",\n";
        $prompt .= "  \"duration_days\": 30,\n";
        $prompt .= "  \"frequency_label\": \"5x por semana\",\n";
        $prompt .= "  \"notification_suggestions\": [\"08:00\", \"16:00\"],\n";
        $prompt .= "  \"exercises\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"name\": \"Nome técnico do exercício\",\n";
        $prompt .= "      \"description\": \"Execução correta e observação fisiológica\",\n";
        $prompt .= "      \"sets\": \"Séries\",\n";
        $prompt .= "      \"reps\": \"Repetições/Tempo\",\n";
        $prompt .= "      \"rest\": \"Tempo de descanso\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"tips\": [\"Dica de performance 1\", \"Dica de recuperação 2\"]\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Build nutrition prompt for an athlete.
     */
    private function buildNutritionPrompt(Athlete $athlete, ?string $coachInstructions = null): string
    {
        $age = $athlete->age;
        $weight = $athlete->weight;
        $height = $athlete->height;
        $position = $athlete->position;
        $team = $athlete->team->name ?? 'Sem equipe';

        $metrics = $athlete->performanceRecords()
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->groupBy('metric')
            ->map(fn($group) => $group->first()->value);

        $metricsContext = "";
        if ($metrics->count() > 0) {
            $metricsContext = "\nContexto de Performance do Atleta:\n";
            foreach ($metrics as $metric => $value) {
                $metricsContext .= "- {$metric}: {$value}%\n";
            }
        }
        
        $prompt = "Você é um **Nutricionista Esportivo de Alta Performance**, especializado em nutrição funcional para atletas de elite. ";
        $prompt .= "Seu objetivo é criar um protocolo nutricional otimizado para o seguinte atleta:\n\n";
        $prompt .= "DADOS BIOMÉTRICOS:\n";
        $prompt .= "Idade: {$age} anos\n";
        $prompt .= "Peso: {$weight} kg\n";
        $prompt .= "Altura: {$height} cm\n";
        $prompt .= "Posição em campo: {$position}\n";
        $prompt .= $metricsContext . "\n";
        
        if ($coachInstructions) {
            $prompt .= "INSTRUÇÕES ADICIONAIS DO TREINADOR/NUTRICIONISTA:\n";
            $prompt .= "{$coachInstructions}\n\n";
        }
        
        $prompt .= "REQUISITOS DO PLANO:\n";
        $prompt .= "1. Ajuste a distribuição de macronutrientes baseada na idade e posição.\n";
        $prompt .= "2. Inclua sugestões de suplementação básica (se aplicável para a idade).\n";
        $prompt .= "3. Defina a DURAÇÃO (dias) e FREQUÊNCIA (ex: Diário) deste protocolo nutricional.\n";
        $prompt .= "4. Defina horários estratégicos para as notificações de alimentação no WhatsApp.\n\n";
        
        $prompt .= "Retorne a resposta em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título do Protocolo Nutricional\",\n";
        $prompt .= "  \"description\": \"Análise nutricional baseada no biotipo e carga de treino do atleta\",\n";
        $prompt .= "  \"duration_days\": 15,\n";
        $prompt .= "  \"frequency_label\": \"Diário\",\n";
        $prompt .= "  \"calories\": \"Total calórico estimado\",\n";
        $prompt .= "  \"notification_suggestions\": [\"07:30\", \"10:00\", \"13:00\", \"16:00\", \"19:00\"],\n";
        $prompt .= "  \"meals\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"name\": \"Nome da refeição\",\n";
        $prompt .= "      \"time\": \"Horário sugerido\",\n";
        $prompt .= "      \"foods\": [\"Alimento + Porção\", \"Alimento + Porção\"],\n";
        $prompt .= "      \"macronutrients\": \"Foco (ex: Proteína e Fibras)\",\n";
        $prompt .= "      \"description\": \"Importância desta refeição para o treino\"\n";
        $prompt .= "    }\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"tips\": [\"Dica de hidratação\", \"Dica de sono/recuperação\"]\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Call OpenAI API.
     */
    private function callOpenAI(string $prompt): array
    {
        Log::info('AIService: Enviando solicitação para OpenAI...', [
            'model' => $this->model,
            'prompt_length' => strlen($prompt)
        ]);

        $startTime = microtime(true);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120) // Aumentado para 120 segundos
        ->post($this->baseUrl . '/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => 2500, // Aumentado para planos mais longos
            'temperature' => 0.7,
        ]);

        Log::info('AIService: Resposta da OpenAI recebida com sucesso', [
            'duration' => $duration,
            'tokens_used' => $response['usage']['total_tokens'] ?? 0
        ]);

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
     * Generate a blog post based on context.
     */
    public function generateBlogPost(string $context): array
    {
        $prompt = "Você é um especialista em marketing esportivo e redação para blogs de clubes de futebol. ";
        $prompt .= "Crie um post de blog engajador baseado no seguinte contexto do clube: {$context}\n\n";
        
        $prompt .= "O post deve ser profissional, informativo e usar um tom que conecte com torcedores e pais de atletas.\n";
        $prompt .= "Inclua um título atraente, um resumo (excerpt) curto e o conteúdo completo formatado em HTML (apenas tags básicas como <p>, <h2>, <ul>, <li>).\n\n";
        
        $prompt .= "Retorne a resposta EXCLUSIVAMENTE em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título do post\",\n";
        $prompt .= "  \"excerpt\": \"Resumo curto do post para listagem\",\n";
        $prompt .= "  \"content\": \"Conteúdo completo em HTML\",\n";
        $prompt .= "  \"meta_description\": \"Descrição para SEO (máximo 160 caracteres)\"\n";
        $prompt .= "}";

        try {
            $response = $this->callOpenAI($prompt);
            $content = $response['choices'][0]['message']['content'] ?? '';
            
            // Clean content from markdown code blocks if present
            $content = preg_replace('/```json\s?(.*?)\s?```/s', '$1', $content);
            
            $decoded = json_decode(trim($content), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AIService: Erro ao decodificar JSON do post', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                throw new \Exception('A IA retornou um formato inválido.');
            }

            return [
                'data' => $decoded,
                'tokens' => $response['usage']['total_tokens'] ?? 0,
                'model' => $this->model
            ];
            
        } catch (\Exception $e) {
            Log::error('AIService: Erro ao gerar post de blog', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
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

    /**
     * Call OpenAI API with Vision support.
     */
    private function callOpenAIVision(string $prompt, string $imageBase64): array
    {
        Log::info('AIService: Enviando solicitação de VISÃO para OpenAI...', [
            'model' => 'gpt-4o',
            'prompt_length' => strlen($prompt)
        ]);

        $startTime = microtime(true);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)
        ->post($this->baseUrl . '/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:image/jpeg;base64,{$imageBase64}"
                            ]
                        ]
                    ],
                ],
            ],
            'max_tokens' => 1000,
            'temperature' => 0.2,
        ]);

        $duration = microtime(true) - $startTime;

        if (!$response->successful()) {
            Log::error('AIService: Erro na API de Visão da OpenAI', [
                'status' => $response->status(),
                'body' => $response->body(),
                'duration' => $duration
            ]);
            throw new \Exception('Erro na API de Visão da OpenAI: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Analyze a meal photo and return nutritional data.
     */
    public function analyzeMealPhoto(string $imageBase64): array
    {
        $prompt = "Você é um nutricionista esportivo especializado em análise visual de alimentos.\n";
        $prompt .= "Analise a imagem enviada e identifique os alimentos presentes.\n";
        $prompt .= "Estime as quantidades (em gramas) e calcule as informações nutricionais aproximadas (calorias, proteínas, carboidratos e gorduras).\n\n";
        
        $prompt .= "Retorne a resposta EXCLUSIVAMENTE em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"food_items\": [\n";
        $prompt .= "    {\"name\": \"Alimento\", \"amount\": \"100g\", \"calories\": 150, \"protein\": 20, \"carbs\": 0, \"fat\": 5}\n";
        $prompt .= "  ],\n";
        $prompt .= "  \"total\": {\n";
        $prompt .= "    \"calories\": 450,\n";
        $prompt .= "    \"protein\": 40,\n";
        $prompt .= "    \"carbs\": 50,\n";
        $prompt .= "    \"fat\": 15\n";
        $prompt .= "  },\n";
        $prompt .= "  \"health_score\": 8,\n";
        $prompt .= "  \"coach_notes\": \"Excelente escolha de proteína limpa para pós-treino.\"\n";
        $prompt .= "}";

        try {
            $response = $this->callOpenAIVision($prompt, $imageBase64);
            $content = $response['choices'][0]['message']['content'] ?? '';
            
            // Clean content from markdown code blocks if present
            $content = preg_replace('/```json\s?(.*?)\s?```/s', '$1', $content);
            
            $decoded = json_decode(trim($content), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AIService: Erro ao decodificar JSON da análise de refeição', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                throw new \Exception('A IA retornou um formato de análise inválido.');
            }

            return $decoded;
            
        } catch (\Exception $e) {
            Log::error('AIService: Erro ao analisar foto de refeição', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}

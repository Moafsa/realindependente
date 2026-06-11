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

        $duration = microtime(true) - $startTime;

        if (!$response->successful()) {
            Log::error('AIService: Erro na API da OpenAI', [
                'status' => $response->status(),
                'body' => $response->body(),
                'duration' => $duration
            ]);
            throw new \Exception('Erro na API da OpenAI: ' . $response->body());
        }

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
     * Generate a PRO blog post based on detailed parameters.
     */
    public function generateProBlogPost(string $topic, string $description, string $wordCount, string $keywords): array
    {
        // Resgata o contexto do sistema para ajudar a IA
        $settings = \App\Models\SiteSetting::getPublicSettings()->pluck('value', 'key');
        $siteName = $settings->get('site_name', 'Nosso Clube');
        $siteDescription = $settings->get('site_description', 'Um clube esportivo focado em excelência.');
        $methodology = $settings->get('methodology_title', '') . ' - ' . $settings->get('methodology_subtitle', '');
        
        $prompt = "Você é um Copywriter Especialista em SEO e Redator Chefe de um grande portal esportivo. ";
        $prompt .= "Sua missão é criar um artigo de blog altamente profissional e otimizado para os motores de busca.\n\n";
        
        $prompt .= "DADOS DO NOSSO CLUBE/SISTEMA (Use para contexto, mas sem forçar se não encaixar perfeitamente):\n";
        $prompt .= "- NOME DO CLUBE/PROJETO: {$siteName}\n";
        $prompt .= "- DESCRIÇÃO: {$siteDescription}\n";
        $prompt .= "- METODOLOGIA: {$methodology}\n\n";

        $prompt .= "PARÂMETROS OBRIGATÓRIOS DO POST:\n";
        $prompt .= "- TEMA PRINCIPAL: {$topic}\n";
        $prompt .= "- INSTRUÇÕES E CONTEXTO: {$description}\n";
        $prompt .= "- PALAVRAS-CHAVE SEO: {$keywords}\n";
        $prompt .= "- TAMANHO ESPERADO: {$wordCount} palavras\n\n";
        
        $prompt .= "DIRETRIZES DE SEO E REDAÇÃO:\n";
        $prompt .= "1. O post deve ter uma estrutura rica usando as tags HTML corretamente (<h1> para o título principal no json, <h2> e <h3> para os subtítulos no conteúdo).\n";
        $prompt .= "2. Distribua as palavras-chave naturalmente ao longo do texto.\n";
        $prompt .= "3. Use palavras de transição, negritos (<strong>) em termos importantes, e listas (<ul>/<li>) quando fizer sentido para tornar o texto escaneável e agradável.\n";
        $prompt .= "4. O tom deve ser profissional, engajador e adequado para leitores interessados no tema.\n";
        $prompt .= "5. Se as INSTRUÇÕES E CONTEXTO forem curtas ou incompletas, crie conteúdo rico e complementar baseado no Tema Principal usando sua base de conhecimento e os Dados do Clube.\n\n";
        
        $prompt .= "Retorne a resposta EXCLUSIVAMENTE em formato JSON com a seguinte estrutura:\n";
        $prompt .= "{\n";
        $prompt .= "  \"title\": \"Título do post atrativo com palavra-chave\",\n";
        $prompt .= "  \"excerpt\": \"Resumo do post (máx 160 caracteres)\",\n";
        $prompt .= "  \"content\": \"Conteúdo completo, rico em tags HTML (<h2>, <h3>, <p>, <ul>, <strong>)\",\n";
        $prompt .= "  \"keywords\": \"Palavras-chave extraídas ou sugeridas separadas por vírgula\"\n";
        $prompt .= "}";

        try {
            $response = $this->callOpenAI($prompt);
            $content = $response['choices'][0]['message']['content'] ?? '';
            
            // Clean content from markdown code blocks if present
            $content = preg_replace('/```json\s?(.*?)\s?```/s', '$1', $content);
            
            $decoded = json_decode(trim($content), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AIService: Erro ao decodificar JSON do PRO post', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                throw new \Exception('A IA retornou um formato inválido para o post PRO.');
            }

            return $decoded;
            
        } catch (\Exception $e) {
            Log::error('AIService: Erro ao gerar PRO post de blog', [
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
    /**
     * Generate a rotated blog post based on index.
     */
    public function generateRotatedBlogPost(string $baseContext, int $rotationIndex): array
    {
        try {
            switch ($rotationIndex) {
                case 0:
                    return $this->generateAthleteShowcasePost($baseContext);
                case 1:
                    return $this->generatePlanPromotionPost($baseContext);
                case 2:
                    return $this->generateMatchEventPost($baseContext);
                case 3:
                    return $this->generateCoachProfilePost($baseContext);
                case 4:
                    return $this->generateTeamShowcasePost($baseContext);
                case 5:
                default:
                    return $this->generateSeoTrendPost($baseContext);
            }
        } catch (\Exception $e) {
            Log::error("AIService: Erro na geração rotativa index {$rotationIndex}. Fallback para SeoTrendPost.", ['error' => $e->getMessage()]);
            return $this->generateSeoTrendPost($baseContext);
        }
    }

    /**
     * Helper to get system settings context
     */
    private function getSystemContext(): string
    {
        $settings = \App\Models\SiteSetting::getPublicSettings()->pluck('value', 'key');
        $siteName = $settings->get('site_name', 'Nosso Clube');
        $siteDescription = $settings->get('site_description', 'Um clube esportivo focado em excelência.');
        return "- NOME DO CLUBE/ESCOLA: {$siteName}\n- DESCRIÇÃO: {$siteDescription}\n";
    }

    private function generateAthleteShowcasePost(string $baseContext): array
    {
        $athlete = \App\Models\Athlete::with('performanceRecords')
            ->whereHas('performanceRecords')
            ->inRandomOrder()
            ->first();

        if (!$athlete) {
            return $this->generateSeoTrendPost($baseContext);
        }

        $latestPerformance = $athlete->getLatestPerformanceAttribute();
        $metric = $latestPerformance ? $latestPerformance->metric : 'Desempenho Geral';
        $score = $latestPerformance ? $latestPerformance->value : 'Ótimo';

        $prompt = "Você é um Jornalista Esportivo Especializado escrevendo para o blog da escolinha.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "TEMA: Destaque de Atleta\n";
        $prompt .= "ATLETA: {$athlete->user->name} (Idade: {$athlete->age}, Posição: {$athlete->position})\n";
        $prompt .= "DESEMPENHO RECENTE: {$metric} - Nota/Status: {$score}\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escreva um post elogiando a evolução desse atleta na nossa escolinha. Fale sobre o comprometimento e como a nossa metodologia ajudou. Seja profissional, como uma matéria de jornal esportivo destacando uma jovem promessa.\n";

        return $this->executeBlogPrompt($prompt);
    }

    private function generatePlanPromotionPost(string $baseContext): array
    {
        $plan = \App\Models\Product::where('type', 'subscription')->inRandomOrder()->first();

        if (!$plan) {
            return $this->generateSeoTrendPost($baseContext);
        }

        $prompt = "Você é um Redator Especialista em Marketing Esportivo escrevendo para o blog da escolinha.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "TEMA: Benefícios do Plano de Treinamento\n";
        $prompt .= "PLANO/PRODUTO: {$plan->name}\n";
        $prompt .= "DESCRIÇÃO DO PLANO: {$plan->description}\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escreva um post explicando por que os benefícios deste plano são cruciais para a evolução de um atleta moderno. Não faça parecer uma propaganda agressiva (compre agora), mas sim um artigo educativo sobre a importância do investimento no desenvolvimento esportivo.\n";

        return $this->executeBlogPrompt($prompt);
    }

    private function generateMatchEventPost(string $baseContext): array
    {
        $prompt = "Você é um Jornalista Esportivo cobrindo os eventos da escolinha.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "TEMA: Cobertura de Jogos e Eventos\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escreva um post sobre a importância da vivência em campeonatos e amistosos para a formação do caráter e controle emocional dos jovens atletas. Fale sobre o clima nos dias de jogos da nossa escolinha, a presença dos pais e a união das equipes.\n";

        return $this->executeBlogPrompt($prompt);
    }

    private function generateCoachProfilePost(string $baseContext): array
    {
        $coach = \App\Models\User::where('role', 'coach')->orWhere('role', 'admin')->inRandomOrder()->first();
        $coachName = $coach ? $coach->name : 'Nossa Comissão Técnica';

        $prompt = "Você é um Jornalista Esportivo escrevendo um perfil sobre os bastidores da escolinha.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "TEMA: Perfil da Comissão Técnica\n";
        $prompt .= "PROFISSIONAL EM DESTAQUE: {$coachName}\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escreva um post apresentando o papel fundamental desse profissional (ou da comissão técnica) no desenvolvimento diário dos nossos atletas. Foco na liderança, dedicação e impacto positivo.\n";

        return $this->executeBlogPrompt($prompt);
    }

    private function generateTeamShowcasePost(string $baseContext): array
    {
        $team = \App\Models\Team::inRandomOrder()->first();
        $teamName = $team ? $team->name : 'Nossas Categorias de Base';

        $prompt = "Você é um Jornalista Esportivo Especializado nas Categorias de Base.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "TEMA: Destaque de Categoria/Equipe\n";
        $prompt .= "EQUIPE: {$teamName}\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escreva um post focado nos desafios, na união e no processo de formação dessa categoria específica. Fale sobre o amadurecimento tático e coletivo que ocorre nessa faixa etária na nossa escolinha.\n";

        return $this->executeBlogPrompt($prompt);
    }

    private function generateSeoTrendPost(string $baseContext): array
    {
        $prompt = "Você é um Jornalista Esportivo e Redator de SEO.\n";
        $prompt .= $this->getSystemContext();
        $prompt .= "CONTEXTO EXTRA: {$baseContext}\n";
        $prompt .= "TEMA: Assuntos do Momento e Dicas de Futebol (Nutrição, Tática, Psicologia ou Físico)\n\n";
        $prompt .= "INSTRUÇÕES:\n";
        $prompt .= "Escolha UM tema em alta no futebol moderno de base (ex: impacto da nutrição, inteligência emocional, controle de bola) e escreva um artigo altamente informativo e engajador. Relacione a importância desse tema com a metodologia da nossa escolinha.\n";

        return $this->executeBlogPrompt($prompt);
    }

    /**
     * Formats the prompt and calls OpenAI for the blog post generation
     */
    private function executeBlogPrompt(string $systemPrompt): array
    {
        $fullPrompt = $systemPrompt . "\n\n";
        $fullPrompt .= "DIRETRIZES TÉCNICAS OBRIGATÓRIAS:\n";
        $fullPrompt .= "1. O post deve ter uma estrutura rica usando tags HTML (<h1> para o título principal no json, <h2> e <h3> para os subtítulos no conteúdo, <ul> e <li> para listas).\n";
        $fullPrompt .= "2. Use palavras de transição e negritos (<strong>) em termos importantes.\n";
        $fullPrompt .= "3. O tamanho deve ser ideal para leitura (400-600 palavras).\n\n";
        
        $fullPrompt .= "Retorne a resposta EXCLUSIVAMENTE em formato JSON com a seguinte estrutura:\n";
        $fullPrompt .= "{\n";
        $fullPrompt .= "  \"title\": \"Título jornalístico atrativo\",\n";
        $fullPrompt .= "  \"excerpt\": \"Resumo do post (máx 160 caracteres)\",\n";
        $fullPrompt .= "  \"content\": \"Conteúdo completo, rico em tags HTML\",\n";
        $fullPrompt .= "  \"meta_description\": \"Descrição para SEO\"\n";
        $fullPrompt .= "}";

        try {
            $response = $this->callOpenAI($fullPrompt);
            $content = $response['choices'][0]['message']['content'] ?? '';
            
            // Clean content from markdown code blocks if present
            $content = preg_replace('/```json\s?(.*?)\s?```/s', '$1', $content);
            
            $decoded = json_decode(trim($content), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('AIService: Erro ao decodificar JSON no executeBlogPrompt', [
                    'content' => $content,
                    'error' => json_last_error_msg()
                ]);
                throw new \Exception('A IA retornou um formato inválido.');
            }

            return [
                'data' => [
                    'title' => $decoded['title'] ?? 'Novo Post',
                    'content' => $decoded['content'] ?? '',
                    'excerpt' => $decoded['excerpt'] ?? '',
                    'meta_description' => $decoded['meta_description'] ?? ''
                ],
                'tokens' => $response['usage']['total_tokens'] ?? 0,
                'model' => $this->model
            ];
            
        } catch (\Exception $e) {
            Log::error('AIService: Erro na geração automática do blog', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}

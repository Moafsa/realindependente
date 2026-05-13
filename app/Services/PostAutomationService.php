<?php

namespace App\Services;

use App\Models\Post;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PostAutomationService
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Main automation entry point.
     */
    public function processAutomation(): void
    {
        // 1. Gera rascunhos se necessário (estoque baixo)
        $this->generateDraftsIfNeeded();

        // 2. Agenda posts aprovados que ainda não têm data de publicação
        $this->scheduleApprovedPosts();

        // 3. Publica posts agendados cuja data chegou
        $this->publishScheduledPosts();
    }

    /**
     * Verifica se precisa gerar mais rascunhos baseado na frequência.
     */
    private function generateDraftsIfNeeded(): void
    {
        $autoGenerate = SiteSetting::get('blog_auto_generate', false);
        if (!$autoGenerate) return;

        $frequency = (int) SiteSetting::get('blog_post_frequency', 0); // Posts por semana
        if ($frequency <= 0) return;

        // Mantemos sempre um "estoque" de 2 semanas de rascunhos pendentes
        $targetStock = $frequency * 2;
        $currentPending = Post::where('status', 'pending_approval')->count();

        if ($currentPending < $targetStock) {
            $needed = $targetStock - $currentPending;
            $context = SiteSetting::get('blog_post_context', 'Novidades e notícias do nosso clube de futebol.');

            Log::info("PostAutomation: Gerando {$needed} novos rascunhos.");

            for ($i = 0; $i < $needed; $i++) {
                try {
                    $generated = $this->aiService->generateBlogPost($context);
                    
                    Post::create([
                        'title' => $generated['data']['title'],
                        'content' => $generated['data']['content'],
                        'excerpt' => $generated['data']['excerpt'],
                        'meta_description' => $generated['data']['meta_description'],
                        'status' => 'pending_approval',
                        'ai_model' => $generated['model'],
                        'tokens_used' => $generated['tokens'],
                    ]);
                } catch (\Exception $e) {
                    Log::error("PostAutomation: Erro ao gerar post #{$i}: " . $e->getMessage());
                    break; // Para se houver erro crítico
                }
            }
        }
    }

    /**
     * Agenda posts que foram aprovados mas não têm data.
     */
    private function scheduleApprovedPosts(): void
    {
        $frequency = (int) SiteSetting::get('blog_post_frequency', 0);
        if ($frequency <= 0) return;

        // Posts aprovados sem data de agendamento
        $approvedPosts = Post::where('status', 'scheduled')
                            ->whereNull('scheduled_at')
                            ->orderBy('created_at', 'asc')
                            ->get();

        if ($approvedPosts->isEmpty()) return;

        // Encontra a última data de agendamento
        $lastScheduled = Post::whereNotNull('scheduled_at')
                             ->orderBy('scheduled_at', 'desc')
                             ->first();

        $startDate = $lastScheduled ? $lastScheduled->scheduled_at : now();
        
        // Calcula intervalo em dias (ex: 7 dias / 2 posts = 3.5 dias)
        $daysInterval = 7 / $frequency;

        foreach ($approvedPosts as $index => $post) {
            $scheduleDate = $startDate->copy()->addHours((int)($daysInterval * 24 * ($index + 1)));
            $post->update(['scheduled_at' => $scheduleDate]);
            
            Log::info("PostAutomation: Post '{$post->title}' agendado para {$scheduleDate}");
        }
    }

    /**
     * Muda status para publicado se a data chegou.
     */
    private function publishScheduledPosts(): void
    {
        $postsToPublish = Post::where('status', 'scheduled')
                             ->whereNotNull('scheduled_at')
                             ->where('scheduled_at', '<=', now())
                             ->get();

        foreach ($postsToPublish as $post) {
            $post->update([
                'status' => 'published',
                'published_at' => $post->scheduled_at // Usa a data que estava agendada
            ]);
            
            Log::info("PostAutomation: Post '{$post->title}' publicado oficialmente.");
        }
    }
}

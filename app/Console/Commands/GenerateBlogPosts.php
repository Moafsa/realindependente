<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\SiteSetting;
use App\Services\PostAutomationService;

class GenerateBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:generate {count=5} {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera N posts para o blog usando a IA rotativa configurada';

    /**
     * Execute the console command.
     */
    public function handle(PostAutomationService $postAutomationService)
    {
        $count = (int) $this->argument('count');
        $tenantId = $this->argument('tenant_id');
        
        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant não encontrado.");
                return;
            }
            tenancy()->initialize($tenant);
            $this->info("Inicializado tenant: {$tenant->name}");
        }

        $context = SiteSetting::get('blog_post_context', 'Novidades e notícias da Escolinha Real Independente.');
        
        $this->info("Iniciando a geração de {$count} posts para o blog via IA...");
        
        // Vamos expor o método de reflexão ou simplesmente instanciar o AIService e chamar generateRotatedBlogPost diretamente
        $aiService = app(\App\Services\AIService::class);
        
        for ($i = 0; $i < $count; $i++) {
            $this->info("Gerando post " . ($i + 1) . " de {$count}...");
            
            try {
                $totalPosts = \App\Models\Post::count();
                $rotationIndex = ($totalPosts + $i) % 6;
                
                $generated = $aiService->generateRotatedBlogPost($context, $rotationIndex);
                
                $post = \App\Models\Post::create([
                    'title' => $generated['data']['title'],
                    'content' => $generated['data']['content'],
                    'excerpt' => $generated['data']['excerpt'],
                    'meta_description' => $generated['data']['meta_description'],
                    'status' => 'published', // Já publica direto para você ver
                    'published_at' => now(),
                    'ai_model' => $generated['model'],
                    'tokens_used' => $generated['tokens'],
                ]);
                
                $this->info("✓ Post criado com sucesso: {$post->title}");
                
            } catch (\Exception $e) {
                $this->error("Erro ao gerar post: " . $e->getMessage());
            }
        }

        $this->info("Processo finalizado!");
    }
}

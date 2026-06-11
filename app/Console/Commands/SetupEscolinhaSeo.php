<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\SiteSetting;

class SetupEscolinhaSeo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:setup-seo {tenant_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura automaticamente todos os textos, SEO e conteúdo inicial do site da Escolinha Real Independente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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

        $this->info("Iniciando injeção de SEO e Copywriting para a Escolinha Real Independente...");

        // Configuração de SEO e Textos do Site focados em Conversão e Esporte
        $settings = [
            'site_name' => 'Escolinha Real Independente',
            'site_description' => 'A melhor escolinha de futebol da região. Focada no desenvolvimento técnico, tático e humano. Formando craques para o esporte e cidadãos para a vida.',
            'seo_keywords' => 'escolinha de futebol, treinamento de futebol, categorias de base, futebol infantil, academia de futebol, real independente, futebol para crianças',
            
            // Hero Section
            'hero_title' => 'Formando os Craques do Futuro com Excelência e Disciplina',
            'hero_subtitle' => 'Descubra a metodologia da Escolinha Real Independente. Foco em técnica, inteligência emocional e trabalho em equipe para crianças e adolescentes de todas as idades.',
            
            // About Section
            'about_title' => 'Muito Mais que Futebol',
            'about_text' => 'Na Escolinha Real Independente, nós acreditamos que o esporte é a maior ferramenta de transformação social. Com profissionais altamente qualificados, infraestrutura moderna e uma metodologia comprovada, desenvolvemos atletas completos que sabem vencer dentro e fora de campo.',
            
            // Methodology Section
            'methodology_title' => 'Nossa Metodologia Exclusiva',
            'methodology_subtitle' => 'Desenvolvimento Integral: Tática, Técnica, Físico e Mental',
            
            // Blog Context (Para IA)
            'blog_post_context' => 'Artigos sobre a evolução dos alunos na Escolinha Real Independente, dicas de nutrição esportiva, importância do apoio dos pais, bastidores dos campeonatos e detalhes da nossa metodologia de ensino.'
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value, 'text', 'Configuração SEO de Alta Conversão', true);
        }

        $this->info("Sucesso! Todos os textos e SEO do site foram atualizados e estão prontos para produção!");
    }
}

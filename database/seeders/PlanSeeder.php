<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfeito para clubes iniciantes com até 50 atletas',
                'price_monthly' => 99.00,
                'price_yearly' => 990.00,
                'max_athletes' => 50,
                'max_teams' => 5,
                'max_branches' => 1,
                'ai_features' => false,
                'custom_domain' => false,
                'priority_support' => false,
                'features' => [
                    'Dashboard administrativo',
                    'Gestão de atletas e equipes',
                    'Site público básico',
                    'Relatórios básicos',
                    'Suporte por email'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal para clubes em crescimento com recursos avançados',
                'price_monthly' => 199.00,
                'price_yearly' => 1990.00,
                'max_athletes' => 200,
                'max_teams' => 15,
                'max_branches' => 3,
                'ai_features' => true,
                'custom_domain' => true,
                'priority_support' => true,
                'features' => [
                    'Tudo do plano Starter',
                    'Inteligência Artificial',
                    'Planos de treino personalizados',
                    'Portal do atleta',
                    'Loja online',
                    'Integração com Asaas',
                    'Domínio personalizado',
                    'Suporte prioritário'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Para grandes clubes e academias com necessidades específicas',
                'price_monthly' => 399.00,
                'price_yearly' => 3990.00,
                'max_athletes' => null, // unlimited
                'max_teams' => null, // unlimited
                'max_branches' => null, // unlimited
                'ai_features' => true,
                'custom_domain' => true,
                'priority_support' => true,
                'features' => [
                    'Tudo do plano Professional',
                    'Atletas ilimitados',
                    'Equipes ilimitadas',
                    'Filiais ilimitadas',
                    'API personalizada',
                    'Integrações customizadas',
                    'Gerente de conta dedicado',
                    'Suporte 24/7'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }
    }
}
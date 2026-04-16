<?php

namespace App\Services;

use App\Models\AiGeneratedContent;
use App\Models\Tenant;
use App\Models\Athlete;
use App\Models\Plan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AIUsageService
{
    /**
     * Verifica se o atleta pode gerar mais planos de IA no mês atual.
     * 
     * @param Athlete $athlete
     * @param string $type Tipo do plano (workout_plan, meal_plan, recovery_plan)
     * @return bool
     */
    public function canGeneratePlan(Athlete $athlete, string $type = 'workout_plan'): bool
    {
        // Busca o tenant do atleta
        $tenant = $this->getTenantFromAthlete($athlete);
        
        if (!$tenant) {
            return false;
        }

        // Busca o plano do tenant
        $plan = $tenant->plan;
        
        if (!$plan || !$plan->ai_features) {
            return false;
        }

        // Verifica limite de gerações por mês
        $monthlyLimit = $this->getMonthlyLimit($plan);
        
        if ($monthlyLimit === null) {
            // Sem limite
            return true;
        }

        // Conta gerações do mês atual
        $currentMonthCount = $this->getMonthlyUsageCount($athlete, $type);

        return $currentMonthCount < $monthlyLimit;
    }

    /**
     * Registra o uso de IA.
     * 
     * @param Athlete $athlete
     * @param string $type Tipo do plano
     * @param int $tokens Tokens utilizados
     * @param float $cost Custo em dólares
     * @return void
     */
    public function recordUsage(Athlete $athlete, string $type, int $tokens, float $cost): void
    {
        try {
            // O registro já é feito no AIService ao criar AiGeneratedContent
            // Este método pode ser usado para logs adicionais ou métricas
            
            $tenant = $this->getTenantFromAthlete($athlete);
            
            if ($tenant) {
                // Atualiza cache de uso mensal
                $cacheKey = "ai_usage:tenant:{$tenant->id}:month:" . now()->format('Y-m');
                $currentUsage = Cache::get($cacheKey, 0);
                Cache::put($cacheKey, $currentUsage + 1, now()->endOfMonth());

                // Atualiza cache de custos
                $costCacheKey = "ai_costs:tenant:{$tenant->id}:month:" . now()->format('Y-m');
                $currentCosts = Cache::get($costCacheKey, 0);
                Cache::put($costCacheKey, $currentCosts + $cost, now()->endOfMonth());
            }

            Log::info('AIUsageService: Uso de IA registrado', [
                'athlete_id' => $athlete->id,
                'type' => $type,
                'tokens' => $tokens,
                'cost' => $cost,
            ]);

        } catch (\Exception $e) {
            Log::error('AIUsageService: Erro ao registrar uso', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Retorna o uso mensal de IA do atleta.
     * 
     * @param Athlete $athlete
     * @param string $type Tipo do plano (opcional)
     * @return int
     */
    public function getMonthlyUsageCount(Athlete $athlete, ?string $type = null): int
    {
        $query = AiGeneratedContent::where('athlete_id', $athlete->id)
            ->whereMonth('generated_at', now()->month)
            ->whereYear('generated_at', now()->year);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->count();
    }

    /**
     * Retorna o uso mensal de IA do tenant.
     * 
     * @param Tenant $tenant
     * @return array
     */
    public function getTenantUsage(Tenant $tenant): array
    {
        $cacheKey = "ai_usage:tenant:{$tenant->id}:month:" . now()->format('Y-m');
        
        return Cache::remember($cacheKey, 3600, function () use ($tenant) {
            // Busca todos os atletas do tenant
            $athletes = $this->getTenantAthletes($tenant);
            
            if ($athletes->isEmpty()) {
                return [
                    'count' => 0,
                    'costs' => 0,
                    'by_type' => [],
                ];
            }

            $athleteIds = $athletes->pluck('id')->toArray();

            // Conta gerações do mês
            $count = AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', now()->month)
                ->whereYear('generated_at', now()->year)
                ->count();

            // Soma custos do mês
            $costs = AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', now()->month)
                ->whereYear('generated_at', now()->year)
                ->sum('cost');

            // Por tipo
            $byType = AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', now()->month)
                ->whereYear('generated_at', now()->year)
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();

            return [
                'count' => $count,
                'costs' => (float) $costs,
                'by_type' => $byType,
            ];
        });
    }

    /**
     * Retorna os custos mensais de IA do tenant.
     * 
     * @param Tenant $tenant
     * @return float
     */
    public function getTenantCosts(Tenant $tenant): float
    {
        $cacheKey = "ai_costs:tenant:{$tenant->id}:month:" . now()->format('Y-m');
        
        return (float) Cache::remember($cacheKey, 3600, function () use ($tenant) {
            $athletes = $this->getTenantAthletes($tenant);
            
            if ($athletes->isEmpty()) {
                return 0;
            }

            $athleteIds = $athletes->pluck('id')->toArray();

            return AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', now()->month)
                ->whereYear('generated_at', now()->year)
                ->sum('cost');
        });
    }

    /**
     * Retorna o limite mensal de gerações baseado no plano.
     * 
     * @param Plan $plan
     * @return int|null Retorna null se não houver limite
     */
    public function getMonthlyLimit(Plan $plan): ?int
    {
        // Limites baseados no plano
        return match($plan->slug ?? $plan->name) {
            'starter', 'basico' => 10, // 10 gerações por mês
            'professional', 'pro' => 50, // 50 gerações por mês
            'enterprise' => null, // Sem limite
            default => 10, // Padrão: 10 gerações
        };
    }

    /**
     * Retorna relatório de uso de IA.
     * 
     * @param Tenant $tenant
     * @param int $months Número de meses para o relatório
     * @return array
     */
    public function getUsageReport(Tenant $tenant, int $months = 6): array
    {
        $athletes = $this->getTenantAthletes($tenant);
        
        if ($athletes->isEmpty()) {
            return [];
        }

        $athleteIds = $athletes->pluck('id')->toArray();

        $report = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $count = AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', $date->month)
                ->whereYear('generated_at', $date->year)
                ->count();

            $costs = AiGeneratedContent::whereIn('athlete_id', $athleteIds)
                ->whereMonth('generated_at', $date->month)
                ->whereYear('generated_at', $date->year)
                ->sum('cost');

            $report[] = [
                'month' => $month,
                'month_name' => $date->format('F Y'),
                'count' => $count,
                'costs' => (float) $costs,
            ];
        }

        return $report;
    }

    /**
     * Busca o tenant do atleta.
     * 
     * @param Athlete $athlete
     * @return Tenant|null
     */
    private function getTenantFromAthlete(Athlete $athlete): ?Tenant
    {
        // Em um sistema multi-tenant, o tenant atual já está no contexto
        // Mas podemos buscar pelo domínio atual se necessário
        try {
            $currentTenant = tenancy()->initialized ? tenancy()->tenant : null;
            return $currentTenant;
        } catch (\Exception $e) {
            Log::warning('AIUsageService: Erro ao buscar tenant', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Busca todos os atletas do tenant.
     * 
     * @param Tenant $tenant
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTenantAthletes(Tenant $tenant): \Illuminate\Database\Eloquent\Collection
    {
        try {
            tenancy()->initialize($tenant);
            $athletes = \App\Models\Athlete::all();
            tenancy()->end();
            return $athletes;
        } catch (\Exception $e) {
            Log::error('AIUsageService: Erro ao buscar atletas do tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return collect([]);
        }
    }
}


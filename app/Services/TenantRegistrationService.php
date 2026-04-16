<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TenantRegistrationService
{
    /**
     * Armazena dados temporários do registro em cache aguardando confirmação de pagamento.
     * 
     * @param string $sessionId ID único da sessão/registro
     * @param array $data Dados do registro (club_name, subdomain, admin_data, plan_id, etc.)
     * @param int $ttl Tempo de vida em minutos (padrão: 30 minutos)
     * @return bool
     */
    public function storeRegistrationData(string $sessionId, array $data, int $ttl = 30): bool
    {
        try {
            Cache::put("tenant_registration:{$sessionId}", $data, now()->addMinutes($ttl));
            
            Log::info('TenantRegistrationService: Dados de registro armazenados em cache', [
                'session_id' => $sessionId,
                'ttl_minutes' => $ttl,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('TenantRegistrationService: Erro ao armazenar dados em cache', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Recupera dados temporários do registro do cache.
     * 
     * @param string $sessionId ID único da sessão/registro
     * @return array|null
     */
    public function getRegistrationData(string $sessionId): ?array
    {
        try {
            $data = Cache::get("tenant_registration:{$sessionId}");
            
            if ($data) {
                Log::info('TenantRegistrationService: Dados de registro recuperados do cache', [
                    'session_id' => $sessionId,
                ]);
            }
            
            return $data;
        } catch (\Exception $e) {
            Log::error('TenantRegistrationService: Erro ao recuperar dados do cache', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Remove dados temporários do registro do cache.
     * 
     * @param string $sessionId ID único da sessão/registro
     * @return bool
     */
    public function clearRegistrationData(string $sessionId): bool
    {
        try {
            Cache::forget("tenant_registration:{$sessionId}");
            
            Log::info('TenantRegistrationService: Dados de registro removidos do cache', [
                'session_id' => $sessionId,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('TenantRegistrationService: Erro ao remover dados do cache', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Gera um ID único para a sessão de registro.
     * 
     * @return string
     */
    public function generateSessionId(): string
    {
        return 'reg_' . uniqid() . '_' . time();
    }

    /**
     * Verifica se os dados de registro ainda são válidos (não expiraram).
     * 
     * @param string $sessionId ID único da sessão/registro
     * @return bool
     */
    public function isRegistrationDataValid(string $sessionId): bool
    {
        return Cache::has("tenant_registration:{$sessionId}");
    }
}


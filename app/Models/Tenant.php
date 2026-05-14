<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains;

    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'email',
        'domain',
        'database_name',
        'asaas_customer_id',
        'asaas_subscription_id',
        'plan_id',
        'status',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
        'data',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    /**
     * Define custom columns that should not be stored in the 'data' JSON blob.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'domain',
            'plan_id',
            'status',
            'is_active',
            'trial_ends_at',
            'subscription_ends_at',
            'asaas_customer_id',
            'asaas_subscription_id',
        ];
    }

    /**
     * Get the plan that belongs to the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the domains for the tenant.
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Check if the tenant is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    /**
     * Check if the tenant is on trial.
     */
    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Get the tenant's primary domain.
     */
    public function getPrimaryDomainAttribute()
    {
        return $this->domains()->where('is_primary', true)->first();
    }

    /**
     * Get the tenant's custom domain.
     */
    public function getCustomDomainAttribute()
    {
        return $this->domains()->where('is_primary', false)->first();
    }

    /**
     * Get the tenant's database name.
     */
    public function getDatabaseNameAttribute()
    {
        return 'tenant_' . $this->id;
    }

    /**
     * Get the tenant's subdomain.
     */
    public function getSubdomainAttribute()
    {
        return $this->domain;
    }


    /**
     * Get the tenant's URL.
     */
    public function getUrlAttribute()
    {
        // Se estiver em CLI, usa o primeiro domínio configurado ou o campo domain
        if (app()->runningInConsole()) {
            $centralDomain = config('tenancy.central_domains')[0] ?? 'meuclube.app';
            return 'http://' . $this->domain . '.' . $centralDomain;
        }

        // Tenta pegar o domínio primário da tabela de domínios
        $primaryDomain = $this->domains()->where('is_primary', true)->first();
        
        if ($primaryDomain) {
            $domain = $primaryDomain->domain;
        } else {
            // Fallback dinâmico: usa o host atual para detectar o domínio base
            $currentHost = request()->getHost();
            $centralDomains = config('tenancy.central_domains', []);
            $baseDomain = $centralDomains[0] ?? 'meuclube.app';
            
            foreach ($centralDomains as $central) {
                // Se o host atual termina com o domínio central (ex: admin.nexts.test termina com nexts.test)
                if ($currentHost === $central || str_ends_with($currentHost, '.' . $central)) {
                    $baseDomain = $central;
                    break;
                }
            }
            
            // Se o host atual não é um domínio central, usa o host atual como base se não for um subdomínio
            // Isso ajuda em ambientes de dev como valet/laragon
            if ($baseDomain === 'meuclube.app' && !in_array($currentHost, $centralDomains)) {
                $baseDomain = $currentHost;
            }

            $domain = $this->domain . '.' . $baseDomain;
        }
        
        $scheme = request()->isSecure() ? 'https://' : 'http://';
        $port = request()->getPort();
        $url = $scheme . $domain;
        
        if ($port && !in_array($port, [80, 443])) {
            $url .= ':' . $port;
        }
        
        return $url;
    }
}

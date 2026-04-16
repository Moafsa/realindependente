<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant
{
    use HasFactory, HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'subdomain',
        'database_name',
        'asaas_customer_id',
        'asaas_subscription_id',
        'plan_id',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'data',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

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
     * Get the tenant's URL.
     */
    public function getUrlAttribute()
    {
        $primaryDomain = $this->primary_domain;
        if ($primaryDomain) {
            return 'http://' . $primaryDomain->domain;
        }
        
        return 'http://' . $this->subdomain . '.' . config('tenancy.central_domains')[0];
    }
}

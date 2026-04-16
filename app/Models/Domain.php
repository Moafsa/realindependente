<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'tenant_id',
        'is_primary',
        'is_verified',
        'verification_token',
        'ssl_certificate',
        'ssl_expires_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'ssl_expires_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the domain.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the domain's URL.
     */
    public function getUrlAttribute()
    {
        $protocol = $this->ssl_certificate ? 'https' : 'http';
        return $protocol . '://' . $this->domain;
    }

    /**
     * Check if the domain is secure (HTTPS).
     */
    public function isSecure()
    {
        return !empty($this->ssl_certificate);
    }

    /**
     * Check if the SSL certificate is expired.
     */
    public function isSslExpired()
    {
        return $this->ssl_expires_at && $this->ssl_expires_at->isPast();
    }

    /**
     * Get the domain's status.
     */
    public function getStatusAttribute()
    {
        if (!$this->is_verified) {
            return 'pending_verification';
        }

        if ($this->is_primary && $this->isSslExpired()) {
            return 'ssl_expired';
        }

        return 'active';
    }

    /**
     * Scope to get only primary domains.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to get only verified domains.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}

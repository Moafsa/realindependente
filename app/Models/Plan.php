<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $fillable = [
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'features',
        'max_athletes',
        'max_branches',
        'ai_features',
        'ecommerce_tax_rate',
        'admin_fee_percentage',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'ai_features' => 'boolean',
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'ecommerce_tax_rate' => 'decimal:2',
        'admin_fee_percentage' => 'decimal:2',
    ];

    /**
     * Get the tenants that belong to this plan.
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get the plan's features as a formatted list.
     */
    public function getFeaturesListAttribute()
    {
        return $this->features ?? [];
    }

    /**
     * Check if the plan has a specific feature.
     */
    public function hasFeature($feature)
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Get the plan's yearly discount.
     */
    public function getYearlyDiscountAttribute()
    {
        if (!$this->price_yearly || !$this->price_monthly) {
            return 0;
        }

        $yearlyPrice = $this->price_yearly;
        $monthlyPrice = $this->price_monthly * 12;
        
        return round((($monthlyPrice - $yearlyPrice) / $monthlyPrice) * 100);
    }

    /**
     * Get the plan's monthly price with discount.
     */
    public function getMonthlyPriceWithDiscountAttribute()
    {
        if ($this->price_yearly) {
            return round($this->price_yearly / 12, 2);
        }
        
        return $this->price_monthly;
    }

    /**
     * Scope to get only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get plans ordered by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price_monthly');
    }
}

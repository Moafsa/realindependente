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
        'price_quarterly',
        'price_semiannual',
        'price_yearly',
        'discount_quarterly',
        'discount_semiannual',
        'discount_yearly',
        'features',
        'max_athletes',
        'max_branches',
        'ai_features',
        'ecommerce_tax_rate',
        'admin_fee_percentage',
        'trial_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'ai_features' => 'boolean',
        'is_active' => 'boolean',
        'price_monthly' => 'decimal:2',
        'price_quarterly' => 'decimal:2',
        'price_semiannual' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'discount_quarterly' => 'integer',
        'discount_semiannual' => 'integer',
        'discount_yearly' => 'integer',
        'ecommerce_tax_rate' => 'decimal:2',
        'admin_fee_percentage' => 'decimal:2',
        'trial_days' => 'integer',
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

    /**
     * Calculate the price for a specific frequency.
     */
    public function getCalculatedPrice($frequency)
    {
        $months = match($frequency) {
            'monthly' => 1,
            'quarterly' => 3,
            'semiannual' => 6,
            'yearly' => 12,
            default => 1
        };

        // If an explicit price is set for the frequency, use it
        $explicitPrice = match($frequency) {
            'monthly' => $this->price_monthly,
            'quarterly' => $this->price_quarterly,
            'semiannual' => $this->price_semiannual,
            'yearly' => $this->price_yearly,
            default => null
        };

        if ($explicitPrice && $explicitPrice > 0) {
            return (float) $explicitPrice;
        }

        // Otherwise calculate based on monthly price and discount
        $basePrice = (float) $this->price_monthly * $months;
        $discount = match($frequency) {
            'quarterly' => (int) $this->discount_quarterly,
            'semiannual' => (int) $this->discount_semiannual,
            'yearly' => (int) $this->discount_yearly,
            default => 0
        };

        if ($discount > 0) {
            return round($basePrice * (1 - ($discount / 100)), 2);
        }

        return round($basePrice, 2);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
        });
    }

    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'sku',
        'image',
        'stock_quantity',
        'is_active',
        'is_featured',
        'attributes',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'attributes' => 'array',
        'weight' => 'decimal:2',
    ];

    /**
     * Get the order items for this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the product's image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return route('tenant.assets', ['path' => $this->image]);
        }
        
        return 'https://via.placeholder.com/300x300?text=' . urlencode($this->name);
    }

    /**
     * Get the product's formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    /**
     * Get the product's stock status.
     */
    public function getStockStatusAttribute()
    {
        if ($this->type === 'subscription' || $this->type === 'service') {
            return 'unlimited';
        }
        
        if ($this->stock_quantity === null) {
            return 'unlimited';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->stock_quantity <= 5) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    /**
     * Get the product's availability.
     */
    public function getIsAvailableAttribute()
    {
        return $this->is_active && $this->stock_status !== 'out_of_stock';
    }

    /**
     * Get the product's total sales.
     */
    public function getTotalSalesAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * Get the product's total revenue.
     */
    public function getTotalRevenueAttribute()
    {
        return $this->orderItems()->sum('total');
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get products by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('stock_quantity')
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope to search products by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
    }
}

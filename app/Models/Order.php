<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'athlete_id',
        'total_amount',
        'status',
        'asaas_payment_id',
        'asaas_payment_url',
        'billing_address',
        'shipping_address',
        'notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user that placed this order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the athlete associated with this order.
     */
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the order items for this order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the order's formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return 'R$ ' . number_format($this->total_amount, 2, ',', '.');
    }

    /**
     * Get the order's status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pendente',
            'paid' => 'Pago',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
            default => 'Desconhecido'
        };
    }

    /**
     * Get the order's status color.
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'paid' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the order's items count.
     */
    public function getItemsCountAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * Get the order's products.
     */
    public function getProductsAttribute()
    {
        return $this->orderItems()->with('product')->get();
    }

    /**
     * Check if the order is paid.
     */
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid' || $this->status === 'shipped' || $this->status === 'delivered';
    }

    /**
     * Check if the order can be cancelled.
     */
    public function getCanBeCancelledAttribute()
    {
        return $this->status === 'pending' || $this->status === 'paid';
    }

    /**
     * Scope to get orders by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get orders by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get orders by athlete.
     */
    public function scopeByAthlete($query, $athleteId)
    {
        return $query->where('athlete_id', $athleteId);
    }

    /**
     * Scope to get paid orders.
     */
    public function scopePaid($query)
    {
        return $query->whereIn('status', ['paid', 'shipped', 'delivered']);
    }

    /**
     * Scope to get pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get orders by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

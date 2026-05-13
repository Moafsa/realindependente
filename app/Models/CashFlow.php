<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashFlow extends Model
{
    use HasFactory;

    protected $table = 'cash_flow';

    protected $fillable = [
        'description',
        'amount',
        'type',
        'date',
        'category',
        'status',
        'notes',
        'created_by',
        'recipient_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who created the transaction.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the recipient of the payment.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope for entries.
     */
    public function scopeEntries($query)
    {
        return $query->where('type', 'entry');
    }

    /**
     * Scope for exits.
     */
    public function scopeExits($query)
    {
        return $query->where('type', 'exit');
    }
}

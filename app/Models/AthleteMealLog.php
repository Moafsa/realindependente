<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AthleteMealLog extends Model
{
    protected $fillable = [
        'athlete_id',
        'photo_path',
        'ai_analysis',
        'calories',
        'proteins',
        'carbs',
        'fats',
        'notes',
        'consumed_at',
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'consumed_at' => 'datetime',
        'calories' => 'decimal:2',
        'proteins' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
    ];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }
}

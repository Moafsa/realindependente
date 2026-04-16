<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGeneratedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'type',
        'content',
        'prompt',
        'model_used',
        'tokens_used',
        'cost',
        'is_favorite',
        'generated_at',
    ];

    protected $casts = [
        'content' => 'array',
        'is_favorite' => 'boolean',
        'generated_at' => 'datetime',
        'cost' => 'decimal:4',
    ];

    /**
     * Get the athlete that this AI content belongs to.
     */
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the content title.
     */
    public function getTitleAttribute()
    {
        return $this->content['title'] ?? 'Plano ' . ucfirst($this->type);
    }

    /**
     * Get the content summary.
     */
    public function getSummaryAttribute()
    {
        return $this->content['summary'] ?? substr($this->content['description'] ?? '', 0, 100) . '...';
    }

    /**
     * Get the content duration (for workout plans).
     */
    public function getDurationAttribute()
    {
        return $this->content['duration'] ?? null;
    }

    /**
     * Get the content difficulty level.
     */
    public function getDifficultyAttribute()
    {
        return $this->content['difficulty'] ?? null;
    }

    /**
     * Get the content calories (for meal plans).
     */
    public function getCaloriesAttribute()
    {
        return $this->content['calories'] ?? null;
    }

    /**
     * Get the content formatted for display.
     */
    public function getFormattedContentAttribute()
    {
        $content = $this->content;
        
        if ($this->type === 'workout_plan') {
            return $this->formatWorkoutPlan($content);
        } elseif ($this->type === 'meal_plan') {
            return $this->formatMealPlan($content);
        }
        
        return $content;
    }

    /**
     * Format workout plan content.
     */
    private function formatWorkoutPlan($content)
    {
        $formatted = [
            'title' => $content['title'] ?? 'Plano de Treino',
            'description' => $content['description'] ?? '',
            'duration' => $content['duration'] ?? '30 minutos',
            'difficulty' => $content['difficulty'] ?? 'Intermediário',
            'exercises' => $content['exercises'] ?? [],
            'tips' => $content['tips'] ?? [],
        ];
        
        return $formatted;
    }

    /**
     * Format meal plan content.
     */
    private function formatMealPlan($content)
    {
        $formatted = [
            'title' => $content['title'] ?? 'Plano Nutricional',
            'description' => $content['description'] ?? '',
            'calories' => $content['calories'] ?? 2000,
            'meals' => $content['meals'] ?? [],
            'tips' => $content['tips'] ?? [],
        ];
        
        return $formatted;
    }

    /**
     * Scope to get content by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get content by athlete.
     */
    public function scopeByAthlete($query, $athleteId)
    {
        return $query->where('athlete_id', $athleteId);
    }

    /**
     * Scope to get favorite content.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to get latest content.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('generated_at', 'desc');
    }
}

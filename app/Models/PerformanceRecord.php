<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'metric',
        'value',
        'notes',
        'recorded_by',
        'recorded_at',
        'tenant_id',
        'tenant_name',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the athlete that this performance record belongs to.
     */
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the user who recorded this performance.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Get the performance trend for this metric.
     */
    public function getTrendAttribute()
    {
        $previousRecord = $this->athlete->performanceRecords()
            ->where('metric', $this->metric)
            ->where('recorded_at', '<', $this->recorded_at)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$previousRecord) {
            return 'new';
        }

        if ($this->value > $previousRecord->value) {
            return 'improving';
        } elseif ($this->value < $previousRecord->value) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Get the performance change percentage.
     */
    public function getChangePercentageAttribute()
    {
        $previousRecord = $this->athlete->performanceRecords()
            ->where('metric', $this->metric)
            ->where('recorded_at', '<', $this->recorded_at)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$previousRecord) {
            return 0;
        }

        $change = (($this->value - $previousRecord->value) / $previousRecord->value) * 100;
        return round($change, 2);
    }

    /**
     * Scope to get records by metric.
     */
    public function scopeByMetric($query, $metric)
    {
        return $query->where('metric', $metric);
    }

    /**
     * Scope to get records by athlete.
     */
    public function scopeByAthlete($query, $athleteId)
    {
        return $query->where('athlete_id', $athleteId);
    }

    /**
     * Scope to get records by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get latest records.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }
}

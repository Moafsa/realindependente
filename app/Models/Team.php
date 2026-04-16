<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'coach_user_id',
        'color_primary',
        'color_secondary',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the coach that manages this team.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_user_id');
    }

    /**
     * Get the athletes that belong to this team.
     */
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    /**
     * Get the team's active athletes.
     */
    public function activeAthletes()
    {
        return $this->athletes()->where('is_active', true);
    }

    /**
     * Get the team's performance records.
     */
    public function performanceRecords()
    {
        return $this->hasManyThrough(PerformanceRecord::class, Athlete::class);
    }

    /**
     * Get the team's logo URL.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=' . ltrim($this->color_primary, '#') . '&background=' . ltrim($this->color_secondary, '#');
    }

    /**
     * Get the team's athlete count.
     */
    public function getAthleteCountAttribute()
    {
        return $this->athletes()->count();
    }

    /**
     * Get the team's active athlete count.
     */
    public function getActiveAthleteCountAttribute()
    {
        return $this->activeAthletes()->count();
    }

    /**
     * Scope to get only active teams.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get teams by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to search teams by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}

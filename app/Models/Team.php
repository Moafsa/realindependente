<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = \Illuminate\Support\Str::slug($team->name) . '-' . \Illuminate\Support\Str::random(5);
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'category',
        'level',
        'description',
        'coach_id',
        'branch_id',
        'primary_color',
        'secondary_color',
        'logo',
        'schedule',
        'competitions',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the coach that manages this team.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
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
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->logo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=' . ltrim($this->primary_color ?? 'FFFFFF', '#') . '&background=' . ltrim($this->secondary_color ?? '000000', '#');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Athlete extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'birth_date',
        'position',
        'profile_picture_url',
        'bio',
        'guardian_name',
        'guardian_contact',
        'guardian_email',
        'team_id',
        'branch_id',
        'user_id',
        'is_active',
        'jersey_number',
        'height',
        'weight',
        'emergency_contact',
        'medical_conditions',
        'allergies',
        'insurance_info',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'medical_conditions' => 'array',
        'allergies' => 'array',
    ];

    /**
     * Get the team that the athlete belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the branch that the athlete belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user account associated with this athlete.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the athlete's performance records.
     */
    public function performanceRecords()
    {
        return $this->hasMany(PerformanceRecord::class);
    }

    /**
     * Get the athlete's AI generated content.
     */
    public function aiGeneratedContent()
    {
        return $this->hasMany(AiGeneratedContent::class);
    }

    /**
     * Get the athlete's orders.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'athlete_id');
    }

    /**
     * Get the athlete's age.
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Get the athlete's full name with team.
     */
    public function getFullNameWithTeamAttribute()
    {
        $teamName = $this->team ? $this->team->name : 'No Team';
        return $this->full_name . ' (' . $teamName . ')';
    }

    /**
     * Get the athlete's profile picture URL.
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->attributes['profile_picture_url']) {
            return asset('storage/' . $this->attributes['profile_picture_url']);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the athlete's latest performance record.
     */
    public function getLatestPerformanceAttribute()
    {
        return $this->performanceRecords()->latest()->first();
    }

    /**
     * Get the athlete's performance trend.
     */
    public function getPerformanceTrendAttribute()
    {
        $records = $this->performanceRecords()->latest()->take(5)->get();
        
        if ($records->count() < 2) {
            return 'insufficient_data';
        }

        $first = $records->last();
        $last = $records->first();
        
        if ($last->value > $first->value) {
            return 'improving';
        } elseif ($last->value < $first->value) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Scope to get only active athletes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get athletes by team.
     */
    public function scopeByTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope to get athletes by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to get athletes by position.
     */
    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope to search athletes by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('full_name', 'like', '%' . $search . '%');
    }
}

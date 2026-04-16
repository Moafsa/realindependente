<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Spatie\Permission\Traits\HasRoles; // Temporariamente desabilitado

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // HasRoles temporariamente removido

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'athlete_id',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the athlete associated with this user.
     */
    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    /**
     * Get the athlete's performance records.
     */
    public function performanceRecords()
    {
        return $this->hasManyThrough(PerformanceRecord::class, Athlete::class);
    }

    /**
     * Get the user's AI generated content.
     */
    public function aiGeneratedContent()
    {
        return $this->hasManyThrough(AiGeneratedContent::class, Athlete::class);
    }

    /**
     * Check if the user is an athlete.
     */
    public function isAthlete()
    {
        return $this->role === 'athlete';
    }

    /**
     * Check if the user is a guardian.
     */
    public function isGuardian()
    {
        return $this->role === 'guardian';
    }

    /**
     * Check if the user is a coach.
     */
    public function isCoach()
    {
        return $this->role === 'coach';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's full name with role.
     */
    public function getFullNameWithRoleAttribute()
    {
        return $this->name . ' (' . ucfirst($this->role) . ')';
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}

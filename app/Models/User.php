<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Spatie\Permission\Traits\HasRoles; // Temporariamente desabilitado

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_super_admin',
        'athlete_id',
        'phone',
        'avatar',
        'is_active',
        'last_login_at',
        'salary',
        'payment_frequency',
        'bio',
        'education',
        'experience',
        'certificates',
        'specialties',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
        'salary' => 'decimal:2',
        'certificates' => 'array',
    ];

    /**
     * Get the athlete associated with this user.
     */
    public function athlete()
    {
        return $this->hasOne(Athlete::class);
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
     * Get the teams managed by this coach.
     */
    public function teams()
    {
        return $this->hasMany(Team::class, 'coach_id');
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
     * Check if the user is a super admin.
     */
    public function isSuperAdmin()
    {
        return $this->is_super_admin === true;
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
            // Se o avatar começa com http, retorna direto
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            return route('tenant.assets', ['path' => $this->avatar]);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=6366F1&bold=true&format=svg';
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

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
        'document',
        'phone',
        'address',
        'birth_date',
        'subcategory',
        'position',
        'positions',
        'profile_picture_url',
        'bio',
        'guardian_name',
        'guardian_contact',
        'guardian_email',
        'guardian_document',
        'team_id',
        'branch_id',
        'user_id',
        'is_active',
        'gender',
        'jersey_number',
        'height',
        'weight',
        'emergency_contact',
        'medical_conditions',
        'allergies',
        'insurance_info',
        'terms_accepted',
        'insurance_accepted',
        'is_verified',
        'profile_completion',
        'medical_certificate_path',
        'athlete_document_path',
        'residence_proof_path',
        'guardian_document_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($athlete) {
            $athlete->profile_completion = $athlete->getProfileCompletionPercentage();
        });
    }

    /**
     * Get the medical certificate URL.
     */
    public function getMedicalCertificateUrlAttribute()
    {
        if (!$this->medical_certificate_path) return null;
        
        // Se já for uma URL absoluta, retorna ela mesma
        if (filter_var($this->medical_certificate_path, FILTER_VALIDATE_URL)) {
            return $this->medical_certificate_path;
        }

        return route('tenant.assets', ['path' => $this->medical_certificate_path]);
    }

    /**
     * Get the athlete document URL.
     */
    public function getAthleteDocumentUrlAttribute()
    {
        if (!$this->athlete_document_path) return null;

        if (filter_var($this->athlete_document_path, FILTER_VALIDATE_URL)) {
            return $this->athlete_document_path;
        }

        return route('tenant.assets', ['path' => $this->athlete_document_path]);
    }

    /**
     * Get the residence proof URL.
     */
    public function getResidenceProofUrlAttribute()
    {
        if (!$this->residence_proof_path) return null;

        if (filter_var($this->residence_proof_path, FILTER_VALIDATE_URL)) {
            return $this->residence_proof_path;
        }

        return route('tenant.assets', ['path' => $this->residence_proof_path]);
    }

    /**
     * Get the guardian document URL.
     */
    public function getGuardianDocumentUrlAttribute()
    {
        if (!$this->guardian_document_path) return null;

        if (filter_var($this->guardian_document_path, FILTER_VALIDATE_URL)) {
            return $this->guardian_document_path;
        }

        return route('tenant.assets', ['path' => $this->guardian_document_path]);
    }

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'medical_conditions' => 'array',
        'allergies' => 'array',
        'positions' => 'array',
        'terms_accepted' => 'boolean',
        'insurance_accepted' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Calcula a subcategoria CBF baseada no ano de nascimento.
     */
    public static function calculateSubcategory($birthDate)
    {
        if (!$birthDate) return null;
        
        $birthYear = \Carbon\Carbon::parse($birthDate)->year;
        $currentYear = now()->year;
        $ageAtEndOfYear = $currentYear - $birthYear;

        if ($ageAtEndOfYear <= 7) return 'Sub-7';
        if ($ageAtEndOfYear <= 9) return 'Sub-9';
        if ($ageAtEndOfYear <= 11) return 'Sub-11';
        if ($ageAtEndOfYear <= 13) return 'Sub-13';
        if ($ageAtEndOfYear <= 15) return 'Sub-15';
        if ($ageAtEndOfYear <= 17) return 'Sub-17';
        if ($ageAtEndOfYear <= 20) return 'Sub-20';
        
        return 'Profissional';
    }

    /**
     * Atualiza a subcategoria de todos os atletas baseado na data de nascimento atual.
     */
    public static function updateAllSubcategories()
    {
        self::chunk(100, function ($athletes) {
            foreach ($athletes as $athlete) {
                if (!$athlete->birth_date) continue;
                $newSub = self::calculateSubcategory($athlete->birth_date);
                if ($athlete->subcategory !== $newSub) {
                    $athlete->update(['subcategory' => $newSub]);
                }
            }
        });
    }

    /**
     * Calcula o percentual de conclusão do perfil.
     */
    public function getProfileCompletionPercentage()
    {
        $fields = [
            'document', 'phone', 'address', 'birth_date', 'height', 'weight',
            'medical_conditions', 'allergies', 'guardian_name', 'guardian_document',
            'positions', 'profile_picture_url'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) $completed++;
        }

        return round(($completed / count($fields)) * 100);
    }

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
        $value = $this->attributes['profile_picture_url'] ?? null;
        
        if (!$value) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
        }

        // Se já for uma URL absoluta, retorna ela mesma
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        // Se for um path relativo, gera a URL via nossa rota de assets
        return route('tenant.assets', ['path' => $value]);
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

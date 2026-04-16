<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact_info',
        'phone',
        'email',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the athletes that belong to this branch.
     */
    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    /**
     * Get the branch's active athletes.
     */
    public function activeAthletes()
    {
        return $this->athletes()->where('is_active', true);
    }

    /**
     * Get the branch's teams through athletes.
     */
    public function teams()
    {
        return $this->hasManyThrough(Team::class, Athlete::class);
    }

    /**
     * Get the branch's full address.
     */
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->contact_info['city'] ?? '';
    }

    /**
     * Get the branch's Google Maps URL.
     */
    public function getGoogleMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return 'https://www.google.com/maps?q=' . $this->latitude . ',' . $this->longitude;
        }
        
        return 'https://www.google.com/maps/search/' . urlencode($this->address);
    }

    /**
     * Get the branch's athlete count.
     */
    public function getAthleteCountAttribute()
    {
        return $this->athletes()->count();
    }

    /**
     * Get the branch's active athlete count.
     */
    public function getActiveAthleteCountAttribute()
    {
        return $this->activeAthletes()->count();
    }

    /**
     * Scope to get only active branches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search branches by name.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }
}

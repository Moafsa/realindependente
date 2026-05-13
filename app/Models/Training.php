<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'type',
        'title',
        'description',
        'date',
        'time',
        'location',
        'latitude',
        'longitude',
        'address',
        'status',
    ];

    /**
     * Get the team that owns the training.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'athlete_id',
        'club_name',
        'club_logo_url',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}

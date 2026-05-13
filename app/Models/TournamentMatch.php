<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    protected $fillable = [
        'tournament_id',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'match_date',
        'match_time',
        'location',
        'status',
    ];

    protected $casts = [
        'match_date' => 'date',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
}

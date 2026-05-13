<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'start_date',
        'end_date',
        'format',
        'status',
    ];

    /**
     * Get the matches for the tournament.
     */
    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }
}

<?php

namespace App\Events;

use App\Models\TournamentMatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchScheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TournamentMatch $match;

    public function __construct(TournamentMatch $match)
    {
        $this->match = $match;
    }
}

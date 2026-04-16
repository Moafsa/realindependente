<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingScheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $trainingData;
    public array $athletes;

    /**
     * Create a new event instance.
     * 
     * @param array $trainingData Dados do treino (date, time, location, team_name, etc.)
     * @param array $athletes Array de atletas que devem receber o lembrete
     */
    public function __construct(array $trainingData, array $athletes)
    {
        $this->trainingData = $trainingData;
        $this->athletes = $athletes;
    }
}


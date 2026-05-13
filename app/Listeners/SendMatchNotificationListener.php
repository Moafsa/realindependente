<?php

namespace App\Listeners;

use App\Events\MatchScheduled;
use App\Services\WuzapiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendMatchNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected WuzapiService $wuzapiService;

    public function __construct(WuzapiService $wuzapiService)
    {
        $this->wuzapiService = $wuzapiService;
    }

    /**
     * Notifica os atletas das duas equipes sobre o jogo agendado via WhatsApp.
     */
    public function handle(MatchScheduled $event): void
    {
        $match = $event->match;

        try {
            $match->load(['homeTeam.athletes', 'awayTeam.athletes', 'tournament']);

            $homeTeam = $match->homeTeam;
            $awayTeam = $match->awayTeam;
            $tournament = $match->tournament;

            if (!$homeTeam || !$awayTeam) {
                Log::warning('SendMatchNotificationListener: Equipes não encontradas para a partida', [
                    'match_id' => $match->id,
                ]);
                return;
            }

            $gameData = [
                'tournament_name' => $tournament?->name ?? 'Torneio',
                'home_team'       => $homeTeam->name,
                'away_team'       => $awayTeam->name,
                'date'            => $match->match_date,
                'time'            => $match->match_time ?? '10:00',
                'location'        => $match->location ?? 'Local a confirmar',
            ];

            // Notifica atletas de ambas as equipes
            $allAthletes = $homeTeam->athletes->merge($awayTeam->athletes);

            foreach ($allAthletes as $athlete) {
                $phone = $athlete->guardian_contact ?? null;
                if (empty($phone)) {
                    continue;
                }

                $athleteGameData = array_merge($gameData, [
                    'athlete_name' => $athlete->full_name,
                    'team_name'    => $athlete->team_id === $homeTeam->id ? $homeTeam->name : $awayTeam->name,
                    'opponent'     => $athlete->team_id === $homeTeam->id ? $awayTeam->name : $homeTeam->name,
                ]);

                $this->wuzapiService->sendGameReminder($athleteGameData, $phone);
            }

            Log::info('SendMatchNotificationListener: Notificações de jogo enviadas', [
                'match_id'   => $match->id,
                'home_team'  => $homeTeam->name,
                'away_team'  => $awayTeam->name,
                'recipients' => $allAthletes->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('SendMatchNotificationListener: Falha ao enviar notificações', [
                'match_id' => $match->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}

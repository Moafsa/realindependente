<?php

namespace App\Http\Controllers;

use App\Events\MatchScheduled;
use App\Models\Tournament;
use App\Models\Team;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TournamentMatchController extends Controller
{
    /**
     * Generate matches for a tournament based on active teams.
     */
    public function generate(Request $request, Tournament $tournament)
    {
        // Limpar partidas existentes se já houver
        $tournament->matches()->delete();

        // Buscar times ativos
        $teams = Team::where('is_active', true)->get()->pluck('id')->toArray();
        $teamCount = count($teams);

        if ($teamCount < 2) {
            return redirect()->back()->with('error', 'É necessário pelo menos 2 times ativos para gerar partidas.');
        }

        // Se o número de times for ímpar, adicionamos um "bye" (folga)
        if ($teamCount % 2 != 0) {
            $teams[] = null; // null representa folga
            $teamCount++;
        }

        $rounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;
        $startDate = $tournament->start_date ? Carbon::parse($tournament->start_date) : now();

        for ($round = 0; $round < $rounds; $round++) {
            $matchDate = $startDate->copy()->addWeeks($round);

            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = $teams[$match];
                $away = $teams[$teamCount - 1 - $match];

                // Só cria a partida se nenhum dos times for null (folga)
                if ($home !== null && $away !== null) {
                    $match = TournamentMatch::create([
                        'tournament_id' => $tournament->id,
                        'home_team_id'  => $home,
                        'away_team_id'  => $away,
                        'match_date'    => $matchDate->format('Y-m-d'),
                        'match_time'    => '10:00:00',
                        'status'        => 'scheduled',
                    ]);

                    // Dispara notificações WhatsApp para os atletas das equipes
                    event(new MatchScheduled($match));
                }
            }

            // Rotacionar times (mantendo o primeiro fixo)
            $firstTeam = array_shift($teams);
            $lastTeam = array_pop($teams);
            array_unshift($teams, $lastTeam);
            array_unshift($teams, $firstTeam);
        }

        $tournament->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Tabela de jogos gerada com sucesso para ' . $tournament->name);
    }

    /**
     * Update the score of a match.
     */
    public function updateScore(Request $request, TournamentMatch $match)
    {
        $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
        ]);

        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'status' => 'completed'
        ]);

        return redirect()->back()->with('success', 'Placar atualizado com sucesso!');
    }

    /**
     * Update the status of a match.
     */
    public function updateStatus(Request $request, TournamentMatch $match)
    {
        $request->validate([
            'status' => 'required|string|in:scheduled,ongoing,completed,cancelled',
        ]);

        $match->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status da partida atualizado!');
    }
}

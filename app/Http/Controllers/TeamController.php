<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(Request $request)
    {
        try {
            $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
            
            $query = Team::withCount('athletes')->with('coach');

            $user = auth()->user();
            if ($user->role === 'coach') {
                $query->where('coach_id', $user->id);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            $teams = $query->orderBy('name')->get();
        } catch (\Exception $e) {
            $categories = [];
            $teams = collect([]);
        }

        return view('teams.index', compact('teams', 'categories'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        if (auth()->user()->role === 'coach') {
            abort(403, 'Acesso negado. Apenas administradores podem criar novas equipes.');
        }
        try {
            $coaches = User::where('role', 'coach')->where('is_active', true)->orderBy('name')->get();
            $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
        } catch (\Exception $e) {
            $coaches = collect([]);
            $categories = [];
        }
        
        return view('teams.create', compact('coaches', 'categories'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role === 'coach') {
            abort(403, 'Acesso negado. Apenas administradores podem criar novas equipes.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coach_id' => 'nullable|exists:users,id',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team = new Team($request->except(['logo']));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->storeOptimized('teams');
            $team->logo = $path;
        }

        $team->save();

        return redirect()->route('admin.teams.show', $team)
            ->with('success', 'Equipe criada com sucesso!');
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team)
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $team->coach_id !== $user->id) {
            abort(403, 'Acesso negado. Você não tem permissão para visualizar esta equipe.');
        }
        $team->load(['coach', 'athletes.branch']);
        
        $athletes = $team->athletes()
            ->with('branch')
            ->orderBy('full_name')
            ->get();

        return view('teams.show', compact('team', 'athletes'));
    }

    /**
     * Show the form for editing the team.
     */
    public function edit(Team $team)
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $team->coach_id !== $user->id) {
            abort(403, 'Acesso negado. Você não tem permissão para editar esta equipe.');
        }
        $coaches = User::where('role', 'coach')->where('is_active', true)->orderBy('name')->get();
        $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
        
        return view('teams.edit', compact('team', 'coaches', 'categories'));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team)
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $team->coach_id !== $user->id) {
            abort(403, 'Acesso negado. Você não tem permissão para atualizar esta equipe.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coach_id' => 'nullable|exists:users,id',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team->update($request->except(['logo']));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            
            $path = $request->file('logo')->storeOptimized('teams');
            $team->update(['logo' => $path]);
        }

        return redirect()->route('admin.teams.show', $team)
            ->with('success', 'Equipe atualizada com sucesso!');
    }

    /**
     * Remove the specified team.
     */
    public function destroy(Team $team)
    {
        if (auth()->user()->role === 'coach') {
            abort(403, 'Acesso negado. Apenas administradores podem excluir equipes.');
        }
        // Check if team has athletes
        if ($team->athletes()->count() > 0) {
            return redirect()->route('admin.teams.index')
                ->with('error', 'Não é possível excluir uma equipe que possui atletas.');
        }

        // Delete logo
        if ($team->logo) {
            Storage::disk('public')->delete($team->logo);
        }

        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', 'Equipe removida com sucesso!');
    }

    /**
     * Toggle team active status.
     */
    public function toggleStatus(Team $team)
    {
        $team->update(['is_active' => !$team->is_active]);

        $status = $team->is_active ? 'ativada' : 'desativada';
        
        return redirect()->route('admin.teams.show', $team)
            ->with('success', "Equipe {$status} com sucesso!");
    }

    public function generateTeamAiPlan(Request $request, Team $team, \App\Services\AIService $aiService)
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $team->coach_id !== $user->id) {
            abort(403, 'Acesso negado. Você não tem permissão para gerar planos para esta equipe.');
        }

        $request->validate([
            'type' => 'required|string|in:workout_plan,meal_plan',
            'goal' => 'required|string|max:255',
        ]);

        $athletes = $team->athletes;

        if ($athletes->isEmpty()) {
            return redirect()->back()->with('error', 'Esta equipe não possui atletas.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($athletes as $athlete) {
            try {
                if ($request->type === 'workout_plan') {
                    $aiContent = $aiService->generateWorkoutPlan($athlete);
                } else {
                    $aiContent = $aiService->generateMealPlan($athlete);
                }

                if ($aiContent) {
                    \App\Models\AiGeneratedContent::where('athlete_id', $athlete->id)
                        ->where('type', $request->type)
                        ->where('status', 'active')
                        ->where('id', '!=', $aiContent->id)
                        ->update(['status' => 'archived']);

                    $content = $aiContent->content;
                    $duration = $content['duration_days'] ?? 30;
                    $frequency = $content['frequency_label'] ?? '3x por semana';
                    $notifications = $content['notification_suggestions'] ?? ['08:00', '16:00'];

                    $aiContent->update([
                        'status' => 'active',
                        'goal' => $request->goal,
                        'start_date' => now(),
                        'end_date' => now()->addDays($duration),
                        'frequency' => $frequency,
                        'notification_settings' => array_filter($notifications),
                    ]);
                    $successCount++;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Erro ao gerar plano para atleta {$athlete->id}: " . $e->getMessage());
                $failCount++;
            }
        }

        $msg = "Planos coletivos gerados com sucesso para {$successCount} atletas.";
        if ($failCount > 0) $msg .= " Falha em {$failCount} atletas.";

        return redirect()->back()->with('success', $msg);
    }
}

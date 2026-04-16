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
    public function index()
    {
        try {
            $teams = Team::withCount('athletes')
                ->with('coach')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $teams = collect([]);
        }

        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        try {
            $coaches = User::where('role', 'coach')->orderBy('name')->get();
        } catch (\Exception $e) {
            $coaches = collect([]);
        }
        
        return view('teams.create', compact('coaches'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coach_user_id' => 'nullable|exists:users,id',
            'color_primary' => 'required|string|max:7',
            'color_secondary' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team = new Team($request->except(['logo']));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('teams', 'public');
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
        $coaches = User::where('role', 'coach')->orderBy('name')->get();
        
        return view('teams.edit', compact('team', 'coaches'));
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'coach_user_id' => 'nullable|exists:users,id',
            'color_primary' => 'required|string|max:7',
            'color_secondary' => 'required|string|max:7',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $team->update($request->except(['logo']));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($team->logo) {
                Storage::disk('public')->delete($team->logo);
            }
            
            $path = $request->file('logo')->store('teams', 'public');
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

        return redirect()->route('teams.index')
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
}

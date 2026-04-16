<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    /**
     * Display a listing of trainings.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // TODO: Implement when Training model is created
        // For now, return empty list
        $trainings = [];
        $teams = Team::where('is_active', true)->get();

        return view('trainings.index', compact('trainings', 'teams'));
    }

    /**
     * Show the form for creating a new training.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            $teams = Team::where('is_active', true)->get();
            $athletes = Athlete::where('is_active', true)->get();
        } catch (\Exception $e) {
            $teams = collect([]);
            $athletes = collect([]);
        }

        return view('trainings.create', compact('teams', 'athletes'));
    }

    /**
     * Store a newly created training.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'athlete_ids' => 'nullable|array',
            'athlete_ids.*' => 'exists:athletes,id',
        ]);

        try {
            // TODO: Implement when Training model is created
            Log::info('Training created', [
                'title' => $request->title,
                'date' => $request->date,
                'team_id' => $request->team_id,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('trainings.index')
                ->with('success', 'Treino agendado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error creating training', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao agendar treino: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified training.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id)
    {
        // TODO: Implement when Training model is created
        $training = null;

        return view('trainings.show', compact('training'));
    }

    /**
     * Show the form for editing the training.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        // TODO: Implement when Training model is created
        $training = null;
        $teams = Team::where('is_active', true)->get();
        $athletes = Athlete::where('is_active', true)->get();

        return view('trainings.edit', compact('training', 'teams', 'athletes'));
    }

    /**
     * Update the specified training.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'athlete_ids' => 'nullable|array',
            'athlete_ids.*' => 'exists:athletes,id',
        ]);

        try {
            // TODO: Implement when Training model is created
            Log::info('Training updated', [
                'training_id' => $id,
                'title' => $request->title,
                'updated_by' => auth()->id(),
            ]);

            return redirect()->route('trainings.show', $id)
                ->with('success', 'Treino atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating training', [
                'training_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar treino: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Remove the specified training.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            // TODO: Implement when Training model is created
            Log::info('Training deleted', [
                'training_id' => $id,
                'deleted_by' => auth()->id(),
            ]);

            return redirect()->route('trainings.index')
                ->with('success', 'Treino removido com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error deleting training', [
                'training_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao remover treino: ' . $e->getMessage()
            ]);
        }
    }
}


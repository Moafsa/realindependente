<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\SiteSetting;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $query = \App\Models\Training::with('team')->latest();

        $user = auth()->user();
        if ($user->role === 'coach') {
            $query->whereHas('team', function($q) use ($user) {
                $q->where('coach_id', $user->id);
            });
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $trainings = $query->paginate(20);
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
            $user = auth()->user();
            $teamsQuery = Team::where('is_active', true);
            if ($user->role === 'coach') {
                $teamsQuery->where('coach_id', $user->id);
            }
            $teams = $teamsQuery->get();
            
            $athletesQuery = Athlete::where('is_active', true);
            if ($user->role === 'coach') {
                $athletesQuery->whereIn('team_id', $teams->pluck('id'));
            }
            $athletes = $athletesQuery->get();
        } catch (\Exception $e) {
            $teams = collect([]);
            $athletes = collect([]);
        }

        // Busca o token do Mapbox do banco central (superadmin), não do banco do tenant
        $mapboxSetting = DB::connection('pgsql')
            ->table('site_settings')
            ->where('key', 'mapbox_public_token')
            ->first();

        $settings = [
            'mapbox_public_token' => $mapboxSetting?->value ?? '',
        ];

        return view('trainings.create', compact('teams', 'athletes', 'settings'));
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
            'type' => 'required|in:training,match,event',
            'description' => 'nullable|string|max:5000',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'status' => 'nullable|in:scheduled,completed,cancelled',
            'athlete_ids' => 'nullable|array',
            'athlete_ids.*' => 'exists:athletes,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $training = \App\Models\Training::create([
                'title' => $request->title,
                'type' => $request->type,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time,
                'location' => $request->location,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'team_id' => $request->team_id,
                'status' => $request->status ?? 'scheduled',
            ]);

            // Busca os atletas que devem receber a notificação
            $athletes = collect([]);
            if ($request->filled('team_id')) {
                $team = Team::find($request->team_id);
                $athletes = $team->athletes()->where('is_active', true)->get();
            } else {
                // Se for Geral, busca todos os atletas ativos do clube
                $athletes = Athlete::where('is_active', true)->get();
            }

            // Prepara dados para o evento
            $trainingData = [
                'title' => $training->title,
                'date' => \Carbon\Carbon::parse($training->date)->format('d/m/Y'),
                'time' => $training->time,
                'location' => $training->location ?? 'Não informado',
                'team_name' => $training->team->name ?? 'Geral',
                'type' => $training->type,
            ];

            $athleteData = $athletes->map(function($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->full_name,
                    'phone' => $a->phone,
                    'guardian_contact' => $a->guardian_contact,
                ];
            })->toArray();

            // Dispara o evento de notificação (Se existir a classe)
            if (class_exists('\App\Events\TrainingScheduled')) {
                event(new \App\Events\TrainingScheduled($trainingData, $athleteData));
            }

            return redirect()->route('trainings.index')
                ->with('success', 'Atividade agendada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error creating training', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao agendar atividade: ' . $e->getMessage()
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
        $training = \App\Models\Training::with('team')->findOrFail($id);
        
        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$training->team || $training->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Você não tem permissão para visualizar esta atividade.');
            }
        }

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
        $training = \App\Models\Training::findOrFail($id);
        
        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$training->team || $training->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Você não tem permissão para editar esta atividade.');
            }
        }

        $teams = Team::where('is_active', true);
        if ($user->role === 'coach') {
            $teams->where('coach_id', $user->id);
        }
        $teams = $teams->get();

        $athletes = Athlete::where('is_active', true);
        if ($user->role === 'coach') {
            $athletes->whereIn('team_id', $teams->pluck('id'));
        }
        $athletes = $athletes->get();

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
        $training = \App\Models\Training::findOrFail($id);

        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$training->team || $training->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Você não tem permissão para atualizar esta atividade.');
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:training,match,event',
            'description' => 'nullable|string|max:5000',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        try {
            $training->update($request->all());

            return redirect()->route('trainings.index')
                ->with('success', 'Atividade atualizada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating training', [
                'training_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar atividade: ' . $e->getMessage()
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
            $training = \App\Models\Training::findOrFail($id);

            $user = auth()->user();
            if ($user->role === 'coach') {
                if (!$training->team || $training->team->coach_id !== $user->id) {
                    abort(403, 'Acesso negado. Você não tem permissão para excluir esta atividade.');
                }
            }

            $training->delete();

            return redirect()->route('trainings.index')
                ->with('success', 'Atividade removida com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error deleting training', [
                'training_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao remover atividade: ' . $e->getMessage()
            ]);
        }
    }
}


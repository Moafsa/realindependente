<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CoachController extends Controller
{
    /**
     * Display a listing of coaches.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'coach');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $coaches = $query->paginate(20);

        // Calculate balance for each coach on the current page
        $coaches->getCollection()->transform(function($coach) {
            $coach->current_balance = \App\Models\CashFlow::where('recipient_id', $coach->id)
                ->selectRaw("SUM(CASE WHEN type = 'exit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0;
            return $coach;
        });

        return view('admin.coaches.index', compact('coaches'));
    }

    /**
     * Display the specified coach (Admin View).
     */
    public function show(User $coach)
    {
        if ($coach->role !== 'coach') abort(403);

        $coach->current_balance = \App\Models\CashFlow::where('recipient_id', $coach->id)
            ->selectRaw("SUM(CASE WHEN type = 'exit' THEN amount ELSE -amount END) as balance")
            ->value('balance') ?? 0;

        $teams = $coach->teams()->withCount('athletes')->get();
        $total_athletes = $teams->sum('athletes_count');

        return view('admin.coaches.show', compact('coach', 'teams', 'total_athletes'));
    }

    /**
     * Show the form for creating a new coach.
     */
    public function create()
    {
        $frequencies = ['per_training' => 'Por Treino', 'hourly' => 'Por Hora', 'daily' => 'Diário', 'weekly' => 'Semanal', 'biweekly' => 'Quinzenal', 'monthly' => 'Mensal'];
        return view('admin.coaches.create', compact('frequencies'));
    }

    /**
     * Store a newly created coach.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|in:per_training,hourly,daily,weekly,biweekly,monthly',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'coach';
        $data['is_active'] = true;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->storeOptimized('avatars');
        }

        User::create($data);

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Treinador criado com sucesso!');
    }

    /**
     * Show the form for editing the coach.
     */
    public function edit(User $coach)
    {
        if ($coach->role !== 'coach') abort(403);
        
        $frequencies = ['per_training' => 'Por Treino', 'hourly' => 'Por Hora', 'daily' => 'Diário', 'weekly' => 'Semanal', 'biweekly' => 'Quinzenal', 'monthly' => 'Mensal'];
        return view('admin.coaches.edit', compact('coach', 'frequencies'));
    }

    /**
     * Update the coach.
     */
    public function update(Request $request, User $coach)
    {
        if ($coach->role !== 'coach') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $coach->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|in:per_training,hourly,daily,weekly,biweekly,monthly',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'avatar']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($coach->avatar) {
                Storage::disk(config('filesystems.default'))->delete($coach->avatar);
            }
            $data['avatar'] = $request->file('avatar')->storeOptimized('avatars');
        }

        $coach->update($data);

        return redirect()->route('admin.coaches.index')
            ->with('success', 'Treinador atualizado com sucesso!');
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(User $coach)
    {
        if ($coach->role !== 'coach') abort(403);

        $coach->update(['is_active' => !$coach->is_active]);
        $status = $coach->is_active ? 'ativado' : 'desativado';

        return redirect()->back()->with('success', "Treinador {$status} com sucesso!");
    }

    /**
     * Remove coach.
     */
    public function destroy(User $coach)
    {
        if ($coach->role !== 'coach') abort(403);

        // Check if coach is assigned to any team
        if (Team::where('coach_id', $coach->id)->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir um treinador vinculado a uma equipe.');
        }

        if ($coach->avatar) {
            Storage::disk('public')->delete($coach->avatar);
        }

        $coach->delete();

        return redirect()->route('admin.coaches.index')->with('success', 'Treinador excluído com sucesso!');
    }

    /**
     * Register a payment for the coach.
     */
    public function payCoach(Request $request, User $coach)
    {
        if ($coach->role !== 'coach') abort(403);
        if (!$coach->salary) {
            return redirect()->back()->with('error', 'Este treinador não possui salário definido.');
        }

        \App\Models\CashFlow::create([
            'description' => "Pagamento Treinador: {$coach->name}",
            'amount' => $coach->salary,
            'type' => 'exit',
            'date' => now(),
            'category' => 'Salários',
            'status' => 'completed',
            'notes' => "Pagamento registrado via gestão de treinadores.",
            'created_by' => auth()->id(),
            'recipient_id' => $coach->id,
        ]);

        return redirect()->back()->with('success', "Pagamento de R$ " . number_format($coach->salary, 2, ',', '.') . " registrado com sucesso!");
    }

    /**
     * Adicionar uma transação manual (crédito/débito) para o treinador.
     */
    public function addTransaction(Request $request, User $coach)
    {
        if ($coach->role !== 'coach') abort(403);

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'type' => 'required|in:entry,exit',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        \App\Models\CashFlow::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'date' => $request->date,
            'category' => 'Salários/Comissões',
            'status' => 'completed',
            'notes' => $request->notes ?? "Lançamento manual para treinador: {$coach->name}",
            'created_by' => auth()->id(),
            'recipient_id' => $coach->id,
        ]);

        $typeLabel = $request->type === 'entry' ? 'Entrada' : 'Saída';
        return redirect()->back()->with('success', "{$typeLabel} de R$ " . number_format($request->amount, 2, ',', '.') . " registrada com sucesso!");
    }

    /**
     * Ver extrato financeiro do treinador logado.
     */
    public function extract(Request $request)
    {
        $user = auth()->user();
        
        $targetUserId = null;
        $targetCoach = null;

        if ($user->role === 'admin') {
            $coachId = $request->query('coach_id');
            if (!$coachId) {
                return redirect()->route('admin.coaches.index')->with('error', 'Treinador não especificado.');
            }
            $targetCoach = \App\Models\User::find($coachId);
            if (!$targetCoach || $targetCoach->role !== 'coach') {
                return redirect()->route('admin.coaches.index')->with('error', 'Treinador inválido.');
            }
            $targetUserId = $coachId;
        } elseif ($user->role === 'coach') {
            $targetUserId = $user->id;
            $targetCoach = $user;
        } else {
            abort(403);
        }

        $transactions = \App\Models\CashFlow::where('recipient_id', $targetUserId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_balance' => \App\Models\CashFlow::where('recipient_id', $targetUserId)
                ->selectRaw("SUM(CASE WHEN type = 'exit' THEN amount ELSE -amount END) as balance")
                ->value('balance') ?? 0,
            'entries' => \App\Models\CashFlow::where('recipient_id', $targetUserId)
                ->where('type', 'exit')
                ->sum('amount'),
            'exits' => \App\Models\CashFlow::where('recipient_id', $targetUserId)
                ->where('type', 'entry')
                ->sum('amount'),
        ];

        return view('admin.coaches.extract', compact('transactions', 'stats', 'targetCoach'));
    }

    /**
     * Ver e editar perfil do treinador.
     */
    public function profile()
    {
        $coach = auth()->user();
        if ($coach->role !== 'coach') abort(403);

        return view('admin.coaches.profile', compact('coach'));
    }

    /**
     * Atualizar perfil do treinador.
     */
    public function updateProfile(Request $request)
    {
        $coach = auth()->user();
        if ($coach->role !== 'coach') abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:2000',
            'education' => 'nullable|string|max:1000',
            'experience' => 'nullable|string|max:2000',
            'specialties' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
            'certificate_files.*' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'certificate_names.*' => 'nullable|string|max:255',
        ]);

        $data = $request->only(['name', 'phone', 'bio', 'education', 'experience', 'specialties']);

        if ($request->hasFile('avatar')) {
            if ($coach->avatar) {
                \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->delete($coach->avatar);
            }
            $data['avatar'] = $request->file('avatar')->storeOptimized('avatars');
        }

        // Handle Certificates
        $certificates = $coach->certificates ?? [];
        
        // Remove certificates if requested (simple logic: if we provide new ones or clear)
        if ($request->has('remove_certificate')) {
            $indexToRemove = $request->remove_certificate;
            if (isset($certificates[$indexToRemove])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($certificates[$indexToRemove]['path']);
                unset($certificates[$indexToRemove]);
                $certificates = array_values($certificates); // reindex
            }
        }

        if ($request->hasFile('certificate_files')) {
            foreach ($request->file('certificate_files') as $key => $file) {
                if ($file && $file->isValid()) {
                    $name = $request->certificate_names[$key] ?? $file->getClientOriginalName();
                    $path = $file->storeOptimized('coach_certificates', 'public');
                    $certificates[] = [
                        'name' => $name,
                        'path' => $path,
                        'date' => now()->format('Y-m-d')
                    ];
                }
            }
        }
        
        $data['certificates'] = $certificates;

        $coach->update($data);

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }
}

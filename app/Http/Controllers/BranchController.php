<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Athlete;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of branches.
     */
    public function index()
    {
        try {
            $branches = Branch::withCount('athletes')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $branches = collect([]);
        }

        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_info' => 'nullable|array',
        ]);

        $branch = Branch::create($request->all());

        return redirect()->route('branches.show', $branch)
            ->with('success', 'Filial criada com sucesso!');
    }

    /**
     * Display the specified branch.
     */
    public function show(Branch $branch)
    {
        $branch->load(['athletes.team']);
        
        $athletes = $branch->athletes()
            ->with('team')
            ->orderBy('full_name')
            ->get();

        return view('branches.show', compact('branch', 'athletes'));
    }

    /**
     * Show the form for editing the branch.
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_info' => 'nullable|array',
        ]);

        $branch->update($request->all());

        return redirect()->route('branches.show', $branch)
            ->with('success', 'Filial atualizada com sucesso!');
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Branch $branch)
    {
        // Check if branch has athletes
        if ($branch->athletes()->count() > 0) {
            return redirect()->route('branches.index')
                ->with('error', 'Não é possível excluir uma filial que possui atletas.');
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Filial removida com sucesso!');
    }

    /**
     * Toggle branch active status.
     */
    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);

        $status = $branch->is_active ? 'ativada' : 'desativada';
        
        return redirect()->route('branches.show', $branch)
            ->with('success', "Filial {$status} com sucesso!");
    }
}

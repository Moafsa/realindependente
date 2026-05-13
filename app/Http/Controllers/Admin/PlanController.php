<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::ordered()->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_athletes' => 'required|integer|min:0',
            'max_branches' => 'required|integer|min:0',
            'ai_features' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer',
        ]);

        $validated['ai_features'] = $request->has('ai_features');
        $validated['is_active'] = $request->has('is_active');
        $validated['features'] = explode(',', $request->features_raw);

        Plan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_athletes' => 'required|integer|min:0',
            'max_branches' => 'required|integer|min:0',
            'ai_features' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer',
        ]);

        $validated['ai_features'] = $request->has('ai_features');
        $validated['is_active'] = $request->has('is_active');
        $validated['features'] = array_filter(array_map('trim', explode(',', $request->features_raw)));

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->tenants()->count() > 0) {
            return back()->with('error', 'Não é possível excluir um plano que possui clubes vinculados.');
        }

        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plano excluído com sucesso!');
    }
}

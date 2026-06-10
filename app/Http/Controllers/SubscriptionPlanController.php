<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::withTrashed()->where('type', 'subscription');

            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $plans = $query->latest()->paginate(20);
        } catch (\Exception $e) {
            $plans = new \Illuminate\Pagination\LengthAwarePaginator(collect([]), 0, 20);
        }

        return view('admin.subscription-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan.
     */
    public function create()
    {
        return view('admin.subscription-plans.create');
    }

    /**
     * Store a newly created plan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'cycle' => 'required|in:MONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'evaluation_frequency' => 'nullable|string',
        ]);

        try {
            $plan = new Product($request->except(['image', 'cycle', 'setup_fee', 'evaluation_frequency']));
            $plan->type = 'subscription';
            
            $attributes = $request->input('attributes', []);
            $attributes['cycle'] = $request->input('cycle');
            $attributes['setup_fee'] = $request->input('setup_fee', 0);
            $attributes['evaluation_frequency'] = $request->input('evaluation_frequency');
            
            // Características pré-prontas
            $attributes['features'] = [
                'insurance' => $request->boolean('features.insurance'),
                'evaluation' => $request->boolean('features.evaluation'),
                'training_plan' => $request->boolean('features.training_plan'),
                'diet_plan' => $request->boolean('features.diet_plan'),
                'whatsapp_support' => $request->boolean('features.whatsapp_support'),
            ];

            // Detalhes editáveis
            $attributes['training_details'] = [
                'days_per_week' => $request->input('training.days'),
                'hours_per_day' => $request->input('training.hours'),
                'uniform' => $request->input('training.uniform'),
                'other_details' => $request->input('training.other'),
            ];

            $plan->attributes = $attributes;
            
            if ($request->hasFile('image')) {
                $plan->image = $request->file('image')->store('products');
            }

            $plan->stock_quantity = 0;
            $plan->is_active = $request->boolean('is_active', true);
            $plan->is_featured = $request->boolean('is_featured', false);
            
            $plan->save();

            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Plano de assinatura criado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error creating plan: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erro ao criar plano: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the plan.
     */
    public function edit(Product $subscription_plan)
    {
        if ($subscription_plan->type !== 'subscription') {
            return redirect()->route('admin.subscription-plans.index');
        }
        return view('admin.subscription-plans.edit', ['plan' => $subscription_plan]);
    }

    /**
     * Update the specified plan.
     */
    public function update(Request $request, Product $subscription_plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'cycle' => 'required|in:MONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'evaluation_frequency' => 'nullable|string',
        ]);

        try {
            $subscription_plan->fill($request->except(['image', 'cycle', 'features', 'training', 'setup_fee', 'evaluation_frequency']));
            
            $attributes = $subscription_plan->attributes ?? [];
            $attributes['cycle'] = $request->input('cycle');
            $attributes['setup_fee'] = $request->input('setup_fee', 0);
            $attributes['evaluation_frequency'] = $request->input('evaluation_frequency');
            
            // Características pré-prontas
            $attributes['features'] = [
                'insurance' => $request->boolean('features.insurance'),
                'evaluation' => $request->boolean('features.evaluation'),
                'training_plan' => $request->boolean('features.training_plan'),
                'diet_plan' => $request->boolean('features.diet_plan'),
                'whatsapp_support' => $request->boolean('features.whatsapp_support'),
            ];

            // Detalhes editáveis
            $attributes['training_details'] = [
                'days_per_week' => $request->input('training.days'),
                'hours_per_day' => $request->input('training.hours'),
                'uniform' => $request->input('training.uniform'),
                'other_details' => $request->input('training.other'),
            ];

            $subscription_plan->attributes = $attributes;

            if ($request->hasFile('image')) {
                if ($subscription_plan->image) {
                    Storage::delete($subscription_plan->image);
                }
                $subscription_plan->image = $request->file('image')->store('products');
            }

            $subscription_plan->is_active = $request->boolean('is_active', $subscription_plan->is_active);
            $subscription_plan->is_featured = $request->boolean('is_featured', $subscription_plan->is_featured);
            
            $subscription_plan->save();

            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Plano atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating plan: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erro ao atualizar plano: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified plan.
     */
    public function destroy(Product $subscription_plan)
    {
        try {
            $subscription_plan->delete();
            return redirect()->route('admin.subscription-plans.index')
                ->with('success', 'Plano removido com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao remover plano.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CashFlow::query();

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Stats
        $entries = (clone $query)->where('type', 'entry')->sum('amount');
        $exits = (clone $query)->where('type', 'exit')->sum('amount');
        $balance = $entries - $exits;

        $transactions = $query->with('creator')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = CashFlow::select('category')->distinct()->pluck('category');

        return view('cash-flow.index', compact('transactions', 'entries', 'exits', 'balance', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:entry,exit',
            'date' => 'required|date',
            'category' => 'required|string|max:100',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        CashFlow::create($validated);

        return redirect()->route('admin.cash-flow.index')
            ->with('success', 'Transação registrada com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashFlow $cashFlow)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:entry,exit',
            'date' => 'required|date',
            'category' => 'required|string|max:100',
            'status' => 'required|in:pending,completed',
            'notes' => 'nullable|string',
        ]);

        $cashFlow->update($validated);

        return redirect()->route('admin.cash-flow.index')
            ->with('success', 'Transação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashFlow $cashFlow)
    {
        $cashFlow->delete();

        return redirect()->route('admin.cash-flow.index')
            ->with('success', 'Transação removida com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user', 'athlete', 'orderItems.product']);

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('id', $request->search)
                      ->orWhereHas('user', function ($userQuery) use ($request) {
                          $userQuery->where('name', 'like', '%' . $request->search . '%')
                                    ->orWhere('email', 'like', '%' . $request->search . '%');
                      })
                      ->orWhereHas('athlete', function ($athleteQuery) use ($request) {
                          $athleteQuery->where('full_name', 'like', '%' . $request->search . '%');
                      });
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $orders = $query->latest()->paginate(20);
            $statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

            // Statistics
            $stats = [
                'total' => Order::count(),
                'pending' => Order::where('status', 'pending')->count(),
                'paid' => Order::where('status', 'paid')->count(),
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
                'monthly_revenue' => Order::where('status', 'paid')
                    ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            ];
        } catch (\Exception $e) {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
            $statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
            $stats = [
                'total' => 0,
                'pending' => 0,
                'paid' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
            ];
        }

        return view('orders.index', compact('orders', 'statuses', 'stats'));
    }

    /**
     * Display the specified order.
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load(['user', 'athlete', 'orderItems.product']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Update order status.
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $oldStatus = $order->status;
            
            $order->update([
                'status' => $request->status,
                'notes' => $request->notes ?? $order->notes,
            ]);

            // Update timestamps based on status
            if ($request->status === 'paid' && !$order->paid_at) {
                $order->update(['paid_at' => now()]);
            }

            if ($request->status === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }

            if ($request->status === 'delivered' && !$order->delivered_at) {
                $order->update(['delivered_at' => now()]);
            }

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
            ]);

            return redirect()->back()
                ->with('success', 'Status do pedido atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel order.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Order $order)
    {
        if (!$order->can_be_cancelled) {
            return back()->withErrors([
                'error' => 'Este pedido não pode ser cancelado.'
            ]);
        }

        try {
            $order->update([
                'status' => 'cancelled',
            ]);

            Log::info('Order cancelled', [
                'order_id' => $order->id,
            ]);

            return redirect()->back()
                ->with('success', 'Pedido cancelado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error cancelling order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao cancelar pedido: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark order as shipped.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ship(Order $order)
    {
        if ($order->status !== 'paid') {
            return back()->withErrors([
                'error' => 'Apenas pedidos pagos podem ser enviados.'
            ]);
        }

        try {
            $order->update([
                'status' => 'shipped',
                'shipped_at' => now(),
            ]);

            Log::info('Order shipped', [
                'order_id' => $order->id,
            ]);

            return redirect()->back()
                ->with('success', 'Pedido marcado como enviado!');

        } catch (\Exception $e) {
            Log::error('Error shipping order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao marcar como enviado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark order as delivered.
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deliver(Order $order)
    {
        if ($order->status !== 'shipped') {
            return back()->withErrors([
                'error' => 'Apenas pedidos enviados podem ser marcados como entregues.'
            ]);
        }

        try {
            $order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            Log::info('Order delivered', [
                'order_id' => $order->id,
            ]);

            return redirect()->back()
                ->with('success', 'Pedido marcado como entregue!');

        } catch (\Exception $e) {
            Log::error('Error delivering order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao marcar como entregue: ' . $e->getMessage()
            ]);
        }
    }
}


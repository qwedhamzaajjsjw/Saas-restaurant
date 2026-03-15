<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // قائمة حالات الطلب بالترتيب الصحيح
    const STATUS_FLOW = [
        'pending', 'confirmed', 'preparing', 'ready', 'delivered',
    ];

    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_name', 'like', '%'.$request->search.'%');
            });
        }

        $orders       = $query->paginate(20)->withQueryString();
        $statusCounts = Order::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        return view('restaurant.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');
        return view('restaurant.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,ready,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated to: '.ucfirst($request->status));
    }
}

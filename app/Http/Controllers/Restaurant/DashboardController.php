<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id;

        $stats = [
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'today_orders'    => Order::whereDate('created_at', today())->count(),
            'today_revenue'   => Order::whereDate('created_at', today())
                                      ->where('payment_status', 'paid')->sum('total'),
            'month_revenue'   => Order::whereMonth('created_at', now()->month)
                                      ->whereYear('created_at', now()->year)
                                      ->where('payment_status', 'paid')->sum('total'),
            'total_products'  => \App\Models\Product::count(),
        ];

        // أحدث 8 طلبات
        $latestOrders = Order::with('items')->latest()->take(8)->get();

        // الطلبات حسب الحالة
        $ordersByStatus = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        // أكثر المنتجات مبيعاً
        $topProducts = \App\Models\OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn($q) => $q->where('restaurant_id', $restaurantId))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)->get();

        return view('restaurant.dashboard', compact(
            'stats', 'latestOrders', 'ordersByStatus', 'topProducts'
        ));
    }
}

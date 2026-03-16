<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class ImpersonateDashboardController extends Controller
{
    /**
     * Show the restaurant dashboard as a super admin preview.
     * No auth switching — we just load the restaurant data directly.
     */
    public function show(Restaurant $restaurant)
    {
        $rid = $restaurant->id;

        // Update session so the banner knows which restaurant we're viewing
        session([
            'impersonating_restaurant_id'   => $rid,
            'impersonating_restaurant_name' => $restaurant->name,
        ]);

        $stats = [
            'total_orders'   => Order::withoutGlobalScopes()->where('restaurant_id', $rid)->count(),
            'pending_orders' => Order::withoutGlobalScopes()->where('restaurant_id', $rid)->where('status', 'pending')->count(),
            'today_orders'   => Order::withoutGlobalScopes()->where('restaurant_id', $rid)->whereDate('created_at', today())->count(),
            'today_revenue'  => Order::withoutGlobalScopes()->where('restaurant_id', $rid)->whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
            'month_revenue'  => Order::withoutGlobalScopes()->where('restaurant_id', $rid)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->where('payment_status', 'paid')->sum('total'),
            'total_products' => Product::withoutGlobalScopes()->where('restaurant_id', $rid)->count(),
        ];

        $latestOrders = Order::withoutGlobalScopes()
            ->where('restaurant_id', $rid)
            ->with('items')
            ->latest()
            ->take(8)
            ->get();

        $ordersByStatus = Order::withoutGlobalScopes()
            ->where('restaurant_id', $rid)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('order', fn($q) => $q->withoutGlobalScopes()->where('restaurant_id', $rid))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Pass restaurant explicitly so the view can use it
        return view('super-admin.restaurants.preview-dashboard', compact(
            'restaurant', 'stats', 'latestOrders', 'ordersByStatus', 'topProducts'
        ));
    }
}

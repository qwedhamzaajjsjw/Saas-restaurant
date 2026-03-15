<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_restaurants'  => Restaurant::count(),
            'active_restaurants' => Restaurant::where('status', 'active')->count(),
            'total_users'        => User::where('role', '!=', 'super_admin')->count(),
            'total_orders'       => Order::withoutGlobalScopes()->count(),
            'orders_today'       => Order::withoutGlobalScopes()->whereDate('created_at', today())->count(),
            'monthly_revenue'    => Subscription::whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->sum('amount_paid'),
        ];

        $latestRestaurants = Restaurant::with('plan')->latest()->take(5)->get();

        $latestOrders = Order::withoutGlobalScopes()
            ->with('restaurant')->latest()->take(8)->get();

        $ordersByStatus = Order::withoutGlobalScopes()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('super-admin.dashboard', compact(
            'stats', 'latestRestaurants', 'latestOrders', 'ordersByStatus'
        ));
    }
}

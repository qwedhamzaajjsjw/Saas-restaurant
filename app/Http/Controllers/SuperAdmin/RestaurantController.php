<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::with(['plan', 'owner'])
            ->withCount('orders');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }

        $restaurants = $query->latest()->paginate(15)->withQueryString();
        $plans = Plan::active()->get();

        return view('super-admin.restaurants.index', compact('restaurants', 'plans'));
    }

    public function create()
    {
        $plans = Plan::active()->get();
        return view('super-admin.restaurants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'plan_id'        => ['required', 'exists:plans,id'],
            // حساب المالك
            'owner_name'     => ['required', 'string', 'max:255'],
            'owner_email'    => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($data) {
            // إنشاء المطعم
            $restaurant = Restaurant::create([
                'name'    => $data['name'],
                'slug'    => Str::slug($data['name']).'-'.Str::random(4),
                'email'   => $data['email']  ?? null,
                'phone'   => $data['phone']  ?? null,
                'address' => $data['address'] ?? null,
                'city'    => $data['city']    ?? null,
                'plan_id' => $data['plan_id'],
                'status'  => 'active',
            ]);

            // إنشاء حساب المالك
            User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'password'          => Hash::make($data['owner_password']),
                'role'              => 'restaurant_owner',
                'restaurant_id'     => $restaurant->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            // إنشاء اشتراك مجاني مبدئي
            Subscription::create([
                'restaurant_id' => $restaurant->id,
                'plan_id'       => $data['plan_id'],
                'starts_at'     => now(),
                'ends_at'       => now()->addMonth(),
                'status'        => 'trial',
                'amount_paid'   => 0,
            ]);
        });

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant created successfully.');
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['plan', 'owner', 'activeSubscription.plan']);
        $ordersCount   = $restaurant->orders()->withoutGlobalScopes()->count();
        $productsCount = $restaurant->products()->withoutGlobalScopes()->count();
        $revenue       = $restaurant->orders()->withoutGlobalScopes()
                            ->where('payment_status', 'paid')->sum('total');

        return view('super-admin.restaurants.show', compact(
            'restaurant', 'ordersCount', 'productsCount', 'revenue'
        ));
    }

    public function edit(Restaurant $restaurant)
    {
        $plans = Plan::active()->get();
        return view('super-admin.restaurants.edit', compact('restaurant', 'plans'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['nullable', 'email'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city'    => ['nullable', 'string', 'max:100'],
            'plan_id' => ['required', 'exists:plans,id'],
            'status'  => ['required', 'in:active,inactive,suspended'],
        ]);

        $restaurant->update($data);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant deleted.');
    }

    // ── تعليق / تفعيل المطعم ─────────────────────────────────────────
    public function toggleStatus(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => $restaurant->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'Restaurant status updated.');
    }
}

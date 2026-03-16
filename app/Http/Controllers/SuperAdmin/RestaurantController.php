<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'logo'           => ['nullable', 'image', 'max:2048'],
            'domain_type'    => ['required', 'in:subdomain,custom,none'],
            'subdomain'      => [
                'nullable', 'string', 'max:63',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$|^[a-z0-9]$/',
                Rule::unique('restaurants', 'subdomain'),
                Rule::requiredIf($request->domain_type === 'subdomain'),
            ],
            'custom_domain'  => [
                'nullable', 'string', 'max:253',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                Rule::unique('restaurants', 'custom_domain'),
                Rule::requiredIf($request->domain_type === 'custom'),
            ],
            // حساب المالك
            'owner_name'     => ['required', 'string', 'max:255'],
            'owner_email'    => ['required', 'email', 'unique:users,email'],
            'owner_password' => ['required', 'string', 'min:8'],
        ]);

        // Handle logo upload before transaction
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        DB::transaction(function () use ($data, $logoPath) {
            $restaurant = Restaurant::create([
                'name'          => $data['name'],
                'slug'          => Str::slug($data['name']).'-'.Str::random(4),
                'email'         => $data['email']   ?? null,
                'phone'         => $data['phone']   ?? null,
                'address'       => $data['address'] ?? null,
                'city'          => $data['city']    ?? null,
                'plan_id'       => $data['plan_id'],
                'logo'          => $logoPath,
                'status'        => 'active',
                'subdomain'     => $data['domain_type'] === 'subdomain' ? ($data['subdomain'] ?? null) : null,
                'custom_domain' => $data['domain_type'] === 'custom'    ? ($data['custom_domain'] ?? null) : null,
            ]);

            User::create([
                'name'              => $data['owner_name'],
                'email'             => $data['owner_email'],
                'password'          => Hash::make($data['owner_password']),
                'role'              => 'restaurant_owner',
                'restaurant_id'     => $restaurant->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

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
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'plan_id'       => ['required', 'exists:plans,id'],
            'logo'          => ['nullable', 'image', 'max:2048'],
            'status'        => ['required', 'in:active,inactive,suspended'],
            'domain_type'   => ['required', 'in:subdomain,custom,none'],
            'subdomain'     => [
                'nullable', 'string', 'max:63',
                'regex:/^[a-z0-9][a-z0-9\-]*[a-z0-9]$|^[a-z0-9]$/',
                Rule::unique('restaurants', 'subdomain')->ignore($restaurant->id),
            ],
            'custom_domain' => [
                'nullable', 'string', 'max:253',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                Rule::unique('restaurants', 'custom_domain')->ignore($restaurant->id),
            ],
        ]);

        $updateData = [
            'name'          => $data['name'],
            'email'         => $data['email']   ?? null,
            'phone'         => $data['phone']   ?? null,
            'address'       => $data['address'] ?? null,
            'city'          => $data['city']    ?? null,
            'plan_id'       => $data['plan_id'],
            'status'        => $data['status'],
            'subdomain'     => $data['domain_type'] === 'subdomain' ? ($data['subdomain'] ?? null) : null,
            'custom_domain' => $data['domain_type'] === 'custom'    ? ($data['custom_domain'] ?? null) : null,
        ];

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $updateData['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $restaurant->update($updateData);

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant deleted.');
    }

    public function toggleStatus(Restaurant $restaurant)
    {
        $restaurant->update([
            'status' => $restaurant->status === 'active' ? 'suspended' : 'active',
        ]);

        return back()->with('success', 'Restaurant status updated.');
    }

    /**
     * Switch to restaurant owner account so super admin can use the real dashboard.
     * Creates a one-time cache token and redirects to a clean (role-free) switch route.
     */
    public function loginAs(Restaurant $restaurant)
    {
        $owner = $restaurant->users()->where('role', 'restaurant_owner')->first();

        if (! $owner) {
            return back()->with('error', 'This restaurant has no owner account yet.');
        }

        $token = Str::random(64);

        Cache::put('restaurant_switch_' . $token, [
            'admin_id'      => auth()->id(),
            'restaurant_id' => $restaurant->id,
        ], now()->addMinutes(2));

        return redirect()->route('auth.restaurant-switch', $token);
    }
}

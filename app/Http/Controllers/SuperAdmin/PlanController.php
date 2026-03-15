<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('restaurants')->orderBy('sort_order')->get();
        return view('super-admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'description'           => ['nullable', 'string'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'max_products'          => ['required', 'integer', 'min:1'],
            'max_orders_per_month'  => ['required', 'integer', 'min:1'],
            'max_categories'        => ['required', 'integer', 'min:1'],
            'features'              => ['nullable', 'array'],
            'is_active'             => ['boolean'],
            'sort_order'            => ['integer', 'min:0'],
        ]);

        $data['slug']     = Str::slug($data['name']);
        $data['features'] = $data['features'] ?? [];
        $data['is_active'] = $request->boolean('is_active', true);

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'description'           => ['nullable', 'string'],
            'price'                 => ['required', 'numeric', 'min:0'],
            'max_products'          => ['required', 'integer', 'min:1'],
            'max_orders_per_month'  => ['required', 'integer', 'min:1'],
            'max_categories'        => ['required', 'integer', 'min:1'],
            'features'              => ['nullable', 'array'],
            'is_active'             => ['boolean'],
            'sort_order'            => ['integer', 'min:0'],
        ]);

        $data['features']  = $data['features'] ?? [];
        $data['is_active'] = $request->boolean('is_active');

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->restaurants()->count() > 0) {
            return back()->with('error', 'Cannot delete a plan that has active restaurants.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plan deleted.');
    }
}

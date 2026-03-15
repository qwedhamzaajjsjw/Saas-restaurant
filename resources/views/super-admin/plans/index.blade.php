@extends('layouts.admin')
@section('title', 'Subscription Plans')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Subscription Plans</h2>
    <a href="{{ route('admin.plans.create') }}"
       class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Plan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($plans as $plan)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 flex flex-col">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h3>
                <p class="text-3xl font-bold text-orange-500 mt-1">
                    ${{ number_format($plan->price, 2) }}
                    <span class="text-sm text-gray-400 font-normal">/mo</span>
                </p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $plan->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <ul class="space-y-2 text-sm text-gray-600 mb-4 flex-1">
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                {{ $plan->max_products }} Products
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                {{ $plan->max_orders_per_month }} Orders/month
            </li>
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                {{ $plan->max_categories }} Categories
            </li>
        </ul>

        <div class="text-xs text-gray-400 mb-4">{{ $plan->restaurants_count }} restaurants on this plan</div>

        <div class="flex gap-2 mt-auto">
            <a href="{{ route('admin.plans.edit', $plan) }}"
               class="flex-1 text-center border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium py-2 rounded-xl transition">
                Edit
            </a>
            @if($plan->restaurants_count === 0)
            <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                @csrf @method('DELETE')
                <button class="text-sm text-red-500 hover:text-red-700 px-3 py-2">Delete</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection

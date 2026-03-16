@extends('layouts.admin')
@section('title', 'Restaurants')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-semibold text-gray-700">All Restaurants</h2>
        <p class="text-sm text-gray-400">Manage all registered restaurants on the platform.</p>
    </div>
    <a href="{{ route('admin.restaurants.create') }}"
       class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Restaurant
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by name or email..."
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-orange-400">
    <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none">
        <option value="">All Status</option>
        <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
        <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
    </select>
    <select name="plan" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none">
        <option value="">All Plans</option>
        @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
        @endforeach
    </select>
    <button class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-700 transition">
        Filter
    </button>
    @if(request()->hasAny(['search','status','plan']))
        <a href="{{ route('admin.restaurants.index') }}" class="border border-gray-200 text-gray-500 px-4 py-2 rounded-xl text-sm hover:bg-gray-50 transition">
            Clear
        </a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Restaurant</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Orders</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Created</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($restaurants as $restaurant)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($restaurant->logo)
                            <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}"
                                 class="w-9 h-9 rounded-xl object-cover border border-gray-100">
                        @else
                            <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center font-bold text-orange-600">
                                {{ strtoupper(substr($restaurant->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $restaurant->name }}</p>
                            <p class="text-xs text-gray-400">{{ $restaurant->email ?? $restaurant->city }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $restaurant->plan?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $restaurant->orders_count }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $restaurant->status === 'active'    ? 'bg-green-100 text-green-700' :
                           ($restaurant->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ ucfirst($restaurant->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-400 text-xs">{{ $restaurant->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-1.5 justify-end flex-wrap">
                        {{-- Dashboard --}}
                        <a href="{{ route('restaurant.login') }}"
                           title="Restaurant Dashboard Login"
                           class="inline-flex items-center gap-1 text-xs bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200 px-2.5 py-1.5 rounded-lg font-medium transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                        {{-- Storefront Preview --}}
                        <a href="{{ $restaurant->storefront_url }}" target="_blank"
                           title="View Restaurant Storefront"
                           class="inline-flex items-center gap-1 text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 px-2.5 py-1.5 rounded-lg font-medium transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Preview
                        </a>
                        {{-- View Details --}}
                        <a href="{{ route('admin.restaurants.show', $restaurant) }}"
                           class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 hover:underline">View</a>
                        <a href="{{ route('admin.restaurants.edit', $restaurant) }}"
                           class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.restaurants.toggle-status', $restaurant) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs {{ $restaurant->status === 'active' ? 'text-red-500' : 'text-green-500' }} hover:underline px-2 py-1.5">
                                {{ $restaurant->status === 'active' ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    No restaurants found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($restaurants->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $restaurants->links() }}
    </div>
    @endif
</div>

@endsection

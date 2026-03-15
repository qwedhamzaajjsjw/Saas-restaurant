@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- ── بطاقات الإحصائيات ────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
    $cards = [
        ['label'=>'Total Restaurants', 'value'=>$stats['total_restaurants'],  'color'=>'blue',   'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['label'=>'Active',            'value'=>$stats['active_restaurants'], 'color'=>'green',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Users',             'value'=>$stats['total_users'],        'color'=>'purple', 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label'=>'Total Orders',      'value'=>$stats['total_orders'],       'color'=>'orange', 'icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['label'=>'Orders Today',      'value'=>$stats['orders_today'],       'color'=>'yellow', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Monthly Revenue',   'value'=>'$'.number_format($stats['monthly_revenue'],2), 'color'=>'emerald', 'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 13v-1m0-8v.01'],
    ];
    @endphp

    @foreach($cards as $card)
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $card['value'] }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $card['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── المحتوى الرئيسي ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- أحدث المطاعم --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Latest Restaurants</h3>
            <a href="{{ route('admin.restaurants.index') }}" class="text-xs text-orange-500 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($latestRestaurants as $restaurant)
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center font-bold text-orange-600 text-sm">
                        {{ strtoupper(substr($restaurant->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $restaurant->name }}</p>
                        <p class="text-xs text-gray-400">{{ $restaurant->plan?->name ?? 'No plan' }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $restaurant->status === 'active' ? 'bg-green-100 text-green-700' :
                       ($restaurant->status === 'suspended' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($restaurant->status) }}
                </span>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-gray-400">No restaurants yet.</p>
            @endforelse
        </div>
    </div>

    {{-- أحدث الطلبات --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Latest Orders</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($latestOrders as $order)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400">{{ $order->restaurant?->name }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">${{ number_format($order->total, 2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-700' :
                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' :
                            ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="px-6 py-4 text-sm text-gray-400">No orders yet.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

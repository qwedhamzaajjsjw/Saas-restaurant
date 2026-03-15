@extends('layouts.restaurant')
@section('title', 'Dashboard')

@section('content')

{{-- بطاقات الإحصائيات --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
@php
$cards = [
    ['label'=>'Total Orders',   'value'=>$stats['total_orders'],                      'color'=>'blue',   'icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
    ['label'=>'Pending',        'value'=>$stats['pending_orders'],                     'color'=>'yellow', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['label'=>'Today Orders',   'value'=>$stats['today_orders'],                       'color'=>'orange', 'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
    ['label'=>'Today Revenue',  'value'=>'$'.number_format($stats['today_revenue'],2), 'color'=>'green',  'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 13v-1'],
    ['label'=>'Month Revenue',  'value'=>'$'.number_format($stats['month_revenue'],2), 'color'=>'purple', 'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ['label'=>'Products',       'value'=>$stats['total_products'],                     'color'=>'pink',   'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
];
@endphp
@foreach($cards as $c)
<div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
    <div class="w-10 h-10 bg-{{ $c['color'] }}-100 rounded-xl flex items-center justify-center mb-3">
        <svg class="w-5 h-5 text-{{ $c['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/>
        </svg>
    </div>
    <p class="text-2xl font-bold text-gray-800">{{ $c['value'] }}</p>
    <p class="text-xs text-gray-500 mt-1">{{ $c['label'] }}</p>
</div>
@endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- أحدث الطلبات --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Latest Orders</h3>
            <a href="{{ route('restaurant.orders.index') }}" class="text-xs text-orange-500 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($latestOrders as $order)
            <div class="flex items-center justify-between px-6 py-3">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400">{{ $order->customer_name }} · {{ $order->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-800">${{ number_format($order->total,2) }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        @if($order->status==='pending')      bg-yellow-100 text-yellow-700
                        @elseif($order->status==='confirmed') bg-blue-100 text-blue-700
                        @elseif($order->status==='preparing') bg-orange-100 text-orange-700
                        @elseif($order->status==='ready')     bg-purple-100 text-purple-700
                        @elseif($order->status==='delivered') bg-green-100 text-green-700
                        @else                                 bg-red-100 text-red-700
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p class="px-6 py-6 text-sm text-gray-400 text-center">No orders yet.</p>
            @endforelse
        </div>
    </div>

    {{-- أكثر المنتجات مبيعاً --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Top Products</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topProducts as $i => $p)
            <div class="flex items-center gap-3 px-6 py-3">
                <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ $i+1 }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 truncate">{{ $p->product_name }}</p>
                </div>
                <span class="text-xs font-semibold text-gray-600">{{ $p->total_qty }} sold</span>
            </div>
            @empty
            <p class="px-6 py-6 text-sm text-gray-400 text-center">No sales yet.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

@extends('layouts.restaurant')
@section('title','Orders')
@section('content')

{{-- إحصائيات سريعة للحالات --}}
<div class="flex gap-3 mb-6 flex-wrap">
    @foreach(['pending'=>'yellow','confirmed'=>'blue','preparing'=>'orange','ready'=>'purple','delivered'=>'green','cancelled'=>'red'] as $status => $color)
    <a href="{{ route('restaurant.orders.index', ['status'=>$status]) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium border transition
              {{ request('status') === $status ? "bg-{$color}-500 text-white border-{$color}-500" : "bg-white text-gray-600 border-gray-200 hover:border-{$color}-300" }}">
        {{ ucfirst($status) }}
        <span class="bg-white/30 text-xs px-1.5 py-0.5 rounded-full">{{ $statusCounts[$status] ?? 0 }}</span>
    </a>
    @endforeach
    @if(request('status'))
    <a href="{{ route('restaurant.orders.index') }}" class="px-4 py-2 rounded-xl text-sm text-gray-400 border border-dashed border-gray-200 hover:bg-gray-50">All</a>
    @endif
</div>

{{-- بحث --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
    @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or customer name..."
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm flex-1 min-w-40 focus:outline-none focus:ring-2 focus:ring-orange-400">
    <input type="date" name="date" value="{{ request('date') }}"
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none">
    <button class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-700 transition">Search</button>
</form>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Order</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Items</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Time</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($orders as $order)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <p class="font-mono font-medium text-gray-800 text-xs">{{ $order->order_number }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($order->type) }}</p>
                </td>
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                    <p class="text-xs text-gray-400">{{ $order->customer_phone }}</p>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $order->items->count() }} items</td>
                <td class="px-6 py-4 font-semibold text-gray-800">${{ number_format($order->total,2) }}</td>
                <td class="px-6 py-4">
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                        @if($order->status==='pending')      bg-yellow-100 text-yellow-700
                        @elseif($order->status==='confirmed') bg-blue-100 text-blue-700
                        @elseif($order->status==='preparing') bg-orange-100 text-orange-700
                        @elseif($order->status==='ready')     bg-purple-100 text-purple-700
                        @elseif($order->status==='delivered') bg-green-100 text-green-700
                        @else                                 bg-red-100 text-red-700
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
                <td class="px-6 py-4">
                    <a href="{{ route('restaurant.orders.show',$order) }}"
                       class="text-xs text-orange-500 hover:underline">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>
@endsection

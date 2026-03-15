@extends('layouts.customer')

@section('title', 'Order Confirmed — '.$restaurant->name)

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- ── Success header ─────────────────────────────────────────────────── --}}
    <div class="text-center py-10">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Order Placed Successfully!</h1>
        <p class="text-gray-500 text-sm">
            Thank you, <strong>{{ $order->customer_name }}</strong>. We've received your order.
        </p>
        <div class="mt-3 inline-block bg-orange-50 border border-orange-200 text-brand font-bold text-lg px-5 py-2 rounded-xl">
            {{ $order->order_number }}
        </div>
    </div>

    {{-- ── Order details card ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">

        {{-- Status bar --}}
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $order->created_at->format('d M Y, H:i') }}
            </div>
            @php
                $colorMap = [
                    'pending'   => 'yellow',
                    'confirmed' => 'blue',
                    'preparing' => 'orange',
                    'ready'     => 'purple',
                    'delivered' => 'green',
                    'cancelled' => 'red',
                ];
                $color = $colorMap[$order->status] ?? 'gray';
            @endphp
            <span class="text-xs font-semibold px-3 py-1 rounded-full
                bg-{{ $color }}-100 text-{{ $color }}-700">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- Info rows --}}
        <div class="p-5 grid sm:grid-cols-2 gap-4 border-b border-gray-100 text-sm">
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Order Type</p>
                <p class="font-medium text-gray-700">{{ ucfirst(str_replace('_',' ', $order->type)) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">Payment</p>
                <p class="font-medium text-gray-700">{{ ucfirst($order->payment_method) }} — {{ ucfirst($order->payment_status) }}</p>
            </div>
            @if($order->delivery_address)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 mb-0.5">Delivery Address</p>
                <p class="font-medium text-gray-700">{{ $order->delivery_address }}{{ $order->delivery_city ? ', '.$order->delivery_city : '' }}</p>
            </div>
            @endif
        </div>

        {{-- Items list --}}
        <div class="p-5 space-y-3">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Items Ordered</h3>
            @foreach($order->items as $item)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-gray-100 rounded-full text-xs font-bold text-gray-500 flex items-center justify-center">
                        {{ $item->quantity }}
                    </span>
                    <span class="text-gray-700">{{ $item->product_name }}</span>
                    @if($item->notes)
                        <span class="text-xs text-gray-400">({{ $item->notes }})</span>
                    @endif
                </div>
                <span class="font-medium text-gray-800">{{ number_format($item->subtotal, 2) }}</span>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="px-5 pb-5 space-y-1 text-sm border-t border-gray-50 pt-4">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->delivery_fee > 0)
            <div class="flex justify-between text-gray-500">
                <span>Delivery Fee</span>
                <span>{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            @endif
            @if($order->tax_amount > 0)
            <div class="flex justify-between text-gray-500">
                <span>Tax</span>
                <span>{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100 mt-2">
                <span>Total</span>
                <span class="text-brand">{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── CTA buttons ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('customer.index', $restaurant->slug) }}"
           class="btn-brand flex-1 text-center py-3 rounded-xl font-semibold text-sm hover:opacity-90 transition">
            Back to Restaurant
        </a>
        <a href="{{ route('customer.menu', $restaurant->slug) }}"
           class="flex-1 text-center py-3 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
            Order Again
        </a>
    </div>

</div>

@endsection

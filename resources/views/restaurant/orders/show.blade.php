@extends('layouts.restaurant')
@section('title','Order #'.$order->order_number)
@section('content')
<div class="max-w-2xl">
    <a href="{{ route('restaurant.orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Back to Orders
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-4">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800 font-mono">{{ $order->order_number }}</h2>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            <span class="text-sm px-3 py-1.5 rounded-xl font-semibold
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

        {{-- معلومات العميل --}}
        <div class="grid grid-cols-2 gap-4 text-sm mb-6 p-4 bg-gray-50 rounded-xl">
            <div><span class="text-gray-400">Customer: </span><span class="font-medium">{{ $order->customer_name }}</span></div>
            <div><span class="text-gray-400">Phone: </span><span class="font-medium">{{ $order->customer_phone ?? '—' }}</span></div>
            <div><span class="text-gray-400">Email: </span><span class="font-medium">{{ $order->customer_email }}</span></div>
            <div><span class="text-gray-400">Type: </span><span class="font-medium">{{ ucfirst($order->type) }}</span></div>
            @if($order->delivery_address)
            <div class="col-span-2"><span class="text-gray-400">Address: </span><span class="font-medium">{{ $order->delivery_address }}</span></div>
            @endif
            @if($order->notes)
            <div class="col-span-2"><span class="text-gray-400">Notes: </span><span class="font-medium">{{ $order->notes }}</span></div>
            @endif
        </div>

        {{-- بنود الطلب --}}
        <div class="space-y-3 mb-6">
            @foreach($order->items as $item)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs font-bold">
                        {{ $item->quantity }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                        @if($item->notes)<p class="text-xs text-gray-400">{{ $item->notes }}</p>@endif
                    </div>
                </div>
                <span class="text-sm font-semibold text-gray-800">${{ number_format($item->subtotal,2) }}</span>
            </div>
            @endforeach
        </div>

        {{-- الإجمالي --}}
        <div class="border-t border-gray-100 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-gray-500"><span>Subtotal</span><span>${{ number_format($order->subtotal,2) }}</span></div>
            @if($order->delivery_fee > 0)
            <div class="flex justify-between text-gray-500"><span>Delivery</span><span>${{ number_format($order->delivery_fee,2) }}</span></div>
            @endif
            @if($order->tax_amount > 0)
            <div class="flex justify-between text-gray-500"><span>Tax</span><span>${{ number_format($order->tax_amount,2) }}</span></div>
            @endif
            <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100">
                <span>Total</span><span>${{ number_format($order->total,2) }}</span>
            </div>
        </div>
    </div>

    {{-- تحديث الحالة --}}
    @if(!in_array($order->status, ['delivered','cancelled']))
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Update Status</h3>
        <form method="POST" action="{{ route('restaurant.orders.status',$order) }}" class="flex flex-wrap gap-2">
            @csrf @method('PATCH')
            @foreach(['pending'=>'gray','confirmed'=>'blue','preparing'=>'orange','ready'=>'purple','delivered'=>'green','cancelled'=>'red'] as $s => $c)
            <button type="submit" name="status" value="{{ $s }}"
                    class="px-4 py-2 rounded-xl text-sm font-medium border transition
                           {{ $order->status === $s ? "bg-{$c}-500 text-white border-{$c}-500" : "border-gray-200 text-gray-600 hover:bg-{$c}-50 hover:border-{$c}-300" }}">
                {{ ucfirst($s) }}
            </button>
            @endforeach
        </form>
    </div>
    @endif
</div>
@endsection

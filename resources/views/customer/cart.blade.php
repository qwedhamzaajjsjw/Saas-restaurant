@extends('layouts.customer')

@section('title', 'Cart — '.$restaurant->name)

@section('content')

<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Your Cart</h1>

    @if($cart->isEmpty())
    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p class="text-lg font-semibold text-gray-500 mb-4">Your cart is empty</p>
        <a href="{{ route('customer.menu', $restaurant->slug) }}"
           class="btn-brand inline-block px-6 py-2.5 rounded-xl font-semibold text-sm">
            Browse Menu
        </a>
    </div>

    @else

    <div class="grid md:grid-cols-3 gap-6">

        {{-- ── Cart items ──────────────────────────────────────────────── --}}
        <div class="md:col-span-2 space-y-3">
            @foreach($cart->all() as $productId => $item)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-4">

                <div class="w-16 h-16 rounded-xl bg-gray-100 flex-shrink-0 overflow-hidden">
                    @if($item['image'])
                        <img src="{{ asset('storage/'.$item['image']) }}"
                             alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $item['name'] }}</p>
                    <p class="text-brand text-sm font-bold">{{ number_format($item['price'], 2) }}</p>
                    @if($item['notes'])
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $item['notes'] }}</p>
                    @endif
                </div>

                {{-- Quantity controls --}}
                <div class="flex items-center gap-2">
                    <form action="{{ route('customer.cart.update', [$restaurant->slug, $productId]) }}"
                          method="POST" class="flex items-center gap-1">
                        @csrf @method('PATCH')
                        <button type="submit" name="quantity" value="{{ max(0, $item['quantity']-1) }}"
                                class="w-7 h-7 rounded-full border border-gray-200 text-gray-500 hover:border-brand hover:text-brand text-lg font-bold transition flex items-center justify-center">
                            −
                        </button>
                        <span class="w-7 text-center text-sm font-bold text-gray-700">{{ $item['quantity'] }}</span>
                        <button type="submit" name="quantity" value="{{ $item['quantity']+1 }}"
                                class="w-7 h-7 rounded-full border border-gray-200 text-gray-500 hover:border-brand hover:text-brand text-lg font-bold transition flex items-center justify-center">
                            +
                        </button>
                    </form>

                    <form action="{{ route('customer.cart.remove', [$restaurant->slug, $productId]) }}"
                          method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-7 h-7 rounded-full text-red-400 hover:text-red-600 hover:bg-red-50 transition flex items-center justify-center"
                                title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="text-right min-w-[60px]">
                    <p class="font-bold text-gray-800 text-sm">{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Order summary ───────────────────────────────────────────── --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">
                <h3 class="font-bold text-gray-800 mb-4">Order Summary</h3>

                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium">{{ number_format($cart->subtotal(), 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery</span>
                        <span class="text-gray-400">Calculated at checkout</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-3 mb-5 flex justify-between font-bold text-gray-800">
                    <span>Total</span>
                    <span class="text-brand">{{ number_format($cart->subtotal(), 2) }}</span>
                </div>

                @if($restaurant->accepts_orders)
                <a href="{{ '#checkout' }}" id="checkout-btn"
                   onclick="document.getElementById('checkout-form').scrollIntoView({behavior:'smooth'}); return false;"
                   class="btn-brand block text-center py-3 rounded-xl font-semibold text-sm hover:opacity-90 transition">
                    Proceed to Checkout
                </a>
                @else
                <div class="bg-red-50 text-red-600 text-center py-3 rounded-xl text-sm font-medium">
                    Restaurant is currently closed
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Checkout Form ─────────────────────────────────────────────────── --}}
    @if($restaurant->accepts_orders)
    <div id="checkout-form" class="mt-8 bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5">Checkout Details</h2>

        <form action="{{ route('customer.checkout', $restaurant->slug) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:border-transparent outline-none"
                           required>
                    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:border-transparent outline-none"
                           required>
                    @error('customer_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email (optional)</label>
                <input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:border-transparent outline-none">
            </div>

            {{-- Order type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Type *</label>
                <div class="flex gap-3">
                    @foreach(['dine_in' => 'Dine In', 'takeaway' => 'Takeaway', 'delivery' => 'Delivery'] as $val => $label)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="type" value="{{ $val }}"
                               class="sr-only peer" {{ old('type','dine_in') === $val ? 'checked' : '' }}>
                        <div class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-center font-medium
                                    peer-checked:border-brand peer-checked:text-brand peer-checked:bg-orange-50
                                    hover:border-gray-300 transition">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Delivery address (shown when delivery is selected) --}}
            <div id="delivery-fields" class="{{ old('type') === 'delivery' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                <input type="text" name="delivery_address" value="{{ old('delivery_address') }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:border-transparent outline-none">
                @error('delivery_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Payment method --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                <div class="flex gap-3">
                    @foreach(['cash' => 'Cash', 'card' => 'Card', 'online' => 'Online'] as $val => $label)
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="payment_method" value="{{ $val }}"
                               class="sr-only peer" {{ old('payment_method','cash') === $val ? 'checked' : '' }}>
                        <div class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-center font-medium
                                    peer-checked:border-brand peer-checked:text-brand peer-checked:bg-orange-50
                                    hover:border-gray-300 transition">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-brand focus:border-transparent outline-none resize-none"
                          placeholder="Any special requests…">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="btn-brand w-full py-3 rounded-xl font-semibold text-sm hover:opacity-90 transition">
                Place Order — {{ number_format($cart->subtotal(), 2) }}
            </button>
        </form>
    </div>
    @endif

    @endif
</div>

@endsection

@push('scripts')
<script>
// Show/hide delivery address based on order type selection
document.querySelectorAll('input[name="type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var deliveryFields = document.getElementById('delivery-fields');
        if (this.value === 'delivery') {
            deliveryFields.classList.remove('hidden');
        } else {
            deliveryFields.classList.add('hidden');
        }
    });
});
</script>
@endpush

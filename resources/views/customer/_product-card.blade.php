{{-- بطاقة منتج واحد — تُستخدم في index.blade + menu.blade --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group flex flex-col">

    {{-- صورة المنتج --}}
    <div class="relative aspect-square overflow-hidden bg-gray-100">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
        @if($product->isOnSale())
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">Sale</span>
        @endif
    </div>

    {{-- بيانات المنتج --}}
    <div class="p-3 flex-1 flex flex-col">
        <h3 class="font-semibold text-gray-800 text-sm leading-tight mb-1 line-clamp-2">{{ $product->name }}</h3>

        @if($product->description)
            <p class="text-xs text-gray-400 mb-2 line-clamp-2">{{ $product->description }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between gap-2">
            <div>
                <span class="font-bold text-brand text-sm">
                    {{ number_format($product->effective_price, 2) }}
                </span>
                @if($product->isOnSale())
                    <span class="text-xs text-gray-400 line-through ml-1">{{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            @if($restaurant->accepts_orders)
            <form action="{{ route('customer.cart.add', $restaurant->slug) }}" method="POST" class="add-to-cart-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity"   value="1">
                <button type="submit"
                        class="btn-brand w-8 h-8 rounded-full flex items-center justify-center hover:opacity-80 transition text-lg font-bold leading-none"
                        title="Add to cart">+</button>
            </form>
            @endif
        </div>
    </div>
</div>

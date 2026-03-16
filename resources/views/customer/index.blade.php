@extends('layouts.customer')
@section('title', $restaurant->name)

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════
     HERO SLIDER  (full-width, breaks out of the max-w container)
══════════════════════════════════════════════════════════════════════ --}}
<div class="-mx-4 -mt-6 mb-0 relative overflow-hidden" style="height:420px;" id="hero-slider">

    {{-- Slides --}}
    @php
        $slides = [];
        if ($restaurant->cover_image)
            $slides[] = ['img' => asset('storage/'.$restaurant->cover_image), 'title' => $restaurant->name, 'sub' => $restaurant->description ?? 'Fresh & Delicious Food'];
        // Fill up to 3 slides with featured product images
        foreach ($featured->take(3 - count($slides)) as $p)
            if ($p->image)
                $slides[] = ['img' => $p->image_url, 'title' => $p->name, 'sub' => $p->description ?? ''];
        // Fallback gradient slides
        $gradients = [
            'from-orange-500 to-red-500',
            'from-yellow-400 to-orange-500',
            'from-red-400 to-pink-500',
        ];
        while (count($slides) < 3)
            $slides[] = ['img' => null, 'title' => $restaurant->name, 'sub' => 'Order now & enjoy!', 'gradient' => $gradients[count($slides)]];
    @endphp

    @foreach($slides as $i => $slide)
    <div class="hero-slide absolute inset-0 transition-opacity duration-700 {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
         data-index="{{ $i }}">
        @if($slide['img'])
            <img src="{{ $slide['img'] }}" alt="{{ $slide['title'] }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
        @else
            <div class="w-full h-full bg-gradient-to-br {{ $slide['gradient'] ?? 'from-orange-500 to-red-500' }}">
                <div class="absolute inset-0 opacity-10"
                     style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23fff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')">
                </div>
            </div>
        @endif
        {{-- Slide content --}}
        <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10 z-10">
            <div class="max-w-5xl mx-auto">
                <p class="text-orange-300 text-sm font-semibold tracking-widest uppercase mb-2">
                    {{ $restaurant->accepts_orders ? '🟢 Open Now' : '🔴 Currently Closed' }}
                </p>
                <h1 class="text-white text-3xl sm:text-5xl font-black mb-2 leading-tight drop-shadow-lg">
                    {{ $slide['title'] }}
                </h1>
                @if($slide['sub'])
                <p class="text-white/80 text-sm sm:text-base max-w-md mb-4">{{ Str::limit($slide['sub'], 80) }}</p>
                @endif
                <div class="flex flex-wrap gap-3">
                    @if($restaurant->accepts_orders)
                    <a href="{{ route('customer.menu', $restaurant->slug) }}"
                       class="btn-brand px-6 py-2.5 rounded-2xl font-bold text-sm shadow-lg hover:opacity-90 transition">
                        Order Now →
                    </a>
                    @endif
                    <a href="#menu-section"
                       class="bg-white/20 backdrop-blur-sm text-white border border-white/30 px-6 py-2.5 rounded-2xl font-bold text-sm hover:bg-white/30 transition">
                        View Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Dots --}}
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
        @foreach($slides as $i => $slide)
        <button class="slider-dot w-2 h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'bg-white w-6' : 'bg-white/50' }}"
                data-index="{{ $i }}"></button>
        @endforeach
    </div>

    {{-- Arrows --}}
    <button id="slider-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button id="slider-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/30 hover:bg-black/50 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     RESTAURANT INFO BAR
══════════════════════════════════════════════════════════════════════ --}}
<div class="-mx-4 bg-white border-b border-gray-100 px-4 py-3 mb-6 shadow-sm">
    <div class="max-w-5xl mx-auto flex flex-wrap items-center gap-4 text-sm text-gray-600">
        @if($restaurant->address)
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{ $restaurant->address }}{{ $restaurant->city ? ', '.$restaurant->city : '' }}
        </span>
        @endif
        @if($restaurant->phone)
        <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ $restaurant->phone }}
        </span>
        @endif
        <span class="flex items-center gap-1.5 ml-auto">
            <span class="w-2 h-2 rounded-full {{ $restaurant->accepts_orders ? 'bg-green-500' : 'bg-red-500' }}"></span>
            {{ $restaurant->accepts_orders ? 'Accepting Orders' : 'Closed' }}
        </span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     CATEGORY QUICK NAV
══════════════════════════════════════════════════════════════════════ --}}
@if($categories->isNotEmpty())
<div class="-mx-4 bg-white border-b border-gray-100 px-4 py-3 mb-8 overflow-x-auto sticky top-16 z-20 shadow-sm">
    <div class="flex gap-2 whitespace-nowrap min-w-max">
        @if($offers->isNotEmpty())
        <a href="#offers-section"
           class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-semibold bg-red-500 text-white hover:bg-red-600 transition">
            🔥 Offers
        </a>
        @endif
        @foreach($categories as $cat)
        @if($cat->products->isNotEmpty())
        <a href="#cat-{{ $cat->id }}"
           class="category-pill inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-medium border border-gray-200 text-gray-600 hover:border-orange-400 hover:text-orange-500 bg-white transition"
           data-target="cat-{{ $cat->id }}">
            {{ $cat->name }}
            <span class="text-xs text-gray-400">{{ $cat->products->count() }}</span>
        </a>
        @endif
        @endforeach
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     SPECIAL OFFERS SECTION
══════════════════════════════════════════════════════════════════════ --}}
@if($offers->isNotEmpty())
<section id="offers-section" class="mb-10" id="menu-section">
    <div class="flex items-center gap-3 mb-5">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🔥</span>
            <h2 class="text-xl font-black text-gray-800">Special Offers</h2>
        </div>
        <div class="flex-1 h-px bg-gradient-to-r from-red-200 to-transparent"></div>
        <span class="bg-red-100 text-red-600 text-xs font-bold px-3 py-1 rounded-full">Limited Time</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($offers as $product)
        <div class="product-card group relative bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer"
             onclick="openProductModal(this)"
             data-product="{{ json_encode([
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => (float)($product->sale_price ?? $product->price),
                'base_price'  => (float)$product->price,
                'sale_price'  => $product->sale_price ? (float)$product->sale_price : null,
                'image'       => $product->image_url,
                'options'     => $product->options ?? [],
             ]) }}">
            {{-- Offer badge --}}
            @if($product->sale_price && $product->sale_price < $product->price)
            @php $disc = round((1 - $product->sale_price/$product->price)*100) @endphp
            <div class="absolute top-3 left-3 z-10 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                -{{ $disc }}%
            </div>
            @endif
            {{-- Image --}}
            <div class="relative h-44 bg-gradient-to-br from-orange-50 to-yellow-50 overflow-hidden">
                @if($product->image)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                @endif
            </div>
            {{-- Info --}}
            <div class="p-4">
                <h3 class="font-bold text-gray-800 mb-1 leading-snug">{{ $product->name }}</h3>
                @if($product->description)
                <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $product->description }}</p>
                @endif
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-lg font-black text-brand">{{ number_format($product->effective_price, 2) }}</span>
                        @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-xs text-gray-400 line-through ml-1">{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                    @if($restaurant->accepts_orders)
                    <button class="w-8 h-8 btn-brand rounded-full flex items-center justify-center shadow hover:opacity-90 transition text-lg font-bold">
                        +
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     CATEGORIES + PRODUCTS GRID
══════════════════════════════════════════════════════════════════════ --}}
<div id="menu-section">
@forelse($categories as $category)
@if($category->products->isNotEmpty())
<section class="mb-12" id="cat-{{ $category->id }}">
    {{-- Category header --}}
    <div class="flex items-center gap-3 mb-5">
        <div>
            <h2 class="text-xl font-black text-gray-800">{{ $category->name }}</h2>
            @if($category->description)
            <p class="text-xs text-gray-400 mt-0.5">{{ $category->description }}</p>
            @endif
        </div>
        <div class="flex-1 h-px bg-gradient-to-r from-gray-200 to-transparent"></div>
        <span class="text-xs text-gray-400 font-medium">{{ $category->products->count() }} items</span>
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($category->products as $product)
        <div class="product-card group relative bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 cursor-pointer"
             onclick="openProductModal(this)"
             data-product="{{ json_encode([
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => (float)($product->sale_price ?? $product->price),
                'base_price'  => (float)$product->price,
                'sale_price'  => $product->sale_price ? (float)$product->sale_price : null,
                'image'       => $product->image_url,
                'options'     => $product->options ?? [],
             ]) }}">

            {{-- Sale badge --}}
            @if($product->sale_price && $product->sale_price < $product->price)
            <div class="absolute top-2 left-2 z-10 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                Sale
            </div>
            @endif

            {{-- Image --}}
            <div class="relative aspect-square bg-gradient-to-br from-orange-50 to-amber-50 overflow-hidden">
                @if($product->image)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="p-3">
                <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-1">{{ $product->name }}</h3>
                @if($product->description)
                <p class="text-xs text-gray-400 mt-0.5 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                @endif
                <div class="flex items-center justify-between mt-2">
                    <div class="flex flex-col">
                        <span class="font-black text-brand text-sm">
                            {{ number_format($product->effective_price, 2) }}
                        </span>
                        @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="text-xs text-gray-300 line-through leading-none">
                            {{ number_format($product->price, 2) }}
                        </span>
                        @endif
                    </div>
                    @if($restaurant->accepts_orders)
                    <button class="w-8 h-8 btn-brand rounded-full flex items-center justify-center shadow-sm hover:shadow transition text-lg font-bold leading-none flex-shrink-0">
                        +
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif
@empty
<div class="text-center py-20 text-gray-400">
    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <p class="text-lg font-semibold">No menu items yet.</p>
</div>
@endforelse
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     PRODUCT MODAL
══════════════════════════════════════════════════════════════════════ --}}
<div id="product-modal"
     class="fixed inset-0 z-50 hidden"
     aria-modal="true" role="dialog">

    {{-- Backdrop --}}
    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         onclick="closeProductModal()"></div>

    {{-- Panel --}}
    <div id="modal-panel"
         class="absolute bottom-0 left-0 right-0 sm:relative sm:flex sm:items-center sm:justify-center sm:h-full sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full sm:max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl transform transition-transform duration-300 translate-y-full sm:translate-y-0 sm:scale-95"
             id="modal-content">

            {{-- Header image --}}
            <div class="relative h-52 sm:h-60 bg-gradient-to-br from-orange-100 to-amber-100 rounded-t-3xl sm:rounded-t-3xl overflow-hidden flex-shrink-0">
                <img id="modal-image" src="" alt="" class="w-full h-full object-cover">
                <div id="modal-no-image" class="hidden w-full h-full items-center justify-center">
                    <svg class="w-20 h-20 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                {{-- Close button --}}
                <button onclick="closeProductModal()"
                        class="absolute top-3 right-3 w-9 h-9 bg-black/40 hover:bg-black/60 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                {{-- Sale badge --}}
                <div id="modal-sale-badge" class="hidden absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full"></div>
            </div>

            <div class="p-5">
                {{-- Name + price --}}
                <div class="flex items-start justify-between mb-2">
                    <h2 id="modal-name" class="text-xl font-black text-gray-800 leading-tight pr-4"></h2>
                    <div class="text-right flex-shrink-0">
                        <div id="modal-price" class="text-xl font-black text-brand"></div>
                        <div id="modal-original-price" class="text-xs text-gray-400 line-through hidden"></div>
                    </div>
                </div>
                <p id="modal-description" class="text-sm text-gray-500 mb-4 leading-relaxed"></p>

                {{-- Extras / Add-ons --}}
                <div id="modal-extras-section" class="mb-4 hidden">
                    <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add-ons <span class="text-xs font-normal text-gray-400">(Optional)</span>
                    </h3>
                    <div id="modal-extras-list" class="space-y-2"></div>
                </div>

                {{-- Notes --}}
                <div class="mb-5">
                    <label class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Special Instructions
                    </label>
                    <textarea id="modal-notes" rows="2" placeholder="E.g. No onions, extra spicy…"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-orange-300 transition"></textarea>
                </div>

                {{-- Qty + Add to Cart --}}
                <div class="flex items-center gap-3">
                    {{-- Quantity --}}
                    <div class="flex items-center gap-0 border border-gray-200 rounded-xl overflow-hidden">
                        <button onclick="changeQty(-1)"
                                class="w-10 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition text-lg font-bold">
                            −
                        </button>
                        <span id="modal-qty" class="w-10 text-center font-bold text-gray-800 text-base select-none">1</span>
                        <button onclick="changeQty(1)"
                                class="w-10 h-11 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition text-lg font-bold">
                            +
                        </button>
                    </div>

                    {{-- Add to Cart --}}
                    <button id="modal-add-btn"
                            onclick="addToCart()"
                            class="flex-1 btn-brand py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:opacity-90 transition shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Add to Cart — <span id="modal-total">0.00</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     TOAST NOTIFICATION
══════════════════════════════════════════════════════════════════════ --}}
<div id="toast"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] hidden">
    <div class="bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-sm font-medium">
        <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span id="toast-msg">Added to cart!</span>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Cart config ────────────────────────────────────────────────────────────
const CART_URL    = "{{ route('customer.cart.add', $restaurant->slug) }}";
const CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]').content;
const ACCEPTS     = {{ $restaurant->accepts_orders ? 'true' : 'false' }};

// ── Hero Slider ────────────────────────────────────────────────────────────
(function() {
    const slides  = document.querySelectorAll('.hero-slide');
    const dots    = document.querySelectorAll('.slider-dot');
    let current   = 0;
    let timer;

    function goTo(n) {
        slides[current].classList.replace('opacity-100','opacity-0');
        slides[current].classList.replace('z-10','z-0');
        dots[current].classList.remove('w-6');
        dots[current].classList.add('bg-white/50');

        current = (n + slides.length) % slides.length;

        slides[current].classList.replace('opacity-0','opacity-100');
        slides[current].classList.replace('z-0','z-10');
        dots[current].classList.add('w-6');
        dots[current].classList.remove('bg-white/50');
    }

    function startAuto() { timer = setInterval(() => goTo(current + 1), 5000); }
    function stopAuto()  { clearInterval(timer); }

    document.getElementById('slider-next')?.addEventListener('click', () => { stopAuto(); goTo(current+1); startAuto(); });
    document.getElementById('slider-prev')?.addEventListener('click', () => { stopAuto(); goTo(current-1); startAuto(); });
    dots.forEach(d => d.addEventListener('click', () => { stopAuto(); goTo(+d.dataset.index); startAuto(); }));
    startAuto();
})();

// ── Category Nav active highlight on scroll ──────────────────────────────
(function() {
    const sections = document.querySelectorAll('section[id^="cat-"], section[id="offers-section"]');
    const pills    = document.querySelectorAll('.category-pill');

    function onScroll() {
        let current = '';
        sections.forEach(s => {
            if (window.scrollY >= s.offsetTop - 150) current = s.id;
        });
        pills.forEach(p => {
            if (p.dataset.target === current) {
                p.classList.add('border-orange-400','text-orange-500','bg-orange-50');
                p.classList.remove('border-gray-200','text-gray-600');
            } else {
                p.classList.remove('border-orange-400','text-orange-500','bg-orange-50');
                p.classList.add('border-gray-200','text-gray-600');
            }
        });
    }
    window.addEventListener('scroll', onScroll, {passive: true});
})();

// ── Product Modal ──────────────────────────────────────────────────────────
let modalProduct  = null;
let modalQty      = 1;
let selectedExtras = [];

function openProductModal(el) {
    if (!ACCEPTS) return;
    const data     = JSON.parse(el.dataset.product);
    modalProduct   = data;
    modalQty       = 1;
    selectedExtras = [];

    // Image
    const imgEl    = document.getElementById('modal-image');
    const noImgEl  = document.getElementById('modal-no-image');
    if (data.image) {
        imgEl.src = data.image;
        imgEl.classList.remove('hidden');
        noImgEl.classList.add('hidden');
    } else {
        imgEl.classList.add('hidden');
        noImgEl.classList.remove('hidden');
        noImgEl.classList.add('flex');
    }

    // Sale badge
    const badge = document.getElementById('modal-sale-badge');
    if (data.sale_price && data.sale_price < data.base_price) {
        const disc = Math.round((1 - data.sale_price / data.base_price) * 100);
        badge.textContent = '-' + disc + '%';
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }

    // Text
    document.getElementById('modal-name').textContent        = data.name;
    document.getElementById('modal-description').textContent = data.description || '';
    document.getElementById('modal-description').style.display = data.description ? '' : 'none';

    // Price
    document.getElementById('modal-price').textContent = data.price.toFixed(2);
    const origEl = document.getElementById('modal-original-price');
    if (data.sale_price && data.sale_price < data.base_price) {
        origEl.textContent = data.base_price.toFixed(2);
        origEl.classList.remove('hidden');
    } else {
        origEl.classList.add('hidden');
    }

    // Extras
    const extrasSection = document.getElementById('modal-extras-section');
    const extrasList    = document.getElementById('modal-extras-list');
    extrasList.innerHTML = '';

    if (data.options && data.options.length > 0) {
        extrasSection.classList.remove('hidden');
        data.options.forEach((opt, i) => {
            const label = document.createElement('label');
            label.className = 'flex items-center justify-between p-3 border border-gray-100 rounded-xl cursor-pointer hover:border-orange-300 hover:bg-orange-50 transition has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50';
            label.innerHTML = `
                <div class="flex items-center gap-3">
                    <input type="checkbox" class="extras-check w-4 h-4 accent-orange-500 cursor-pointer" data-index="${i}" onchange="updateExtras()">
                    <span class="text-sm font-medium text-gray-700">${opt.name}</span>
                </div>
                <span class="text-sm font-bold ${opt.price > 0 ? 'text-orange-500' : 'text-gray-400'}">
                    ${opt.price > 0 ? '+' + parseFloat(opt.price).toFixed(2) : 'Free'}
                </span>
            `;
            extrasList.appendChild(label);
        });
    } else {
        extrasSection.classList.add('hidden');
    }

    // Reset
    document.getElementById('modal-notes').value = '';
    document.getElementById('modal-qty').textContent = '1';
    updateTotal();

    // Show modal
    const modal = document.getElementById('product-modal');
    const content = document.getElementById('modal-content');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        content.classList.remove('translate-y-full','scale-95');
        content.classList.add('translate-y-0','scale-100');
    });
}

function closeProductModal() {
    const content = document.getElementById('modal-content');
    content.classList.add('translate-y-full','scale-95');
    content.classList.remove('translate-y-0','scale-100');
    setTimeout(() => {
        document.getElementById('product-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function changeQty(delta) {
    modalQty = Math.max(1, Math.min(99, modalQty + delta));
    document.getElementById('modal-qty').textContent = modalQty;
    updateTotal();
}

function updateExtras() {
    selectedExtras = [];
    document.querySelectorAll('.extras-check:checked').forEach(cb => {
        const opt = modalProduct.options[parseInt(cb.dataset.index)];
        selectedExtras.push({name: opt.name, price: parseFloat(opt.price)});
    });
    updateTotal();
}

function updateTotal() {
    if (!modalProduct) return;
    const extrasTotal = selectedExtras.reduce((s, e) => s + e.price, 0);
    const total = (modalProduct.price + extrasTotal) * modalQty;
    document.getElementById('modal-total').textContent = total.toFixed(2);
}

async function addToCart() {
    if (!modalProduct) return;
    const btn = document.getElementById('modal-add-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Adding…';

    try {
        const body = new FormData();
        body.append('_token',     CSRF_TOKEN);
        body.append('product_id', modalProduct.id);
        body.append('quantity',   modalQty);
        body.append('notes',      document.getElementById('modal-notes').value || '');
        if (selectedExtras.length > 0)
            body.append('extras', JSON.stringify(selectedExtras));

        const res = await fetch(CART_URL, {method:'POST', body, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const json = await res.json();

        if (json.success) {
            document.getElementById('cart-count').textContent = json.count;
            closeProductModal();
            showToast('Added to cart! 🛒');
        } else {
            showToast(json.message || 'Something went wrong');
        }
    } catch(e) {
        showToast('Connection error, please try again.');
    } finally {
        btn.disabled = false;
        const extrasTotal = selectedExtras.reduce((s,e) => s + e.price, 0);
        const total = (modalProduct.price + extrasTotal) * modalQty;
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Add to Cart — <span id="modal-total">${total.toFixed(2)}</span>`;
    }
}

// Close modal on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProductModal(); });

// ── Toast ──────────────────────────────────────────────────────────────────
let toastTimer;
function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.classList.remove('hidden');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.add('hidden'), 3000);
}
</script>
@endpush

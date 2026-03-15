@extends('layouts.customer')

@section('title', $restaurant->name)

@section('content')

{{-- ── Hero ──────────────────────────────────────────────────────────────── --}}
<div class="rounded-2xl overflow-hidden mb-8 relative bg-gray-800 text-white"
     style="min-height:200px;">
    @if($restaurant->cover_image)
        <img src="{{ asset('storage/'.$restaurant->cover_image) }}"
             alt="{{ $restaurant->name }}"
             class="absolute inset-0 w-full h-full object-cover opacity-40">
    @endif
    <div class="relative z-10 p-8">
        <h1 class="text-3xl font-bold mb-2">{{ $restaurant->name }}</h1>
        @if($restaurant->description)
            <p class="text-gray-200 max-w-xl">{{ $restaurant->description }}</p>
        @endif
        <div class="flex flex-wrap items-center gap-3 mt-4 text-sm">
            @if($restaurant->address)
                <span class="flex items-center gap-1 bg-white/20 px-3 py-1 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $restaurant->address }}
                </span>
            @endif
            @if($restaurant->phone)
                <span class="flex items-center gap-1 bg-white/20 px-3 py-1 rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $restaurant->phone }}
                </span>
            @endif
            <span class="flex items-center gap-1 px-3 py-1 rounded-full
                {{ $restaurant->accepts_orders ? 'bg-green-500/80' : 'bg-red-500/80' }}">
                {{ $restaurant->accepts_orders ? 'Open – Accepting Orders' : 'Closed' }}
            </span>
        </div>
    </div>
</div>

{{-- ── Featured Products ───────────────────────────────────────────────────── --}}
@if($featured->isNotEmpty())
<section class="mb-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-800">Featured Items</h2>
        <a href="{{ route('customer.menu', $restaurant->slug) }}"
           class="text-sm text-brand font-medium hover:underline">View full menu →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        @foreach($featured as $product)
            @include('customer._product-card', ['product' => $product, 'restaurant' => $restaurant])
        @endforeach
    </div>
</section>
@endif

{{-- ── Categories / Browse ─────────────────────────────────────────────────── --}}
@if($categories->isNotEmpty())
@foreach($categories as $category)
    @if($category->products->isNotEmpty())
    <section class="mb-10" id="cat-{{ $category->id }}">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            {{ $category->name }}
            <span class="text-sm font-normal text-gray-400">({{ $category->products->count() }} items)</span>
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($category->products as $product)
                @include('customer._product-card', ['product' => $product, 'restaurant' => $restaurant])
            @endforeach
        </div>
    </section>
    @endif
@endforeach
@else
    <div class="text-center py-20 text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-lg font-medium">No menu items available yet.</p>
    </div>
@endif

@endsection

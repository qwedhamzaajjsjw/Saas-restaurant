@extends('layouts.customer')

@section('title', 'Menu — '.$restaurant->name)

@section('content')

<div class="flex gap-6">

    {{-- ── Sidebar: category anchors (desktop) ───────────────────────────── --}}
    <aside class="hidden md:block w-44 flex-shrink-0">
        <div class="sticky top-24 bg-white rounded-2xl shadow-sm border border-gray-100 p-3 space-y-1">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider px-2 mb-2">Categories</p>
            @foreach($categories as $category)
                @if($category->products->isNotEmpty())
                <a href="#cat-{{ $category->id }}"
                   class="block px-2 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-orange-50 hover:text-brand transition">
                    {{ $category->name }}
                    <span class="text-xs text-gray-400 ml-1">({{ $category->products->count() }})</span>
                </a>
                @endif
            @endforeach
        </div>
    </aside>

    {{-- ── Main: product grid per category ───────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        @if($categories->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <p class="text-lg font-medium">No menu items available yet.</p>
            </div>
        @endif

        @foreach($categories as $category)
            @if($category->products->isNotEmpty())
            <section class="mb-10" id="cat-{{ $category->id }}">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">
                    {{ $category->name }}
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($category->products as $product)
                        @include('customer._product-card', ['product' => $product, 'restaurant' => $restaurant])
                    @endforeach
                </div>
            </section>
            @endif
        @endforeach
    </div>
</div>

@endsection

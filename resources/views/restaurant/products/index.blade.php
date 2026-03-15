@extends('layouts.restaurant')
@section('title','Products')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Products</h2>
    <a href="{{ route('restaurant.products.create') }}"
       class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Product
    </a>
</div>

{{-- فلتر البحث --}}
<form method="GET" class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
           class="border border-gray-200 rounded-xl px-4 py-2 text-sm flex-1 min-w-40 focus:outline-none focus:ring-2 focus:ring-orange-400">
    <select name="category" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none">
        <option value="">All Categories</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none">
        <option value="">All Status</option>
        <option value="available"   {{ request('status')==='available'   ? 'selected' : '' }}>Available</option>
        <option value="unavailable" {{ request('status')==='unavailable' ? 'selected' : '' }}>Unavailable</option>
    </select>
    <button class="bg-gray-800 text-white px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-700 transition">Filter</button>
    @if(request()->hasAny(['search','category','status']))
    <a href="{{ route('restaurant.products.index') }}" class="border border-gray-200 text-gray-500 px-4 py-2 rounded-xl text-sm hover:bg-gray-50">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Available</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" class="w-10 h-10 rounded-xl object-cover">
                        @else
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $product->name }}</p>
                            @if($product->is_featured)
                            <span class="text-xs text-orange-500">⭐ Featured</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-500 text-xs">{{ $product->category->name ?? '—' }}</td>
                <td class="px-6 py-4">
                    <p class="font-semibold text-gray-800">${{ number_format($product->price,2) }}</p>
                    @if($product->sale_price)
                    <p class="text-xs text-green-600">Sale: ${{ number_format($product->sale_price,2) }}</p>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <form method="POST" action="{{ route('restaurant.products.toggle-available',$product) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                       {{ $product->is_available ? 'bg-green-500' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform
                                         {{ $product->is_available ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center gap-3 justify-end">
                        <a href="{{ route('restaurant.products.edit',$product) }}" class="text-xs text-gray-500 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('restaurant.products.destroy',$product) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">No products yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $products->links() }}</div>
    @endif
</div>
@endsection

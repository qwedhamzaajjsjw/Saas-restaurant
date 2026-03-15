@extends('layouts.restaurant')
@section('title','New Category')
@section('content')
<div class="max-w-lg">
    <a href="{{ route('restaurant.categories.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Back
    </a>
    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">New Category</h2>
        <form method="POST" action="{{ route('restaurant.categories.store') }}" class="space-y-5">
            @csrf 
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Menu *</label>
                <select name="menu_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Select menu</option>
                    @foreach($menus as $menu)
                        <option value="{{ $menu->id }}" {{ old('menu_id', ) == $menu->id ? 'selected' : '' }}>{{ $menu->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Category Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order') ?? 0 }}" min="0"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded">
                <span class="text-sm text-gray-700">Active</span>
            </label>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('restaurant.categories.index') }}" class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">New Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

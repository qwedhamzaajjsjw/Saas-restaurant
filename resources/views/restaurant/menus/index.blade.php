@extends('layouts.restaurant')
@section('title','Menus')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Menus</h2>
    <a href="{{ route('restaurant.menus.create') }}"
       class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Menu
    </a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($menus as $menu)
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $menu->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $menu->categories_count }} categories</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $menu->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        @if($menu->description)
        <p class="text-sm text-gray-500 mb-4">{{ Str::limit($menu->description,60) }}</p>
        @endif
        <div class="flex gap-2">
            <a href="{{ route('restaurant.menus.edit',$menu) }}"
               class="flex-1 text-center border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm py-2 rounded-xl transition">Edit</a>
            <form method="POST" action="{{ route('restaurant.menus.destroy',$menu) }}" onsubmit="return confirm('Delete this menu?')">
                @csrf @method('DELETE')
                <button class="text-sm text-red-400 hover:text-red-600 px-3 py-2">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center py-16 text-gray-400">
        <p class="text-lg mb-2">No menus yet</p>
        <a href="{{ route('restaurant.menus.create') }}" class="text-orange-500 hover:underline text-sm">Create your first menu</a>
    </div>
    @endforelse
</div>
@endsection

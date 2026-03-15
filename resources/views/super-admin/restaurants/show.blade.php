@extends('layouts.admin')
@section('title', $restaurant->name)

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.restaurants.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg> Back
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-2xl font-bold text-orange-600">
                    {{ strtoupper(substr($restaurant->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $restaurant->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $restaurant->email }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.restaurants.edit', $restaurant) }}"
                   class="border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-xl transition">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.restaurants.toggle-status', $restaurant) }}">
                    @csrf @method('PATCH')
                    <button class="text-sm font-medium px-4 py-2 rounded-xl transition
                        {{ $restaurant->status === 'active' ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                        {{ $restaurant->status === 'active' ? 'Suspend' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            @foreach([['label'=>'Total Orders','value'=>$ordersCount],['label'=>'Products','value'=>$productsCount],['label'=>'Revenue','value'=>'$'.number_format($revenue,2)]] as $s)
            <div class="bg-gray-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-gray-800">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $s['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Info --}}
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-400">Status: </span><span class="font-medium">{{ ucfirst($restaurant->status) }}</span></div>
            <div><span class="text-gray-400">Plan: </span><span class="font-medium">{{ $restaurant->plan?->name ?? '—' }}</span></div>
            <div><span class="text-gray-400">Phone: </span><span class="font-medium">{{ $restaurant->phone ?? '—' }}</span></div>
            <div><span class="text-gray-400">City: </span><span class="font-medium">{{ $restaurant->city ?? '—' }}</span></div>
            <div><span class="text-gray-400">Owner: </span><span class="font-medium">{{ $restaurant->owner?->name ?? '—' }}</span></div>
            <div><span class="text-gray-400">Registered: </span><span class="font-medium">{{ $restaurant->created_at->format('M d, Y') }}</span></div>
        </div>
    </div>
</div>
@endsection

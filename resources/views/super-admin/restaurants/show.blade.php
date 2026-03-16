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
                @if($restaurant->logo)
                    <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}"
                         class="w-16 h-16 rounded-2xl object-cover border border-gray-200 shadow-sm">
                @else
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center text-2xl font-bold text-orange-600">
                        {{ strtoupper(substr($restaurant->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $restaurant->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $restaurant->email }}</p>
                    {{-- Storefront URL --}}
                    <a href="{{ $restaurant->storefront_url }}" target="_blank"
                       class="inline-flex items-center gap-1 text-xs text-blue-500 hover:text-blue-700 mt-1 font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        {{ $restaurant->storefront_url }}
                    </a>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap justify-end">
                {{-- Dashboard Button --}}
                <a href="{{ route('admin.restaurants.login-as', $restaurant) }}"
                   class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                {{-- Preview Storefront Button --}}
                <a href="{{ $restaurant->storefront_url }}" target="_blank"
                   class="inline-flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-sm font-medium px-4 py-2 rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Preview
                </a>
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

    {{-- Restaurant Links Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Restaurant Links
        </h3>
        <div class="space-y-3">
            {{-- Dashboard Login Link --}}
            <div class="flex items-center justify-between bg-orange-50 rounded-xl px-4 py-3">
                <div>
                    <p class="text-xs font-semibold text-orange-700">Restaurant Dashboard</p>
                    <p class="text-xs text-orange-500 mt-0.5">{{ url('/restaurant/login') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="copyToClipboard('{{ url('/restaurant/login') }}', this)"
                            class="text-xs text-orange-600 hover:text-orange-800 border border-orange-200 bg-white px-2.5 py-1 rounded-lg transition">
                        Copy
                    </button>
                    <a href="{{ route('restaurant.login') }}" target="_blank"
                       class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-2.5 py-1 rounded-lg transition">
                        Open
                    </a>
                </div>
            </div>

            {{-- Storefront Link --}}
            <div class="flex items-center justify-between bg-blue-50 rounded-xl px-4 py-3">
                <div>
                    <p class="text-xs font-semibold text-blue-700">Customer Storefront</p>
                    <p class="text-xs text-blue-500 mt-0.5">{{ $restaurant->storefront_url }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="copyToClipboard('{{ $restaurant->storefront_url }}', this)"
                            class="text-xs text-blue-600 hover:text-blue-800 border border-blue-200 bg-white px-2.5 py-1 rounded-lg transition">
                        Copy
                    </button>
                    <a href="{{ $restaurant->storefront_url }}" target="_blank"
                       class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-2.5 py-1 rounded-lg transition">
                        Open
                    </a>
                </div>
            </div>

            {{-- Owner info --}}
            @if($restaurant->owner)
            <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                <div>
                    <p class="text-xs font-semibold text-gray-700">Owner Login Email</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $restaurant->owner->email }}</p>
                </div>
                <button onclick="copyToClipboard('{{ $restaurant->owner->email }}', this)"
                        class="text-xs text-gray-600 hover:text-gray-800 border border-gray-200 bg-white px-2.5 py-1 rounded-lg transition">
                    Copy
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const original = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.add('bg-green-100', 'text-green-700');
        setTimeout(() => {
            btn.textContent = original;
            btn.classList.remove('bg-green-100', 'text-green-700');
        }, 2000);
    });
}
</script>
@endpush

@endsection

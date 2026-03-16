<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ auth()->user()->restaurant->name ?? 'Restaurant' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col h-screen overflow-hidden font-sans">

@if(session('returning_admin_id'))
<div class="bg-indigo-700 text-white text-sm flex items-center justify-between px-6 py-2 flex-shrink-0 z-50">
    <div class="flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span>Super Admin Preview Mode — viewing as <strong>{{ auth()->user()->name }}</strong></span>
    </div>
    <a href="{{ route('auth.admin-return') }}"
       class="bg-white text-indigo-700 font-semibold text-xs px-3 py-1 rounded-lg hover:bg-indigo-50 transition">
        ← Return to Admin Panel
    </a>
</div>
@endif

{{-- ── Sidebar + Main wrapper ───────────────────────────────────────── --}}
<div class="flex flex-1 overflow-hidden">

{{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 shadow-sm">

    {{-- Logo --}}
    <div class="p-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-bold text-sm text-gray-800 leading-tight truncate">
                    {{ auth()->user()->restaurant->name ?? 'My Restaurant' }}
                </p>
                <p class="text-xs text-orange-500">Owner Panel</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 py-4 px-3 space-y-1 overflow-y-auto">
        @php
        $navItems = [
            ['route'=>'restaurant.dashboard',         'label'=>'Dashboard',   'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route'=>'restaurant.menus.index',       'label'=>'Menus',       'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['route'=>'restaurant.categories.index',  'label'=>'Categories',  'icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
            ['route'=>'restaurant.products.index',    'label'=>'Products',    'icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['route'=>'restaurant.orders.index',      'label'=>'Orders',      'icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
            ['route'=>'restaurant.settings.index',    'label'=>'Settings',    'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['route'=>'restaurant.subscription.show', 'label'=>'Subscription', 'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        ];
        @endphp

        @foreach($navItems as $item)
            @php $active = request()->routeIs(str_replace('.index','.*',$item['route'])); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ $active ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
                @if($item['route'] === 'restaurant.orders.index')
                    @php $pendingCount = auth()->user()->restaurant?->orders()->where('status','pending')->count() ?? 0; @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">
                            {{ $pendingCount }}
                        </span>
                    @endif
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Subscription status widget --}}
    @php
        $activeSub = auth()->user()->restaurant?->subscriptions()
            ->with('plan')
            ->where('status','active')
            ->where(function($q){ $q->whereNull('ends_at')->orWhere('ends_at','>',now()); })
            ->latest('starts_at')->first();
    @endphp
    @if($activeSub)
    <div class="mx-3 mb-3 bg-orange-50 border border-orange-100 rounded-xl px-3 py-2.5">
        <p class="text-xs font-bold text-orange-600 truncate">{{ $activeSub->plan->name }}</p>
        @if($activeSub->ends_at)
            @php $daysLeft = (int) now()->diffInDays($activeSub->ends_at, false); @endphp
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $daysLeft > 0 ? $daysLeft.' days left' : 'Expired' }}
            </p>
        @else
            <p class="text-xs text-gray-400 mt-0.5">Active</p>
        @endif
    </div>
    @else
    <div class="mx-3 mb-3 bg-red-50 border border-red-100 rounded-xl px-3 py-2.5">
        <p class="text-xs font-bold text-red-600">No Active Plan</p>
        <a href="{{ route('restaurant.subscription.expired') }}" class="text-xs text-red-400 hover:underline">View details</a>
    </div>
    @endif

    {{-- User info --}}
    <div class="p-4 border-t border-gray-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-sm font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left text-xs text-gray-400 hover:text-gray-700 transition flex items-center gap-2 px-1 py-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

{{-- ── Main ─────────────────────────────────────────────────────────── --}}
<div class="flex-1 flex flex-col overflow-hidden">
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
        <h1 class="text-xl font-bold text-gray-800">@yield('title','Dashboard')</h1>
        <div class="flex items-center gap-3">
            @php $restaurant = auth()->user()->restaurant; @endphp
            @if($restaurant)
            <span class="text-xs px-3 py-1 rounded-full font-medium
                {{ $restaurant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ ucfirst($restaurant->status) }}
            </span>
            <span class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full font-medium">
                {{ $restaurant->plan?->name ?? 'No Plan' }}
            </span>
            @endif
        </div>
    </header>

    {{-- Flash --}}
    <div class="px-8 pt-4 space-y-2">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif
    </div>

    <main class="flex-1 overflow-y-auto p-8">
        @yield('content')
    </main>
</div>

</div>{{-- end sidebar+main wrapper --}}

@stack('scripts')
</body>
</html>

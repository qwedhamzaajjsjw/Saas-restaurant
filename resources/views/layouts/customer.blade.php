<!DOCTYPE html>
<html lang="ar" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $restaurant->name ?? 'Menu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --brand: {{ $restaurant->primary_color ?? '#f97316' }}; }
        .btn-brand  { background-color: var(--brand); color: #fff; }
        .text-brand { color: var(--brand); }
        .border-brand { border-color: var(--brand); }
        .ring-brand   { --tw-ring-color: var(--brand); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

{{-- ── Top nav ─────────────────────────────────────────────────────── --}}
<header class="bg-white shadow-sm sticky top-0 z-30">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between gap-4">

        {{-- Logo + name --}}
        <a href="{{ route('customer.index', $restaurant->slug) }}" class="flex items-center gap-3 min-w-0">
            @if($restaurant->logo)
                <img src="{{ asset('storage/'.$restaurant->logo) }}" alt="{{ $restaurant->name }}"
                     class="w-10 h-10 rounded-full object-cover flex-shrink-0">
            @else
                <div class="w-10 h-10 rounded-full btn-brand flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ strtoupper(substr($restaurant->name,0,1)) }}
                </div>
            @endif
            <span class="font-bold text-gray-800 text-lg leading-tight truncate">{{ $restaurant->name }}</span>
        </a>

        {{-- Nav links --}}
        <nav class="hidden sm:flex items-center gap-5 text-sm font-medium text-gray-600">
            <a href="{{ route('customer.index', $restaurant->slug) }}"
               class="hover:text-brand transition {{ request()->routeIs('customer.index') ? 'text-brand' : '' }}">Home</a>
            <a href="{{ route('customer.menu', $restaurant->slug) }}"
               class="hover:text-brand transition {{ request()->routeIs('customer.menu') ? 'text-brand' : '' }}">Menu</a>
        </nav>

        {{-- Cart button --}}
        <a href="{{ route('customer.cart.index', $restaurant->slug) }}"
           class="relative flex items-center gap-2 btn-brand px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:opacity-90 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span id="cart-count" class="font-bold">{{ isset($cart) ? $cart->count() : 0 }}</span>
        </a>
    </div>
</header>

{{-- Flash messages --}}
<div class="max-w-5xl mx-auto px-4 mt-4 space-y-2">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif
</div>

{{-- Page content --}}
<main class="max-w-5xl mx-auto px-4 py-6">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="text-center text-xs text-gray-400 py-8">
    &copy; {{ date('Y') }} {{ $restaurant->name }}. Powered by Restaurant SaaS.
</footer>

@stack('scripts')
</body>
</html>

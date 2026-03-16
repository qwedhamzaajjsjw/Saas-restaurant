<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $rid = $restaurant->id;
        $metaTitle = \App\Models\Setting::get('meta_title', null, $rid);
        $metaDesc  = \App\Models\Setting::get('meta_description', null, $rid);
        $metaKeys  = \App\Models\Setting::get('meta_keywords', null, $rid);
    @endphp

    <title>{{ $metaTitle ?: (@yield('title', $restaurant->name)) }}</title>
    @if($metaDesc)  <meta name="description" content="{{ $metaDesc }}"> @endif
    @if($metaKeys)  <meta name="keywords"    content="{{ $metaKeys }}"> @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { --brand: {{ $restaurant->primary_color ?? '#f97316' }}; }
        .btn-brand    { background-color: var(--brand); color: #fff; }
        .text-brand   { color: var(--brand); }
        .border-brand { border-color: var(--brand); }
        .ring-brand   { --tw-ring-color: var(--brand); }
        .hover\:text-brand:hover { color: var(--brand); }
        .focus\:ring-brand:focus { --tw-ring-color: var(--brand); }
        .peer:checked ~ .peer-checked\:bg-brand { background-color: var(--brand); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

{{-- ── Announcement Bar ────────────────────────────────────────────── --}}
@php
    $annEnabled = \App\Models\Setting::get('announcement_enabled', false, $restaurant->id);
    $annText    = \App\Models\Setting::get('announcement_text', '', $restaurant->id);
    $annColor   = \App\Models\Setting::get('announcement_color', 'bg-orange-500', $restaurant->id);
@endphp
@if($annEnabled && $annText)
<div class="{{ $annColor }} text-white text-center text-xs font-medium py-2 px-4">
    {{ $annText }}
</div>
@endif

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
@php
    $footerText = \App\Models\Setting::get('footer_text', '', $restaurant->id);
    $whatsapp   = \App\Models\Setting::get('whatsapp',   '', $restaurant->id);
    $instagram  = \App\Models\Setting::get('instagram',  '', $restaurant->id);
    $facebook   = \App\Models\Setting::get('facebook',   '', $restaurant->id);
    $twitter    = \App\Models\Setting::get('twitter',    '', $restaurant->id);
    $hasSocial  = $whatsapp || $instagram || $facebook || $twitter;
@endphp
<footer class="bg-white border-t border-gray-100 mt-12">
    <div class="max-w-5xl mx-auto px-4 py-8">
        {{-- Social icons --}}
        @if($hasSocial)
        <div class="flex justify-center gap-4 mb-4">
            @if($whatsapp)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank"
               class="w-9 h-9 bg-green-500 text-white rounded-full flex items-center justify-center hover:opacity-80 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            @endif
            @if($instagram)
            <a href="{{ $instagram }}" target="_blank"
               class="w-9 h-9 text-white rounded-full flex items-center justify-center hover:opacity-80 transition"
               style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0 10.838c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            @endif
            @if($facebook)
            <a href="{{ $facebook }}" target="_blank"
               class="w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center hover:opacity-80 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            @endif
            @if($twitter)
            <a href="{{ $twitter }}" target="_blank"
               class="w-9 h-9 bg-black text-white rounded-full flex items-center justify-center hover:opacity-80 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            @endif
        </div>
        @endif

        <p class="text-center text-xs text-gray-400">
            {{ $footerText ?: ('© '.date('Y').' '.$restaurant->name.'. All rights reserved.') }}
        </p>
        @if($restaurant->phone || $restaurant->address)
        <p class="text-center text-xs text-gray-300 mt-1">
            {{ $restaurant->address }}{{ $restaurant->phone ? ' · '.$restaurant->phone : '' }}
        </p>
        @endif
    </div>
</footer>

@stack('scripts')
</body>
</html>

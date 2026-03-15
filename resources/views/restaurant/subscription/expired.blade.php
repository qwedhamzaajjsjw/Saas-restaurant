@extends('layouts.restaurant')

@section('title', 'Subscription Expired')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- ── Warning banner ─────────────────────────────────────────────────── --}}
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8 flex items-start gap-4">
        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-red-700 mb-1">Your Subscription Has Expired</h1>
            <p class="text-red-600 text-sm">
                Your subscription for <strong>{{ $restaurant->name }}</strong> has expired or is no longer active.
                Your dashboard access is limited. Please contact the platform administrator to renew your subscription.
            </p>
        </div>
    </div>

    {{-- ── What you can still do ──────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8">
        <h2 class="font-bold text-gray-800 mb-4">What you can still access</h2>
        <ul class="space-y-2">
            <li class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                View this subscription page
            </li>
            <li class="flex items-center gap-3 text-sm text-gray-600">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Update restaurant settings and profile
            </li>
        </ul>

        <hr class="my-4 border-gray-100">
        <h2 class="font-bold text-gray-800 mb-4">What's blocked until renewal</h2>
        <ul class="space-y-2">
            @foreach(['Dashboard & Analytics', 'Menu & Category management', 'Product management', 'Order management'] as $blocked)
            <li class="flex items-center gap-3 text-sm text-gray-400">
                <svg class="w-5 h-5 text-red-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $blocked }}
            </li>
            @endforeach
        </ul>
    </div>

    {{-- ── Plans ──────────────────────────────────────────────────────────── --}}
    <h2 class="text-lg font-bold text-gray-800 mb-4">Available Plans</h2>
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:border-orange-200 transition">
            <h3 class="font-bold text-gray-800 mb-1">{{ $plan->name }}</h3>
            <p class="text-xl font-bold text-orange-500 mb-3">
                {{ $plan->isFree() ? 'Free' : '$'.number_format($plan->price, 2) }}
                @if(! $plan->isFree()) <span class="text-xs font-normal text-gray-400">/mo</span> @endif
            </p>
            <ul class="space-y-1 text-xs text-gray-500">
                <li>{{ $plan->max_products ? $plan->max_products.' products' : '∞ products' }}</li>
                <li>{{ $plan->max_categories ? $plan->max_categories.' categories' : '∞ categories' }}</li>
                <li>{{ $plan->max_orders_per_month ? $plan->max_orders_per_month.' orders/mo' : '∞ orders' }}</li>
            </ul>
        </div>
        @endforeach
    </div>

    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 text-center">
        <p class="text-gray-700 font-medium mb-1">Ready to renew?</p>
        <p class="text-sm text-gray-500 mb-4">
            Contact your platform administrator to renew or upgrade your subscription.
        </p>
        <a href="{{ route('restaurant.settings.index') }}"
           class="inline-block bg-orange-500 text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:bg-orange-600 transition">
            Go to Settings
        </a>
    </div>

</div>

@endsection

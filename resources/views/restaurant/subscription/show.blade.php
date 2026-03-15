@extends('layouts.restaurant')

@section('title', 'My Subscription')

@section('content')

{{-- ── Current Plan Header ─────────────────────────────────────────────── --}}
<div class="grid lg:grid-cols-3 gap-6 mb-8">

    {{-- Plan card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 h-full">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Current Plan</p>

            @if($stats['plan'])
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $stats['plan']->name }}</h2>
                        <p class="text-sm text-gray-500">
                            {{ $stats['plan']->isFree() ? 'Free' : '$'.number_format($stats['plan']->price, 2).'/mo' }}
                        </p>
                    </div>
                </div>

                {{-- Subscription dates --}}
                @if($stats['subscription'])
                    <div class="space-y-2 text-sm border-t border-gray-50 pt-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Started</span>
                            <span class="font-medium text-gray-700">{{ $stats['subscription']->starts_at->format('d M Y') }}</span>
                        </div>
                        @if($stats['subscription']->ends_at)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Expires</span>
                            <span class="font-medium {{ $stats['days_remaining'] <= 7 ? 'text-red-600' : 'text-gray-700' }}">
                                {{ $stats['subscription']->ends_at->format('d M Y') }}
                            </span>
                        </div>
                        @if($stats['days_remaining'] !== null)
                        <div class="mt-2 rounded-lg px-3 py-2 text-xs font-medium text-center
                            {{ $stats['days_remaining'] <= 7 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                            @if($stats['days_remaining'] > 0)
                                {{ $stats['days_remaining'] }} days remaining
                            @else
                                Expired
                            @endif
                        </div>
                        @endif
                        @else
                        <div class="flex justify-between">
                            <span class="text-gray-400">Expires</span>
                            <span class="font-medium text-green-600">Never (Lifetime)</span>
                        </div>
                        @endif
                    </div>
                @endif

                {{-- Plan features --}}
                @if($stats['plan']->features)
                    <ul class="mt-4 space-y-1.5">
                        @foreach($stats['plan']->features as $feature)
                            <li class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

            @else
                <p class="text-gray-400 text-sm">No active subscription found.</p>
            @endif
        </div>
    </div>

    {{-- Usage stats --}}
    <div class="lg:col-span-2 grid sm:grid-cols-3 gap-4">

        @php
            $usageItems = [
                [
                    'label'   => 'Products',
                    'icon'    => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'data'    => $stats['products'],
                    'color'   => 'blue',
                ],
                [
                    'label'   => 'Categories',
                    'icon'    => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
                    'data'    => $stats['categories'],
                    'color'   => 'purple',
                ],
                [
                    'label'   => 'Orders (this month)',
                    'icon'    => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                    'data'    => $stats['orders'],
                    'color'   => 'orange',
                ],
            ];
        @endphp

        @foreach($usageItems as $item)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-{{ $item['color'] }}-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $item['color'] }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-500">{{ $item['label'] }}</p>
            </div>

            <p class="text-2xl font-bold text-gray-800 mb-1">
                {{ $item['data']['used'] }}
                @if($item['data']['limit'])
                    <span class="text-sm font-normal text-gray-400">/ {{ $item['data']['limit'] }}</span>
                @else
                    <span class="text-sm font-normal text-gray-400">/ ∞</span>
                @endif
            </p>

            @if($item['data']['limit'])
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="h-1.5 rounded-full transition-all
                        {{ $item['data']['percentage'] >= 90 ? 'bg-red-500' : ($item['data']['percentage'] >= 70 ? 'bg-yellow-500' : 'bg-'.$item['color'].'-500') }}"
                         style="width: {{ $item['data']['percentage'] }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $item['data']['percentage'] }}% used</p>
            @else
                <p class="text-xs text-green-600 mt-1 font-medium">Unlimited</p>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ── Available Plans ──────────────────────────────────────────────────── --}}
<div>
    <h2 class="text-lg font-bold text-gray-800 mb-4">Available Plans</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($plans as $plan)
        @php $isCurrent = $stats['plan']?->id === $plan->id; @endphp
        <div class="bg-white rounded-2xl border-2 p-5
            {{ $isCurrent ? 'border-orange-300 ring-2 ring-orange-100' : 'border-gray-100 hover:border-gray-200' }}
            transition relative">

            @if($isCurrent)
                <span class="absolute top-3 right-3 text-xs bg-orange-100 text-orange-600 font-semibold px-2 py-0.5 rounded-full">
                    Current
                </span>
            @endif

            <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $plan->name }}</h3>
            <p class="text-2xl font-bold text-orange-500 mb-3">
                {{ $plan->isFree() ? 'Free' : '$'.number_format($plan->price, 2) }}
                @if(! $plan->isFree())
                    <span class="text-sm font-normal text-gray-400">/month</span>
                @endif
            </p>

            <ul class="space-y-1.5 mb-5">
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    {{ $plan->max_products ? $plan->max_products.' products' : 'Unlimited products' }}
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ $plan->max_categories ? $plan->max_categories.' categories' : 'Unlimited categories' }}
                </li>
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    {{ $plan->max_orders_per_month ? $plan->max_orders_per_month.' orders/month' : 'Unlimited orders' }}
                </li>
                @foreach($plan->features ?? [] as $feature)
                <li class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>

            @if($isCurrent)
                <div class="w-full text-center py-2 rounded-xl text-sm font-medium text-gray-400 bg-gray-50 border border-gray-100">
                    Active Plan
                </div>
            @else
                <button disabled
                        class="w-full py-2 rounded-xl text-sm font-semibold bg-orange-500 text-white opacity-60 cursor-not-allowed"
                        title="Contact admin to change plan">
                    Upgrade (Contact Admin)
                </button>
            @endif
        </div>
        @endforeach
    </div>
    <p class="text-xs text-gray-400 mt-3">To change your plan, please contact the platform administrator.</p>
</div>

@endsection

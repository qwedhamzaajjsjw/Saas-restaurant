@extends('layouts.installer')
@section('title', 'Installation Complete')
@php $step = 5; @endphp

@section('content')
<div class="text-center">

    {{-- Success icon --}}
    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-2">Installation Complete!</h2>
    <p class="text-gray-500 mb-8">
        Your Restaurant SaaS platform is ready to use.<br>
        The installer has been locked and cannot be accessed again.
    </p>

    {{-- What was done --}}
    <div class="bg-gray-50 rounded-xl p-5 mb-8 text-left space-y-3">
        @foreach([
            ['color' => 'green', 'text' => 'Server requirements verified'],
            ['color' => 'green', 'text' => 'Database configured and .env file created'],
            ['color' => 'green', 'text' => 'All migrations executed successfully'],
            ['color' => 'green', 'text' => 'Default subscription plans seeded'],
            ['color' => 'green', 'text' => 'Super Admin account created'],
        ] as $item)
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-{{ $item['color'] }}-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm text-gray-700">{{ $item['text'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- CTA --}}
    @auth
        <a href="{{ route('admin.dashboard') }}"
           class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition text-base">
            Go to Admin Dashboard
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    @else
        <a href="{{ route('login') }}"
           class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition text-base">
            Login to Dashboard
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    @endauth
</div>
@endsection

@extends('layouts.auth')
@section('title', 'Forgot Password')
@section('subtitle', 'Reset your password')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Forgot your password?</h2>
    <p class="text-gray-500 text-sm mt-1">
        No problem. Enter your email and we'll send you a reset link.
    </p>
</div>

<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
            Email Address
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   autofocus autocomplete="email"
                   placeholder="you@example.com"
                   class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        </div>
        @error('email')
            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600
                   text-white font-semibold py-2.5 rounded-xl transition-colors duration-200 text-sm">
        Send Reset Link
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </button>
</form>

<div class="mt-6 text-center">
    <a href="{{ route('login') }}"
       class="text-sm text-orange-500 hover:text-orange-600 font-medium flex items-center justify-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Sign In
    </a>
</div>

@endsection

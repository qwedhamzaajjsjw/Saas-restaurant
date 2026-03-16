@extends('layouts.auth')
@section('title', 'Restaurant Login')
@section('subtitle', 'Sign in to your restaurant dashboard')

@section('content')

<div class="flex items-center justify-center mb-4">
    <span class="inline-flex items-center gap-2 bg-orange-50 text-orange-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-orange-200">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
        </svg>
        Restaurant Owner Portal
    </span>
</div>

<h2 class="text-xl font-bold text-gray-800 mb-1 text-center">Welcome back</h2>
<p class="text-sm text-gray-400 text-center mb-6">Access your restaurant management dashboard</p>

<form method="POST" action="{{ route('login.post') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="portal" value="restaurant">

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
            Email Address
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
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
            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <a href="{{ route('password.request') }}"
               class="text-xs text-orange-500 hover:text-orange-600 font-medium">
                Forgot password?
            </a>
        </div>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <input id="password" type="password" name="password"
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full pl-10 pr-12 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            <button type="button" onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="flex items-center">
        <input id="remember" type="checkbox" name="remember"
               class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-400">
        <label for="remember" class="ml-2 text-sm text-gray-600">Keep me signed in</label>
    </div>

    {{-- Submit --}}
    <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600
                   text-white font-semibold py-2.5 rounded-xl transition-colors duration-200 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
        Sign In to Dashboard
    </button>
</form>

{{-- Footer note --}}
<div class="mt-6 pt-5 border-t border-gray-100 text-center">
    <p class="text-xs text-gray-400">
        Are you a platform admin?
        <a href="{{ route('login') }}" class="text-orange-500 hover:text-orange-600 font-medium">
            Admin Login
        </a>
    </p>
</div>

@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush

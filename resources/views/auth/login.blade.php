@extends('layouts.auth')
@section('title', 'Sign In')
@section('subtitle', 'Sign in to your account')

@section('content')

<h2 class="text-xl font-bold text-gray-800 mb-6">Welcome back</h2>

<form method="POST" action="{{ route('login.post') }}" class="space-y-5">
    @csrf

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
            {{-- Toggle visibility --}}
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
        Sign In
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
</form>

{{-- Role hint --}}
<div class="mt-6 pt-5 border-t border-gray-100">
    <p class="text-xs text-center text-gray-400">
        This platform serves Super Admins and Restaurant Owners.<br>
        Customer accounts are created during checkout.
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

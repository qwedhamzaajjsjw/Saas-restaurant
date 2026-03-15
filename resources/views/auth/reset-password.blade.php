@extends('layouts.auth')
@section('title', 'Reset Password')
@section('subtitle', 'Create a new password')

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Set new password</h2>
    <p class="text-gray-500 text-sm mt-1">Choose a strong password of at least 8 characters.</p>
</div>

<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    {{-- Email --}}
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
        <input id="email" type="email" name="email"
               value="{{ old('email', $email) }}"
               autocomplete="email" readonly
               class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-sm text-gray-500 cursor-not-allowed">
        @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
    </div>

    {{-- New Password --}}
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <input id="password" type="password" name="password"
                   placeholder="Min. 8 characters"
                   class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        </div>
        @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
    </div>

    {{-- Confirm Password --}}
    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
            Confirm New Password
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   placeholder="Repeat new password"
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
    </div>

    {{-- Strength indicator --}}
    <div>
        <div class="flex gap-1 mb-1" id="strength-bars">
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar-1"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar-2"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar-3"></div>
            <div class="h-1 flex-1 rounded-full bg-gray-200" id="bar-4"></div>
        </div>
        <p class="text-xs text-gray-400" id="strength-text">Enter a password</p>
    </div>

    <button type="submit"
            class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600
                   text-white font-semibold py-2.5 rounded-xl transition-colors duration-200 text-sm">
        Reset Password
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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

@push('scripts')
<script>
document.getElementById('password').addEventListener('input', function () {
    const val = this.value;
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['', 'bg-red-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-500'];
    const labels = ['Enter a password', 'Weak', 'Fair', 'Good', 'Strong'];

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('bar-' + i);
        bar.className = 'h-1 flex-1 rounded-full ' + (i <= score ? colors[score] : 'bg-gray-200');
    }
    document.getElementById('strength-text').textContent = labels[score];
});
</script>
@endpush

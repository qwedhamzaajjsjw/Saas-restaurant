@extends('layouts.installer')
@section('title', 'Step 4 – Admin Account')
@php $step = 4; @endphp

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-1">Create Super Admin Account</h2>
<p class="text-gray-500 text-sm mb-6">This account will have full control over the platform.</p>

<form method="POST" action="{{ route('installer.admin.save') }}" class="space-y-5">
    @csrf

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}"
               placeholder="John Smith"
               class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                      {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}"
               placeholder="admin@yourdomain.com"
               class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                      {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Password --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <input type="password" name="password"
                   placeholder="Min. 8 characters"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   placeholder="Repeat password"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 border-gray-300">
        </div>
    </div>

    {{-- Info box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex gap-2">
        <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <p class="text-blue-700 text-xs">
            After this step, the installer will be locked and you will be automatically logged into the admin dashboard.
        </p>
    </div>

    <div class="pt-2 flex gap-3">
        <a href="{{ route('installer.migrate') }}"
           class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-3 rounded-xl transition text-sm">
            Back
        </a>
        <button type="submit"
                class="flex-1 flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
            Create Account & Finish
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</form>
@endsection

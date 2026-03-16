@extends('layouts.installer')
@section('title', 'Step 2 – Database')
@php $step = 2; @endphp

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-1">Database Configuration</h2>
<p class="text-gray-500 text-sm mb-6">Enter your database credentials. We will write the <code class="bg-gray-100 px-1 rounded">.env</code> file automatically.</p>

<form method="POST" action="{{ route('installer.database.save') }}" class="space-y-5">
    @csrf

    {{-- App Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Application Name</label>
        <input type="text" name="app_name"
               value="{{ old('app_name', $defaults['app_name']) }}"
               placeholder="Restaurant SaaS"
               class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                      {{ $errors->has('app_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('app_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- App URL + Timezone --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Application URL</label>
            <input type="url" name="app_url"
                   value="{{ old('app_url', $defaults['app_url']) }}"
                   placeholder="https://example.com"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('app_url') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            @error('app_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Timezone</label>
            <select name="timezone"
                    class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                           {{ $errors->has('timezone') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                @foreach($timezones as $tz)
                    <option value="{{ $tz }}" {{ old('timezone', $defaults['timezone']) === $tz ? 'selected' : '' }}>
                        {{ $tz }}
                    </option>
                @endforeach
            </select>
            @error('timezone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <hr class="border-gray-100">

    {{-- Host + Port --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Database Host</label>
            <input type="text" name="db_host"
                   value="{{ old('db_host', $defaults['db_host']) }}"
                   placeholder="127.0.0.1"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('db_host') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            @error('db_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Port</label>
            <input type="number" name="db_port"
                   value="{{ old('db_port', $defaults['db_port']) }}"
                   placeholder="3306"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('db_port') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            @error('db_port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- DB Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Database Name</label>
        <input type="text" name="db_name"
               value="{{ old('db_name', $defaults['db_name']) }}"
               placeholder="restaurant_saas"
               class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                      {{ $errors->has('db_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        @error('db_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Username + Password --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
            <input type="text" name="db_user"
                   value="{{ old('db_user', $defaults['db_user']) }}"
                   placeholder="root"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400
                          {{ $errors->has('db_user') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            @error('db_user') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
            <input type="password" name="db_pass"
                   value="{{ old('db_pass', $defaults['db_pass']) }}"
                   placeholder="(leave blank if none)"
                   class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 border-gray-300">
        </div>
    </div>

    <div class="pt-2 flex gap-3">
        <a href="{{ route('installer.index') }}"
           class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-3 rounded-xl transition text-sm">
            Back
        </a>
        <button type="submit"
                class="flex-1 flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
            Test & Save Configuration
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</form>
@endsection

@extends('layouts.installer')
@section('title', 'Step 1 – Requirements')
@php $step = 1; @endphp

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-1">Server Requirements</h2>
<p class="text-gray-500 text-sm mb-6">Checking that your server meets the minimum requirements.</p>

{{-- PHP Version --}}
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">PHP Version</h3>
    <div class="flex items-center justify-between p-3 rounded-xl border
        {{ $php['passed'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex items-center gap-3">
            @if($php['passed'])
                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            @else
                <div class="w-6 h-6 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            @endif
            <span class="text-sm font-medium {{ $php['passed'] ? 'text-green-800' : 'text-red-800' }}">
                {{ $php['label'] }}
            </span>
        </div>
        <span class="text-sm font-mono {{ $php['passed'] ? 'text-green-600' : 'text-red-600' }}">
            {{ $php['current'] }}
        </span>
    </div>
</div>

{{-- Extensions --}}
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">PHP Extensions</h3>
    <div class="grid grid-cols-2 gap-2">
        @foreach($extensions as $ext)
            <div class="flex items-center gap-2 p-2.5 rounded-lg border text-sm
                {{ $ext['passed'] ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                @if($ext['passed'])
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @endif
                <span class="font-mono">{{ $ext['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Folder Permissions --}}
<div class="mb-8">
    <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Folder Permissions</h3>
    <div class="space-y-2">
        @foreach($permissions as $perm)
            <div class="flex items-center justify-between p-2.5 rounded-lg border text-sm
                {{ $perm['passed'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                <div class="flex items-center gap-2">
                    @if($perm['passed'])
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                    <span class="font-mono text-xs {{ $perm['passed'] ? 'text-green-800' : 'text-red-800' }}">
                        {{ $perm['label'] }}
                    </span>
                </div>
                <span class="text-xs font-semibold {{ $perm['passed'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ $perm['passed'] ? 'Writable' : 'Not Writable' }}
                </span>
            </div>
        @endforeach
    </div>
</div>

@if($allPassed)
    <a href="{{ route('installer.database') }}"
       class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition">
        Continue to Database Setup
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
@else
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
        <p class="text-red-700 text-sm font-medium">
            Some requirements are not met. Please fix the issues above, then
            <a href="{{ route('installer.index') }}" class="underline">refresh this page</a>.
        </p>
    </div>
    <a href="{{ route('installer.index') }}"
       class="w-full flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 rounded-xl transition">
        Check Again
    </a>
@endif
@endsection

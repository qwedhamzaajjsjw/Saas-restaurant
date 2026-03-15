@extends('layouts.installer')
@section('title', 'Step 3 – Migrate')
@php $step = 3; @endphp

@section('content')
<h2 class="text-xl font-bold text-gray-800 mb-1">Database Migration</h2>
<p class="text-gray-500 text-sm mb-6">Running migrations and seeding default data.</p>

@if($success)
    {{-- Success state --}}
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl mb-6">
        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-green-800">Migrations completed successfully!</p>
            <p class="text-green-600 text-sm">All tables created and default data seeded.</p>
        </div>
    </div>

    {{-- Migration output --}}
    @if($migrationOutput)
        <div class="mb-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Migration Output</p>
            <pre class="bg-gray-900 text-green-400 text-xs rounded-xl p-4 overflow-auto max-h-40 font-mono">{{ $migrationOutput }}</pre>
        </div>
    @endif

    @if($seedOutput)
        <div class="mb-6">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Seeder Output</p>
            <pre class="bg-gray-900 text-blue-400 text-xs rounded-xl p-4 overflow-auto max-h-24 font-mono">{{ $seedOutput }}</pre>
        </div>
    @endif

    <a href="{{ route('installer.admin') }}"
       class="w-full flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition">
        Continue – Create Admin Account
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
@else
    {{-- Failure state --}}
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl mb-6">
        <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-red-800">Migration failed</p>
            <p class="text-red-600 text-sm">Please check your database credentials and try again.</p>
        </div>
    </div>

    <pre class="bg-gray-900 text-red-400 text-xs rounded-xl p-4 overflow-auto max-h-40 font-mono mb-6">{{ $migrationOutput }}</pre>

    <a href="{{ route('installer.database') }}"
       class="w-full flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-xl transition">
        Back to Database Config
    </a>
@endif
@endsection

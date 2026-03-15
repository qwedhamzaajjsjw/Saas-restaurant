<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Install') — Restaurant SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-orange-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-2xl">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-2xl shadow-lg mb-4">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Restaurant SaaS</h1>
        <p class="text-gray-500 text-sm">Installation Wizard</p>
    </div>

    {{-- Stepper --}}
    @php
        $steps = [1 => 'Requirements', 2 => 'Database', 3 => 'Migrate', 4 => 'Admin Account', 5 => 'Complete'];
        $currentStep = $step ?? 1;
    @endphp
    <div class="flex items-center justify-center mb-6">
        @foreach($steps as $n => $label)
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all
                        {{ $currentStep == $n ? 'bg-orange-500 text-white shadow-md ring-4 ring-orange-200' :
                           ($currentStep > $n  ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500') }}">
                        @if($currentStep > $n)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            {{ $n }}
                        @endif
                    </div>
                    <span class="text-xs mt-1 hidden sm:block font-medium
                        {{ $currentStep == $n ? 'text-orange-600' : ($currentStep > $n ? 'text-green-600' : 'text-gray-400') }}">
                        {{ $label }}
                    </span>
                </div>
                @if($n < 5)
                    <div class="w-10 sm:w-16 h-0.5 mx-1 mb-4
                        {{ $currentStep > $n ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Alert messages --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 flex items-start gap-3">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl px-4 py-3 text-sm">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-xl p-8">
        @yield('content')
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">Restaurant SaaS Platform &copy; {{ date('Y') }}</p>
</div>
</body>
</html>

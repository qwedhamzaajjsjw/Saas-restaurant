<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install – Restaurant SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-2xl">
        {{-- Stepper --}}
        <div class="flex items-center justify-center mb-8 space-x-2 text-sm">
            @foreach([1=>'Requirements', 2=>'Database', 3=>'Migrate', 4=>'Admin', 5=>'Done'] as $n => $label)
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                        {{ isset($step) && $step == $n ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' }}">
                        {{ $n }}
                    </div>
                    <span class="ml-1 hidden sm:inline {{ isset($step) && $step == $n ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">
                        {{ $label }}
                    </span>
                    @if($n < 5) <div class="w-6 h-px bg-gray-300 mx-2"></div> @endif
                </div>
            @endforeach
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') – {{ auth()->user()->restaurant->name ?? 'Restaurant' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-orange-700 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-orange-800">
            {{ auth()->user()->restaurant->name ?? 'My Restaurant' }}
            <span class="block text-xs text-orange-200 font-normal mt-1">Owner Panel</span>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('restaurant.dashboard') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Dashboard
            </a>
            <a href="{{ route('restaurant.menus.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Menus
            </a>
            <a href="{{ route('restaurant.categories.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Categories
            </a>
            <a href="{{ route('restaurant.products.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Products
            </a>
            <a href="{{ route('restaurant.orders.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Orders
            </a>
            <a href="{{ route('restaurant.settings.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-orange-800 transition">
                Settings
            </a>
        </nav>
        <div class="p-4 border-t border-orange-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-orange-200 hover:text-white">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1 overflow-y-auto">
        <header class="bg-white shadow px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            <span class="text-sm text-gray-500">{{ auth()->user()->name }}</span>
        </header>
        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>

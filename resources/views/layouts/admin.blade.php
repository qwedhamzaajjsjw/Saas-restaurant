<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') – Restaurant SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-gray-700">
            Restaurant SaaS
            <span class="block text-xs text-gray-400 font-normal mt-1">Super Admin</span>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Dashboard
            </a>
            <a href="{{ route('admin.restaurants.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Restaurants
            </a>
            <a href="{{ route('admin.plans.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Plans
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Users
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Settings
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-gray-400 hover:text-white">Logout</button>
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

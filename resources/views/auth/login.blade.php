<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login – Restaurant SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Login</h1>
    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf
        @if($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password"
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700">
            Sign In
        </button>
    </form>
</div>
</body>
</html>

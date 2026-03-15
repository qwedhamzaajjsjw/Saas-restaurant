@extends('layouts.admin')
@section('title', 'System Settings')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">System Settings</h2>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Platform Name</label>
                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'Restaurant SaaS') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Support Email</label>
                <input type="email" name="app_email" value="{{ old('app_email', $settings['app_email'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Default Currency</label>
                <input type="text" name="app_currency" value="{{ old('app_currency', $settings['app_currency'] ?? 'USD') }}" maxlength="3"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="registration_open" value="1"
                           {{ ($settings['registration_open'] ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-500 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Open Registration</p>
                        <p class="text-xs text-gray-400">Allow new restaurants to register</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1"
                           {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                           class="w-4 h-4 text-orange-500 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Maintenance Mode</p>
                        <p class="text-xs text-gray-400">Take the platform offline temporarily</p>
                    </div>
                </label>
            </div>
            <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                Save Settings
            </button>
        </form>
    </div>
</div>
@endsection

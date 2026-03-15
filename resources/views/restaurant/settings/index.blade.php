@extends('layouts.restaurant')
@section('title','Restaurant Settings')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Restaurant Settings</h2>
        <form method="POST" action="{{ route('restaurant.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- شعار المطعم --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                @if($restaurant->logo)
                <div class="mb-3">
                    <img src="{{ Storage::url($restaurant->logo) }}" class="w-20 h-20 rounded-2xl object-cover">
                </div>
                @endif
                <input type="file" name="logo" accept="image/*"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Restaurant Name *</label>
                    <input type="text" name="name" value="{{ old('name', $restaurant->name) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $restaurant->email) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $restaurant->city) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Timezone</label>
                    <select name="timezone" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ old('timezone', $restaurant->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address', $restaurant->address) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('description', $restaurant->description) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                        <input type="checkbox" name="accepts_orders" value="1"
                               {{ old('accepts_orders', $restaurant->accepts_orders) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-500 rounded">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Accept Orders</p>
                            <p class="text-xs text-gray-400">Allow customers to place orders through the storefront</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                Save Settings
            </button>
        </form>
    </div>
</div>
@endsection

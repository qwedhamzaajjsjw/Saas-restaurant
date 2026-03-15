@extends('layouts.admin')
@section('title', 'Add Restaurant')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.restaurants.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg> Back to Restaurants
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">New Restaurant</h2>

        <form method="POST" action="{{ route('admin.restaurants.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Restaurant Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Subscription Plan *</label>
                    <select name="plan_id" class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('plan_id') ? 'border-red-400' : 'border-gray-300' }}">
                        <option value="">Select a plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — ${{ number_format($plan->price,2) }}/mo
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <hr class="border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Owner Account</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Owner Name *</label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}"
                           class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('owner_name') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('owner_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Owner Email *</label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}"
                           class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('owner_email') ? 'border-red-400' : 'border-gray-300' }}">
                    @error('owner_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                    <input type="password" name="owner_password"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('owner_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.restaurants.index') }}"
                   class="flex-1 text-center border border-gray-300 text-gray-600 hover:bg-gray-50 font-semibold py-2.5 rounded-xl transition text-sm">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                    Create Restaurant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

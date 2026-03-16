@extends('layouts.admin')
@section('title', 'Edit Restaurant')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.restaurants.index') }}" class="text-sm text-gray-400 hover:text-gray-600 flex items-center gap-1 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg> Back
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Edit: {{ $restaurant->name }}</h2>

        <form method="POST" action="{{ route('admin.restaurants.update', $restaurant) }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- ── Basic Info ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $restaurant->name) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $restaurant->email) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        @foreach(['active','inactive','suspended'] as $s)
                            <option value="{{ $s }}" {{ old('status', $restaurant->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Plan *</label>
                    <select name="plan_id" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $restaurant->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ── Domain Assignment ───────────────────────────────────── --}}
            <hr class="border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Domain Assignment</h3>

            @php
                $currentDomainType = old('domain_type',
                    $restaurant->custom_domain ? 'custom' : ($restaurant->subdomain ? 'subdomain' : 'none')
                );
            @endphp

            {{-- Current URL info --}}
            @if($restaurant->storefront_url)
            <div class="flex items-center gap-2 bg-orange-50 border border-orange-100 rounded-xl px-4 py-3 text-sm">
                <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                <span class="text-gray-600">Current URL:</span>
                <a href="{{ $restaurant->storefront_url }}" target="_blank"
                   class="text-orange-600 hover:underline font-medium truncate">{{ $restaurant->storefront_url }}</a>
            </div>
            @endif

            {{-- Domain Type Tabs --}}
            <div class="flex rounded-xl border border-gray-200 overflow-hidden">
                <button type="button" data-type="subdomain"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium transition
                               {{ $currentDomainType === 'subdomain' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    Subdomain
                    <span class="block text-xs font-normal opacity-75">pizza.yourdomain.com</span>
                </button>
                <button type="button" data-type="custom"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium border-l border-gray-200 transition
                               {{ $currentDomainType === 'custom' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    Custom Domain
                    <span class="block text-xs font-normal opacity-75">pizza-palace.com</span>
                </button>
                <button type="button" data-type="none"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium border-l border-gray-200 transition
                               {{ $currentDomainType === 'none' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    No Domain
                    <span class="block text-xs font-normal opacity-75">yourdomain.com/restaurant/slug</span>
                </button>
            </div>
            <input type="hidden" name="domain_type" id="domain_type" value="{{ $currentDomainType }}">

            {{-- Subdomain Panel --}}
            <div id="panel-subdomain" class="domain-panel {{ $currentDomainType !== 'subdomain' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subdomain Prefix</label>
                <div class="flex items-center border rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-400 {{ $errors->has('subdomain') ? 'border-red-400' : 'border-gray-300' }}">
                    <input type="text" name="subdomain" id="subdomain_input"
                           value="{{ old('subdomain', $restaurant->subdomain) }}"
                           placeholder="pizza"
                           class="flex-1 px-4 py-2.5 text-sm outline-none bg-white">
                    <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-l border-gray-200 whitespace-nowrap">
                        .{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                    </span>
                </div>
                @error('subdomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Custom Domain Panel --}}
            <div id="panel-custom" class="domain-panel {{ $currentDomainType !== 'custom' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Custom Domain</label>
                <input type="text" name="custom_domain" id="custom_domain_input"
                       value="{{ old('custom_domain', $restaurant->custom_domain) }}"
                       placeholder="pizza-palace.com"
                       class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('custom_domain') ? 'border-red-400' : 'border-gray-300' }}">
                @error('custom_domain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- No Domain Panel --}}
            <div id="panel-none" class="domain-panel {{ $currentDomainType !== 'none' ? 'hidden' : '' }}">
                <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-500">
                    Restaurant accessible at:
                    <code class="text-orange-600 ml-1">{{ config('app.url') }}/restaurant/{{ $restaurant->slug }}</code>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.restaurants.index') }}"
                   class="flex-1 text-center border border-gray-300 text-gray-600 font-semibold py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const tabs   = document.querySelectorAll('.domain-tab');
    const panels = document.querySelectorAll('.domain-panel');
    const input  = document.getElementById('domain_type');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const type = tab.dataset.type;
            input.value = type;

            tabs.forEach(t => {
                t.classList.remove('bg-orange-500', 'text-white');
                t.classList.add('bg-white', 'text-gray-500');
            });
            tab.classList.add('bg-orange-500', 'text-white');
            tab.classList.remove('bg-white', 'text-gray-500');

            panels.forEach(p => p.classList.add('hidden'));
            document.getElementById('panel-' + type).classList.remove('hidden');
        });
    });
})();
</script>
@endsection

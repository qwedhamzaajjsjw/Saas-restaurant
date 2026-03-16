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

            {{-- ── Basic Info ──────────────────────────────────────────── --}}
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

            {{-- ── Domain Assignment ───────────────────────────────────── --}}
            <hr class="border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">Domain Assignment</h3>
            <p class="text-xs text-gray-400 -mt-4">Choose how the restaurant's storefront will be accessed by customers.</p>

            {{-- Domain Type Tabs --}}
            <div class="flex rounded-xl border border-gray-200 overflow-hidden" id="domain-tabs">
                <button type="button" data-type="subdomain"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium transition
                               {{ old('domain_type','subdomain') === 'subdomain' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    Subdomain
                    <span class="block text-xs font-normal opacity-75">pizza.yourdomain.com</span>
                </button>
                <button type="button" data-type="custom"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium border-l border-gray-200 transition
                               {{ old('domain_type') === 'custom' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    Custom Domain
                    <span class="block text-xs font-normal opacity-75">pizza-palace.com</span>
                </button>
                <button type="button" data-type="none"
                        class="domain-tab flex-1 py-2.5 text-sm font-medium border-l border-gray-200 transition
                               {{ old('domain_type') === 'none' ? 'bg-orange-500 text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                    No Domain
                    <span class="block text-xs font-normal opacity-75">yourdomain.com/restaurant/slug</span>
                </button>
            </div>
            <input type="hidden" name="domain_type" id="domain_type" value="{{ old('domain_type','subdomain') }}">

            {{-- Subdomain Panel --}}
            <div id="panel-subdomain" class="domain-panel {{ old('domain_type','subdomain') !== 'subdomain' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subdomain Prefix *</label>
                <div class="flex items-center border rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-400 {{ $errors->has('subdomain') ? 'border-red-400' : 'border-gray-300' }}">
                    <input type="text" name="subdomain" id="subdomain_input"
                           value="{{ old('subdomain') }}"
                           placeholder="pizza"
                           class="flex-1 px-4 py-2.5 text-sm outline-none bg-white">
                    <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-l border-gray-200 whitespace-nowrap">
                        .{{ parse_url(config('app.url'), PHP_URL_HOST) }}
                    </span>
                </div>
                @error('subdomain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">
                    Lowercase letters, numbers and hyphens only. Make sure you have a wildcard DNS record
                    <code class="bg-gray-100 px-1 rounded">*.{{ parse_url(config('app.url'), PHP_URL_HOST) }} → Server IP</code>
                </p>
            </div>

            {{-- Custom Domain Panel --}}
            <div id="panel-custom" class="domain-panel {{ old('domain_type') !== 'custom' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Custom Domain *</label>
                <input type="text" name="custom_domain" id="custom_domain_input"
                       value="{{ old('custom_domain') }}"
                       placeholder="pizza-palace.com"
                       class="w-full border rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 {{ $errors->has('custom_domain') ? 'border-red-400' : 'border-gray-300' }}">
                @error('custom_domain')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">
                    The restaurant owner must point their domain's A record to your server IP before this works.
                </p>
            </div>

            {{-- No Domain Panel --}}
            <div id="panel-none" class="domain-panel {{ old('domain_type') !== 'none' ? 'hidden' : '' }}">
                <div class="bg-gray-50 rounded-xl px-4 py-3 text-sm text-gray-500">
                    The restaurant will be accessible at:
                    <code class="text-orange-600 ml-1">{{ config('app.url') }}/restaurant/&lt;slug&gt;</code>
                </div>
            </div>

            {{-- ── Owner Account ───────────────────────────────────────── --}}
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

<script>
(function () {
    const tabs   = document.querySelectorAll('.domain-tab');
    const panels = document.querySelectorAll('.domain-panel');
    const input  = document.getElementById('domain_type');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const type = tab.dataset.type;
            input.value = type;

            // Style tabs
            tabs.forEach(t => {
                t.classList.remove('bg-orange-500', 'text-white');
                t.classList.add('bg-white', 'text-gray-500');
            });
            tab.classList.add('bg-orange-500', 'text-white');
            tab.classList.remove('bg-white', 'text-gray-500');

            // Show/hide panels
            panels.forEach(p => p.classList.add('hidden'));
            document.getElementById('panel-' + type).classList.remove('hidden');

            // Clear unused inputs so they don't get validated
            document.getElementById('subdomain_input').required    = (type === 'subdomain');
            document.getElementById('custom_domain_input').required = (type === 'custom');
        });
    });

    // Set initial required state
    const current = input.value;
    document.getElementById('subdomain_input').required    = (current === 'subdomain');
    document.getElementById('custom_domain_input').required = (current === 'custom');
})();
</script>
@endsection

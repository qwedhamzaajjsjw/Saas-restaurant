@extends('layouts.restaurant')
@section('title', 'Settings')

@section('content')

{{-- ── Storefront link bar ──────────────────────────────────────────────── --}}
<div class="flex items-center justify-between bg-orange-50 border border-orange-200 rounded-2xl px-5 py-3 mb-6">
    <div>
        <p class="text-xs font-semibold text-orange-700 mb-0.5">Your Storefront URL</p>
        <a href="{{ $restaurant->storefront_url }}" target="_blank"
           class="text-sm text-orange-600 hover:underline font-mono">{{ $restaurant->storefront_url }}</a>
    </div>
    <div class="flex gap-2">
        <button onclick="navigator.clipboard.writeText('{{ $restaurant->storefront_url }}').then(()=>{this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)})"
                class="text-xs border border-orange-300 text-orange-600 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition">Copy</button>
        <a href="{{ $restaurant->storefront_url }}" target="_blank"
           class="text-xs bg-orange-500 text-white hover:bg-orange-600 px-3 py-1.5 rounded-lg transition">Preview →</a>
    </div>
</div>

{{-- ── Tabs ─────────────────────────────────────────────────────────────── --}}
<div class="flex gap-1 bg-gray-100 p-1 rounded-2xl mb-6 overflow-x-auto">
    @foreach([
        ['id'=>'general',    'label'=>'General',      'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9'],
        ['id'=>'appearance', 'label'=>'Appearance',   'icon'=>'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
        ['id'=>'orders',     'label'=>'Orders',       'icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['id'=>'hours',      'label'=>'Working Hours','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['id'=>'social',     'label'=>'Social & SEO', 'icon'=>'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
    ] as $tab)
    <button onclick="switchTab('{{ $tab['id'] }}')" id="tab-btn-{{ $tab['id'] }}"
            class="tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-all flex-shrink-0
                   {{ $activeTab === $tab['id'] ? 'bg-white text-orange-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
        </svg>
        {{ $tab['label'] }}
    </button>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     TAB 1 — GENERAL
════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-general" class="tab-panel {{ $activeTab !== 'general' ? 'hidden' : '' }}">
<form method="POST" action="{{ route('restaurant.settings.update') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="tab" value="general">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Logo & Cover --}}
        <div class="space-y-4">
            {{-- Logo --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Restaurant Logo</h3>
                <div class="flex flex-col items-center gap-3">
                    <div id="logo-preview-wrap"
                         class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center bg-gray-50">
                        @if($restaurant->logo)
                            <img id="logo-preview-img" src="{{ Storage::url($restaurant->logo) }}"
                                 class="w-full h-full object-cover" alt="Logo">
                        @else
                            <img id="logo-preview-img" src="" class="hidden w-full h-full object-cover" alt="">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>
                    <label for="logo" class="cursor-pointer text-xs font-medium text-orange-600 hover:text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg transition">
                        {{ $restaurant->logo ? 'Change Logo' : 'Upload Logo' }}
                    </label>
                    <input type="file" id="logo" name="logo" accept="image/*" class="hidden"
                           onchange="previewImg(this,'logo-preview-img')">
                    <p class="text-xs text-gray-400 text-center">PNG, JPG up to 2MB<br>Recommended: 200×200px</p>
                </div>
            </div>

            {{-- Cover Image --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Cover / Banner Image</h3>
                <div class="space-y-3">
                    <div class="w-full aspect-video rounded-xl border-2 border-dashed border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center">
                        @if($restaurant->cover_image)
                            <img id="cover-preview-img" src="{{ Storage::url($restaurant->cover_image) }}"
                                 class="w-full h-full object-cover" alt="">
                        @else
                            <img id="cover-preview-img" src="" class="hidden w-full h-full object-cover" alt="">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        @endif
                    </div>
                    <label for="cover_image" class="cursor-pointer block text-center text-xs font-medium text-orange-600 hover:text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg transition">
                        {{ $restaurant->cover_image ? 'Change Cover' : 'Upload Cover' }}
                    </label>
                    <input type="file" id="cover_image" name="cover_image" accept="image/*" class="hidden"
                           onchange="previewImg(this,'cover-preview-img')">
                    <p class="text-xs text-gray-400 text-center">Used in hero slider. 1200×600px recommended.</p>
                </div>
            </div>
        </div>

        {{-- Right: Info --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700">Basic Information</h3>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Restaurant Name *</label>
                    <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $restaurant->phone) }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $restaurant->email) }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">City</label>
                        <input type="text" name="city" value="{{ old('city', $restaurant->city) }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Timezone</label>
                        <select name="timezone" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                            @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ old('timezone', $restaurant->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Address</label>
                    <input type="text" name="address" value="{{ old('address', $restaurant->address) }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 resize-none transition">{{ old('description', $restaurant->description) }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700">Domain Settings</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Custom Domain</label>
                    <input type="text" name="custom_domain" value="{{ old('custom_domain', $restaurant->custom_domain) }}"
                           placeholder="e.g. mypizza.com"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                    <p class="text-xs text-gray-400 mt-1">Point your domain's A record to this server's IP, then enter it here.</p>
                </div>

                <label class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Accept Orders</p>
                        <p class="text-xs text-gray-400">Allow customers to place orders on your storefront</p>
                    </div>
                    <div class="relative">
                        <input type="checkbox" name="accepts_orders" value="1"
                               {{ old('accepts_orders', $restaurant->accepts_orders) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                </label>
            </div>

            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
                Save General Settings
            </button>
        </div>
    </div>
</form>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     TAB 2 — APPEARANCE
════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-appearance" class="tab-panel {{ $activeTab !== 'appearance' ? 'hidden' : '' }}">
<form method="POST" action="{{ route('restaurant.settings.update') }}">
    @csrf
    <input type="hidden" name="tab" value="appearance">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Brand Color --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Brand Color</h3>
            <div class="flex items-center gap-4">
                <input type="color" name="primary_color" id="color-picker"
                       value="{{ old('primary_color', $restaurant->primary_color ?? '#f97316') }}"
                       class="w-14 h-14 rounded-xl border border-gray-200 cursor-pointer p-1"
                       oninput="document.getElementById('color-hex').value=this.value; document.getElementById('color-sample').style.background=this.value">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1.5">Hex Code</label>
                    <input type="text" id="color-hex" value="{{ $restaurant->primary_color ?? '#f97316' }}"
                           oninput="document.getElementById('color-picker').value=this.value; document.getElementById('color-sample').style.background=this.value"
                           maxlength="7" placeholder="#f97316"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>
                <div id="color-sample" class="w-14 h-14 rounded-xl border border-gray-100 shadow-inner flex-shrink-0"
                     style="background: {{ $restaurant->primary_color ?? '#f97316' }}"></div>
            </div>

            {{-- Color presets --}}
            <div class="mt-4">
                <p class="text-xs text-gray-400 mb-2">Quick presets</p>
                <div class="flex flex-wrap gap-2">
                    @foreach(['#f97316','#ef4444','#8b5cf6','#3b82f6','#10b981','#f59e0b','#ec4899','#14b8a6','#6366f1','#1d4ed8'] as $c)
                    <button type="button"
                            onclick="document.getElementById('color-picker').value='{{ $c }}'; document.getElementById('color-hex').value='{{ $c }}'; document.getElementById('color-sample').style.background='{{ $c }}'"
                            class="w-7 h-7 rounded-lg border-2 border-white shadow hover:scale-110 transition"
                            style="background:{{ $c }}" title="{{ $c }}"></button>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-3">This color is used for buttons, highlights, and accents on your storefront.</p>
        </div>

        {{-- Announcement Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Announcement Bar</h3>
            <p class="text-xs text-gray-400 mb-3">Show a banner at the top of your storefront (promotions, delivery info, etc.)</p>

            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition mb-3">
                <span class="text-sm font-medium text-gray-700">Show Announcement Bar</span>
                <div class="relative">
                    <input type="checkbox" name="announcement_enabled" value="1"
                           {{ $settings['announcement_enabled'] ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </div>
            </label>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Announcement Text</label>
                <input type="text" name="announcement_text"
                       value="{{ old('announcement_text', $settings['announcement_text']) }}"
                       placeholder="🎉 Free delivery on orders over $30!"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
            </div>

            <div class="mt-3">
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Bar Background Color</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['bg-orange-500'=>'Orange','bg-green-500'=>'Green','bg-blue-600'=>'Blue','bg-red-500'=>'Red','bg-gray-800'=>'Dark'] as $cls => $lbl)
                    <label class="cursor-pointer">
                        <input type="radio" name="announcement_color" value="{{ $cls }}"
                               class="sr-only peer"
                               {{ old('announcement_color', $settings['announcement_color'] ?? 'bg-orange-500') === $cls ? 'checked' : '' }}>
                        <span class="block px-3 py-1.5 rounded-lg text-xs text-white font-medium peer-checked:ring-2 peer-checked:ring-offset-1 peer-checked:ring-gray-400 {{ $cls }}">
                            {{ $lbl }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Storefront Sections --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Storefront Sections</h3>
            <div class="space-y-3">
                @foreach([
                    ['name'=>'show_offers_section',   'label'=>'Special Offers Section', 'desc'=>'Show products with sale prices in a highlighted section'],
                    ['name'=>'show_featured_section', 'label'=>'Featured Items',          'desc'=>'Show featured products in the hero slider'],
                    ['name'=>'show_category_nav',     'label'=>'Category Quick Nav',      'desc'=>'Show horizontal category navigation pills'],
                    ['name'=>'show_restaurant_info',  'label'=>'Restaurant Info Bar',     'desc'=>'Show address, phone, and status below the slider'],
                ] as $toggle)
                <label class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $toggle['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ $toggle['desc'] }}</p>
                    </div>
                    <div class="relative flex-shrink-0 ml-3">
                        <input type="checkbox" name="{{ $toggle['name'] }}" value="1"
                               {{ $settings[$toggle['name']] ?? true ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Footer Text</h3>
            <textarea name="footer_text" rows="3" placeholder="© 2025 My Restaurant. All rights reserved."
                      class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 resize-none transition">{{ old('footer_text', $settings['footer_text']) }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Shown at the bottom of your storefront page.</p>
        </div>
    </div>

    <button type="submit" class="mt-6 w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
        Save Appearance Settings
    </button>
</form>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     TAB 3 — ORDER SETTINGS
════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-orders" class="tab-panel {{ $activeTab !== 'orders' ? 'hidden' : '' }}">
<form method="POST" action="{{ route('restaurant.settings.update') }}">
    @csrf
    <input type="hidden" name="tab" value="orders">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Order Types --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Order Types</h3>
            <div class="space-y-3">
                @foreach([
                    ['name'=>'delivery_enabled', 'label'=>'Delivery', 'desc'=>'Customers can order for delivery to their address'],
                    ['name'=>'takeaway_enabled', 'label'=>'Takeaway / Pickup', 'desc'=>'Customers can pick up their order from the restaurant'],
                    ['name'=>'dine_in_enabled',  'label'=>'Dine In',  'desc'=>'Customers can order while seated at the restaurant'],
                ] as $type)
                <label class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $type['label'] }}</p>
                        <p class="text-xs text-gray-400">{{ $type['desc'] }}</p>
                    </div>
                    <div class="relative flex-shrink-0 ml-3">
                        <input type="checkbox" name="{{ $type['name'] }}" value="1"
                               {{ $settings[$type['name']] ?? true ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Fees --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-700">Fees & Minimums</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Delivery Fee</label>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-300">
                        <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-r border-gray-200">$</span>
                        <input type="number" name="delivery_fee" step="0.01" min="0"
                               value="{{ old('delivery_fee', $settings['delivery_fee']) }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Tax Rate</label>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-300">
                        <input type="number" name="tax_rate" step="0.1" min="0" max="100"
                               value="{{ old('tax_rate', $settings['tax_rate']) }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none">
                        <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-l border-gray-200">%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Minimum Order</label>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-300">
                        <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-r border-gray-200">$</span>
                        <input type="number" name="min_order_amount" step="0.01" min="0"
                               value="{{ old('min_order_amount', $settings['min_order_amount']) }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Est. Delivery Time</label>
                    <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-orange-300">
                        <input type="text" name="delivery_time" placeholder="30-45"
                               value="{{ old('delivery_time', $settings['delivery_time']) }}"
                               class="flex-1 px-3 py-2.5 text-sm outline-none">
                        <span class="px-3 py-2.5 bg-gray-50 text-gray-400 text-sm border-l border-gray-200">min</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Currency --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-700">Currency</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Currency Code</label>
                    <input type="text" name="currency" maxlength="3" placeholder="USD"
                           value="{{ old('currency', $restaurant->currency ?? 'USD') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Currency Symbol</label>
                    <input type="text" name="currency_symbol" maxlength="5"
                           value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}"
                           placeholder="$"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>
            </div>
        </div>

        {{-- Notification --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-700">Order Notifications</h3>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Notification Email</label>
                <input type="email" name="notification_email"
                       value="{{ old('notification_email', $settings['notification_email']) }}"
                       placeholder="orders@myrestaurant.com"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <p class="text-xs text-gray-400 mt-1">Receive email alerts for new orders at this address.</p>
            </div>

            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                <div>
                    <p class="text-sm font-medium text-gray-700">Auto-confirm Orders</p>
                    <p class="text-xs text-gray-400">Automatically set new orders to "confirmed" status</p>
                </div>
                <div class="relative flex-shrink-0 ml-3">
                    <input type="checkbox" name="auto_confirm" value="1"
                           {{ $settings['auto_confirm'] ?? false ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-checked:bg-orange-500 rounded-full transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </div>
            </label>
        </div>
    </div>

    <button type="submit" class="mt-6 w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
        Save Order Settings
    </button>
</form>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     TAB 4 — WORKING HOURS
════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-hours" class="tab-panel {{ $activeTab !== 'hours' ? 'hidden' : '' }}">
<form method="POST" action="{{ route('restaurant.settings.update') }}">
    @csrf
    <input type="hidden" name="tab" value="hours">

    <div class="bg-white rounded-2xl border border-gray-100 p-6 max-w-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold text-gray-700">Weekly Working Hours</h3>
            <button type="button" onclick="setAllHours()"
                    class="text-xs text-orange-600 hover:text-orange-700 border border-orange-200 px-3 py-1.5 rounded-lg transition">
                Apply to All Days
            </button>
        </div>

        <div class="space-y-3">
            @php
                $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                $dayLabels = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                $hours = $settings['working_hours'] ?? [];
            @endphp

            {{-- Reference row (for "apply to all") --}}
            <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-xl border border-orange-100">
                <span class="w-24 text-xs font-semibold text-orange-700">Default</span>
                <input type="time" id="default-open"  value="09:00" class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                <span class="text-xs text-gray-400">to</span>
                <input type="time" id="default-close" value="22:00" class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                <span class="text-xs text-gray-400 ml-auto">Set these → apply all</span>
            </div>

            @foreach($days as $i => $day)
            @php
                $dayData = collect($hours)->firstWhere('day', $day) ?? ['is_closed'=>false,'open'=>'09:00','close'=>'22:00'];
            @endphp
            <div class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl hover:bg-gray-50 transition">
                <input type="checkbox" name="hours[{{ $day }}][is_open]" value="1"
                       id="open_{{ $day }}"
                       {{ !($dayData['is_closed'] ?? false) ? 'checked' : '' }}
                       class="w-4 h-4 accent-orange-500"
                       onchange="toggleDayRow('{{ $day }}', this.checked)">
                <label for="open_{{ $day }}" class="w-24 text-sm font-medium text-gray-700 cursor-pointer">{{ $dayLabels[$i] }}</label>
                <div id="hours-row-{{ $day }}" class="{{ ($dayData['is_closed'] ?? false) ? 'opacity-40 pointer-events-none' : '' }} flex items-center gap-2 flex-1">
                    <input type="time" name="hours[{{ $day }}][open]"
                           value="{{ $dayData['open'] ?? '09:00' }}"
                           class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <span class="text-xs text-gray-400">to</span>
                    <input type="time" name="hours[{{ $day }}][close]"
                           value="{{ $dayData['close'] ?? '22:00' }}"
                           class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-300">
                </div>
                <span id="closed-label-{{ $day }}" class="{{ !($dayData['is_closed'] ?? false) ? 'hidden' : '' }} text-xs text-red-400 font-medium">Closed</span>
            </div>
            @endforeach
        </div>

        <button type="submit" class="mt-6 w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
            Save Working Hours
        </button>
    </div>
</form>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     TAB 5 — SOCIAL & SEO
════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-social" class="tab-panel {{ $activeTab !== 'social' ? 'hidden' : '' }}">
<form method="POST" action="{{ route('restaurant.settings.update') }}">
    @csrf
    <input type="hidden" name="tab" value="social">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Social Links --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-700">Social Media Links</h3>
            <p class="text-xs text-gray-400 -mt-2">These will appear as icons in your storefront footer.</p>

            @foreach([
                ['name'=>'whatsapp',  'label'=>'WhatsApp Number', 'placeholder'=>'+1 555 123 4567', 'icon'=>'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z'],
                ['name'=>'instagram', 'label'=>'Instagram URL',   'placeholder'=>'https://instagram.com/myrestaurant', 'icon'=>'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z'],
                ['name'=>'facebook',  'label'=>'Facebook URL',    'placeholder'=>'https://facebook.com/myrestaurant', 'icon'=>'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
                ['name'=>'twitter',   'label'=>'Twitter / X URL', 'placeholder'=>'https://twitter.com/myrestaurant', 'icon'=>'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
            ] as $social)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="{{ $social['icon'] }}"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">{{ $social['label'] }}</label>
                    <input type="text" name="{{ $social['name'] }}"
                           value="{{ old($social['name'], $settings[$social['name']] ?? '') }}"
                           placeholder="{{ $social['placeholder'] }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                </div>
            </div>
            @endforeach
        </div>

        {{-- SEO --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
            <h3 class="text-sm font-bold text-gray-700">SEO / Meta Tags</h3>
            <p class="text-xs text-gray-400 -mt-2">Improve how your storefront appears in search engines.</p>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Page Title</label>
                <input type="text" name="meta_title"
                       value="{{ old('meta_title', $settings['meta_title']) }}"
                       placeholder="{{ $restaurant->name }} — Best Food in Town"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
                <p class="text-xs text-gray-400 mt-1">Recommended: 50–60 characters</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Meta Description</label>
                <textarea name="meta_description" rows="3"
                          placeholder="Order delicious food online from {{ $restaurant->name }}. Fast delivery, fresh ingredients."
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 resize-none transition">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Recommended: 150–160 characters</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Keywords</label>
                <input type="text" name="meta_keywords"
                       value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                       placeholder="burger, pizza, fast food, delivery"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 transition">
            </div>

            {{-- Preview --}}
            <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 mb-2">Search Preview</p>
                <p class="text-blue-700 text-sm font-medium" id="seo-title-preview">
                    {{ $settings['meta_title'] ?: $restaurant->name }}
                </p>
                <p class="text-xs text-green-700">{{ $restaurant->storefront_url }}</p>
                <p class="text-xs text-gray-500 mt-0.5" id="seo-desc-preview">
                    {{ Str::limit($settings['meta_description'] ?: $restaurant->description, 150) }}
                </p>
            </div>
        </div>
    </div>

    <button type="submit" class="mt-6 w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition text-sm">
        Save Social & SEO Settings
    </button>
</form>
</div>

@endsection

@push('scripts')
<script>
// ── Tab switching ─────────────────────────────────────────────────────────
function switchTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('tab-' + id).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white','text-orange-600','shadow-sm');
        b.classList.add('text-gray-500');
    });
    const btn = document.getElementById('tab-btn-' + id);
    btn.classList.add('bg-white','text-orange-600','shadow-sm');
    btn.classList.remove('text-gray-500');
    history.replaceState(null,'','?tab='+id);
}

// ── Image preview ─────────────────────────────────────────────────────────
function previewImg(input, imgId) {
    const img = document.getElementById(imgId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Working hours helpers ─────────────────────────────────────────────────
function toggleDayRow(day, isOpen) {
    const row   = document.getElementById('hours-row-' + day);
    const label = document.getElementById('closed-label-' + day);
    if (isOpen) {
        row.classList.remove('opacity-40','pointer-events-none');
        label.classList.add('hidden');
    } else {
        row.classList.add('opacity-40','pointer-events-none');
        label.classList.remove('hidden');
    }
}

function setAllHours() {
    const open  = document.getElementById('default-open').value;
    const close = document.getElementById('default-close').value;
    ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'].forEach(day => {
        const row = document.getElementById('hours-row-' + day);
        row.querySelectorAll('input[type="time"]')[0].value = open;
        row.querySelectorAll('input[type="time"]')[1].value = close;
        document.getElementById('open_' + day).checked = true;
        toggleDayRow(day, true);
    });
}

// ── SEO live preview ──────────────────────────────────────────────────────
document.querySelector('input[name="meta_title"]')?.addEventListener('input', function() {
    document.getElementById('seo-title-preview').textContent = this.value || '{{ $restaurant->name }}';
});
document.querySelector('textarea[name="meta_description"]')?.addEventListener('input', function() {
    document.getElementById('seo-desc-preview').textContent = this.value.slice(0, 150) || '{{ Str::limit($restaurant->description ?? '', 150) }}';
});
</script>
@endpush

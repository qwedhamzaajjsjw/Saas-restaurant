<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    // Setting keys stored in the `settings` table (per-restaurant)
    private const SETTING_KEYS = [
        // Appearance
        'announcement_enabled', 'announcement_text', 'announcement_color',
        'show_offers_section', 'show_featured_section', 'show_category_nav', 'show_restaurant_info',
        'footer_text',
        // Orders
        'delivery_fee', 'tax_rate', 'min_order_amount', 'delivery_time',
        'delivery_enabled', 'takeaway_enabled', 'dine_in_enabled',
        'notification_email', 'auto_confirm',
        'currency_symbol',
        // Working hours
        'working_hours',
        // Social & SEO
        'whatsapp', 'instagram', 'facebook', 'twitter',
        'meta_title', 'meta_description', 'meta_keywords',
    ];

    public function index()
    {
        $restaurant = auth()->user()->restaurant;
        $rid        = $restaurant->id;

        // Load all per-restaurant settings into a flat array
        $settings = [];
        foreach (self::SETTING_KEYS as $key) {
            $settings[$key] = Setting::get($key, null, $rid);
        }

        // Defaults
        $settings['delivery_fee']        ??= 0;
        $settings['tax_rate']            ??= 0;
        $settings['min_order_amount']    ??= 0;
        $settings['delivery_time']       ??= '30-45';
        $settings['delivery_enabled']    ??= true;
        $settings['takeaway_enabled']    ??= true;
        $settings['dine_in_enabled']     ??= true;
        $settings['auto_confirm']        ??= false;
        $settings['announcement_enabled']??= false;
        $settings['announcement_text']   ??= '';
        $settings['announcement_color']  ??= 'bg-orange-500';
        $settings['show_offers_section'] ??= true;
        $settings['show_featured_section']??= true;
        $settings['show_category_nav']   ??= true;
        $settings['show_restaurant_info']??= true;
        $settings['footer_text']         ??= '';
        $settings['notification_email']  ??= $restaurant->email ?? '';
        $settings['currency_symbol']     ??= '$';
        $settings['working_hours']       ??= [];
        $settings['whatsapp']            ??= '';
        $settings['instagram']           ??= '';
        $settings['facebook']            ??= '';
        $settings['twitter']             ??= '';
        $settings['meta_title']          ??= '';
        $settings['meta_description']    ??= '';
        $settings['meta_keywords']       ??= '';

        $activeTab = request('tab', 'general');

        return view('restaurant.settings.index', compact('restaurant', 'settings', 'activeTab'));
    }

    public function update(Request $request)
    {
        $restaurant = auth()->user()->restaurant;
        $rid        = $restaurant->id;
        $tab        = $request->input('tab', 'general');

        match ($tab) {
            'general'    => $this->saveGeneral($request, $restaurant),
            'appearance' => $this->saveAppearance($request, $rid),
            'orders'     => $this->saveOrders($request, $restaurant, $rid),
            'hours'      => $this->saveHours($request, $rid),
            'social'     => $this->saveSocial($request, $rid),
            default      => null,
        };

        return redirect()->route('restaurant.settings.index', ['tab' => $tab])
            ->with('success', 'Settings saved successfully.');
    }

    // ── General ───────────────────────────────────────────────────────────

    private function saveGeneral(Request $request, $restaurant): void
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email'],
            'address'        => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'description'    => ['nullable', 'string'],
            'accepts_orders' => ['boolean'],
            'timezone'       => ['nullable', 'string'],
            'logo'           => ['nullable', 'image', 'max:2048'],
            'cover_image'    => ['nullable', 'image', 'max:4096'],
            'custom_domain'  => [
                'nullable', 'string', 'max:253',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                \Illuminate\Validation\Rule::unique('restaurants', 'custom_domain')
                    ->ignore($restaurant->id),
            ],
        ]);

        $data['accepts_orders'] = $request->boolean('accepts_orders', true);

        if ($request->hasFile('logo')) {
            if ($restaurant->logo) Storage::disk('public')->delete($restaurant->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($restaurant->cover_image) Storage::disk('public')->delete($restaurant->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $restaurant->update($data);
    }

    // ── Appearance ────────────────────────────────────────────────────────

    private function saveAppearance(Request $request, int $rid): void
    {
        $request->validate([
            'primary_color'      => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'announcement_text'  => ['nullable', 'string', 'max:255'],
            'footer_text'        => ['nullable', 'string', 'max:500'],
            'announcement_color' => ['nullable', 'string'],
        ]);

        // primary_color goes to restaurants table
        auth()->user()->restaurant->update([
            'primary_color' => $request->input('primary_color', '#f97316'),
        ]);

        $this->bulkSet($rid, [
            'announcement_enabled'  => [$request->boolean('announcement_enabled'),  'boolean'],
            'announcement_text'     => [$request->input('announcement_text', ''),   'string'],
            'announcement_color'    => [$request->input('announcement_color','bg-orange-500'), 'string'],
            'show_offers_section'   => [$request->boolean('show_offers_section'),   'boolean'],
            'show_featured_section' => [$request->boolean('show_featured_section'), 'boolean'],
            'show_category_nav'     => [$request->boolean('show_category_nav'),     'boolean'],
            'show_restaurant_info'  => [$request->boolean('show_restaurant_info'),  'boolean'],
            'footer_text'           => [$request->input('footer_text', ''),         'string'],
        ]);
    }

    // ── Orders ────────────────────────────────────────────────────────────

    private function saveOrders(Request $request, $restaurant, int $rid): void
    {
        $request->validate([
            'delivery_fee'       => ['nullable', 'numeric', 'min:0'],
            'tax_rate'           => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_order_amount'   => ['nullable', 'numeric', 'min:0'],
            'delivery_time'      => ['nullable', 'string', 'max:20'],
            'notification_email' => ['nullable', 'email'],
            'currency'           => ['nullable', 'string', 'max:3'],
            'currency_symbol'    => ['nullable', 'string', 'max:5'],
        ]);

        $restaurant->update([
            'currency' => strtoupper($request->input('currency', 'USD')),
        ]);

        $this->bulkSet($rid, [
            'delivery_fee'       => [(float)$request->input('delivery_fee', 0),    'string'],
            'tax_rate'           => [(float)$request->input('tax_rate', 0),         'string'],
            'min_order_amount'   => [(float)$request->input('min_order_amount', 0),'string'],
            'delivery_time'      => [$request->input('delivery_time', '30-45'),    'string'],
            'delivery_enabled'   => [$request->boolean('delivery_enabled'),        'boolean'],
            'takeaway_enabled'   => [$request->boolean('takeaway_enabled'),        'boolean'],
            'dine_in_enabled'    => [$request->boolean('dine_in_enabled'),         'boolean'],
            'notification_email' => [$request->input('notification_email', ''),    'string'],
            'auto_confirm'       => [$request->boolean('auto_confirm'),            'boolean'],
            'currency_symbol'    => [$request->input('currency_symbol', '$'),      'string'],
        ]);
    }

    // ── Working Hours ─────────────────────────────────────────────────────

    private function saveHours(Request $request, int $rid): void
    {
        $days  = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $input = $request->input('hours', []);
        $hours = [];

        foreach ($days as $day) {
            $dayData = $input[$day] ?? [];
            $hours[] = [
                'day'       => $day,
                'is_closed' => !isset($dayData['is_open']),
                'open'      => $dayData['open']  ?? '09:00',
                'close'     => $dayData['close'] ?? '22:00',
            ];
        }

        Setting::set('working_hours', json_encode($hours), $rid, 'json');
    }

    // ── Social & SEO ──────────────────────────────────────────────────────

    private function saveSocial(Request $request, int $rid): void
    {
        $request->validate([
            'whatsapp'         => ['nullable', 'string', 'max:30'],
            'instagram'        => ['nullable', 'url', 'max:255'],
            'facebook'         => ['nullable', 'url', 'max:255'],
            'twitter'          => ['nullable', 'url', 'max:255'],
            'meta_title'       => ['nullable', 'string', 'max:80'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'meta_keywords'    => ['nullable', 'string', 'max:255'],
        ]);

        $this->bulkSet($rid, [
            'whatsapp'         => [$request->input('whatsapp', ''),         'string'],
            'instagram'        => [$request->input('instagram', ''),        'string'],
            'facebook'         => [$request->input('facebook', ''),         'string'],
            'twitter'          => [$request->input('twitter', ''),          'string'],
            'meta_title'       => [$request->input('meta_title', ''),       'string'],
            'meta_description' => [$request->input('meta_description', ''), 'string'],
            'meta_keywords'    => [$request->input('meta_keywords', ''),    'string'],
        ]);
    }

    // ── Helper ────────────────────────────────────────────────────────────

    private function bulkSet(int $restaurantId, array $map): void
    {
        foreach ($map as $key => [$value, $type]) {
            Setting::set($key, is_bool($value) ? (int)$value : $value, $restaurantId, $type);
        }
    }
}

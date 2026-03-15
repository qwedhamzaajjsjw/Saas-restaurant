<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected array $settingKeys = [
        'app_name', 'app_email', 'app_currency',
        'registration_open', 'maintenance_mode', 'default_plan',
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->settingKeys as $key) {
            $settings[$key] = Setting::get($key, null, null);
        }

        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name'          => ['required', 'string', 'max:100'],
            'app_email'         => ['required', 'email'],
            'app_currency'      => ['required', 'string', 'size:3'],
            'registration_open' => ['boolean'],
            'maintenance_mode'  => ['boolean'],
        ]);

        foreach ($data as $key => $value) {
            $type = in_array($key, ['registration_open', 'maintenance_mode']) ? 'boolean' : 'string';
            Setting::set($key, $value, null, $type);
        }

        // booleans from checkboxes
        foreach (['registration_open', 'maintenance_mode'] as $boolKey) {
            Setting::set($boolKey, $request->boolean($boolKey), null, 'boolean');
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}

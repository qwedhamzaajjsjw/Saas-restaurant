<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $restaurant = auth()->user()->restaurant;
        return view('restaurant.settings.index', compact('restaurant'));
    }

    public function update(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

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
            'custom_domain'  => [
                'nullable', 'string', 'max:253',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                \Illuminate\Validation\Rule::unique('restaurants', 'custom_domain')
                    ->ignore($restaurant->id),
            ],
        ]);

        $data['accepts_orders'] = $request->boolean('accepts_orders', true);

        // رفع الشعار
        if ($request->hasFile('logo')) {
            if ($restaurant->logo) {
                Storage::disk('public')->delete($restaurant->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $restaurant->update($data);

        return back()->with('success', 'Settings saved successfully.');
    }
}

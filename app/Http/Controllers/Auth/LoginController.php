<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            return match ($user->role) {
                'super_admin'        => redirect()->route('admin.dashboard'),
                'restaurant_owner'   => redirect()->route('restaurant.dashboard'),
                default              => redirect('/'),
            };
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }
}

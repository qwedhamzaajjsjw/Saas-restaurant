<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Handles super-admin impersonation of restaurant owner accounts.
 *
 * Flow:
 *  1. Super admin calls loginAs() → creates a short-lived cache token
 *  2. Browser is redirected to switchToRestaurant($token) — no role middleware
 *  3. Token is validated, admin's ID saved in session, user is switched to restaurant owner
 *  4. Restaurant owner dashboard loads normally (with "Return to Admin" banner)
 *  5. Admin clicks "Return to Admin" → returnToAdmin() switches back
 */
class ImpersonationController extends Controller
{
    /**
     * Perform the actual user switch to restaurant owner.
     * Called from a clean route with no role restrictions.
     */
    public function switchToRestaurant(string $token)
    {
        $data = Cache::pull('restaurant_switch_' . $token);

        if (! $data || ! isset($data['admin_id'], $data['restaurant_id'])) {
            abort(403, 'Invalid or expired impersonation token.');
        }

        // Must be the same admin who generated the token
        if (auth()->id() !== $data['admin_id']) {
            abort(403, 'Token mismatch.');
        }

        $owner = User::where('restaurant_id', $data['restaurant_id'])
                     ->where('role', 'restaurant_owner')
                     ->first();

        if (! $owner) {
            return redirect()->route('admin.restaurants.index')
                ->with('error', 'No owner account found for this restaurant.');
        }

        // Store the admin ID before switching — session data survives migrate(true)
        $adminId = $data['admin_id'];

        Auth::login($owner);

        // Re-set after login so it survives session regeneration
        session(['returning_admin_id' => $adminId]);

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Previewing restaurant dashboard as owner.');
    }

    /**
     * Switch back from restaurant owner to super admin.
     * Called from a clean route accessible by restaurant_owner role.
     */
    public function returnToAdmin()
    {
        $adminId = session('returning_admin_id');

        if (! $adminId) {
            return redirect()->route('restaurant.dashboard')
                ->with('error', 'No admin session to return to.');
        }

        $admin = User::where('id', $adminId)
                     ->where('role', 'super_admin')
                     ->first();

        if (! $admin) {
            session()->forget('returning_admin_id');
            return redirect()->route('login');
        }

        session()->forget('returning_admin_id');

        Auth::login($admin);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Returned to admin panel.');
    }
}

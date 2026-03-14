<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates the default Super Admin account.
 * Used by both the web installer and artisan db:seed.
 *
 * Credentials can be overridden via environment variables:
 *   SUPER_ADMIN_NAME, SUPER_ADMIN_EMAIL, SUPER_ADMIN_PASSWORD
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'admin@restaurant-saas.com')],
            [
                'name'              => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password'          => Hash::make(env('SUPER_ADMIN_PASSWORD', 'Admin@123456')),
                'role'              => 'super_admin',
                'restaurant_id'     => null,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

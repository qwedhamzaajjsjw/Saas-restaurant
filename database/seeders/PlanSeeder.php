<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                  => 'Starter',
                'slug'                  => 'starter',
                'description'           => 'Perfect for small restaurants just getting started.',
                'price'                 => 0.00,
                'currency'              => 'USD',
                'max_products'          => 20,
                'max_orders_per_month'  => 100,
                'max_categories'        => 5,
                'features'              => ['basic_menu', 'online_orders'],
                'is_active'             => true,
                'sort_order'            => 1,
            ],
            [
                'name'                  => 'Pro',
                'slug'                  => 'pro',
                'description'           => 'For growing restaurants that need more power.',
                'price'                 => 29.99,
                'currency'              => 'USD',
                'max_products'          => 100,
                'max_orders_per_month'  => 1000,
                'max_categories'        => 20,
                'features'              => ['basic_menu', 'online_orders', 'analytics', 'export'],
                'is_active'             => true,
                'sort_order'            => 2,
            ],
            [
                'name'                  => 'Enterprise',
                'slug'                  => 'enterprise',
                'description'           => 'Unlimited scale for restaurant chains.',
                'price'                 => 99.99,
                'currency'              => 'USD',
                'max_products'          => 9999,
                'max_orders_per_month'  => 9999,
                'max_categories'        => 9999,
                'features'              => ['basic_menu', 'online_orders', 'analytics', 'export', 'custom_domain', 'priority_support'],
                'is_active'             => true,
                'sort_order'            => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}

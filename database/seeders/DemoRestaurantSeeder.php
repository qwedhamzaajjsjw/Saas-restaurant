<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoRestaurantSeeder extends Seeder
{
    private const SLUG = 'burger-house-demo';

    public function run(): void
    {
        // ── Find a plan ───────────────────────────────────────────────────
        $plan = Plan::where('name', 'Pro')->first() ?? Plan::first();
        if (!$plan) {
            $this->command->error('No plans found. Please create a plan first.');
            return;
        }

        // ── Clean up any previous demo data ──────────────────────────────
        $existing = Restaurant::withTrashed()->where('slug', self::SLUG)->first();
        if ($existing) {
            $this->command->info('Removing old demo data...');
            Product::withoutGlobalScopes()->where('restaurant_id', $existing->id)->forceDelete();
            Category::withoutGlobalScopes()->where('restaurant_id', $existing->id)->forceDelete();
            Menu::withoutGlobalScopes()->where('restaurant_id', $existing->id)->forceDelete();
            Subscription::where('restaurant_id', $existing->id)->delete();
            User::where('restaurant_id', $existing->id)->forceDelete();
            $existing->forceDelete();
        }

        // ── Create Demo Restaurant ────────────────────────────────────────
        $restaurant = Restaurant::create([
            'name'          => 'Burger House',
            'slug'          => self::SLUG,
            'description'   => 'Serving the juiciest burgers in town since 2010. Fresh ingredients, bold flavors, and unforgettable taste.',
            'logo'          => 'demo/logo.svg',
            'cover_image'   => 'demo/cover.svg',
            'primary_color' => '#f97316',
            'phone'         => '+1 (555) 123-4567',
            'email'         => 'hello@burgerhouse.demo',
            'address'       => '123 Main Street',
            'city'          => 'New York',
            'country'       => 'US',
            'plan_id'       => $plan->id,
            'status'        => 'active',
            'accepts_orders'=> true,
            'timezone'      => 'America/New_York',
            'currency'      => 'USD',
        ]);

        // ── Create Restaurant Owner ───────────────────────────────────────
        User::create([
            'name'              => 'Demo Owner',
            'email'             => 'owner@burgerhouse.demo',
            'password'          => Hash::make('Demo@123456'),
            'role'              => 'restaurant_owner',
            'restaurant_id'     => $restaurant->id,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        // ── Create Subscription ───────────────────────────────────────────
        Subscription::create([
            'restaurant_id' => $restaurant->id,
            'plan_id'       => $plan->id,
            'starts_at'     => now(),
            'ends_at'       => now()->addYear(),
            'status'        => 'active',
            'amount_paid'   => 0,
        ]);

        // ── Create Menu ───────────────────────────────────────────────────
        $menu = Menu::create([
            'restaurant_id' => $restaurant->id,
            'name'          => 'Main Menu',
            'description'   => 'Our full selection of delicious food and drinks',
            'is_active'     => true,
            'sort_order'    => 0,
        ]);

        // ── Common option sets ────────────────────────────────────────────
        $burgerExtras = [
            ['name' => 'Extra Cheese',  'price' => 1.00],
            ['name' => 'Double Patty',  'price' => 2.50],
            ['name' => 'Bacon',         'price' => 1.50],
            ['name' => 'Jalapeños',     'price' => 0.50],
            ['name' => 'Avocado',       'price' => 1.00],
        ];

        $sauceExtras = [
            ['name' => 'Mayo',          'price' => 0.00],
            ['name' => 'Ketchup',       'price' => 0.00],
            ['name' => 'BBQ Sauce',     'price' => 0.50],
            ['name' => 'Hot Sauce',     'price' => 0.50],
        ];

        $drinkExtras = [
            ['name' => 'Extra Ice',     'price' => 0.00],
            ['name' => 'No Ice',        'price' => 0.00],
            ['name' => 'Large Size',    'price' => 1.00],
        ];

        // ═══════════════════════════════════════════════════════════════════
        // CATEGORY 1 — Burgers
        // ═══════════════════════════════════════════════════════════════════
        $catBurgers = Category::create([
            'restaurant_id' => $restaurant->id,
            'menu_id'       => $menu->id,
            'name'          => 'Burgers',
            'description'   => 'Hand-crafted burgers made fresh daily',
            'is_active'     => true,
            'sort_order'    => 1,
        ]);

        $burgers = [
            [
                'name'        => 'Classic Smash Burger',
                'description' => 'Double smash patties, cheddar cheese, lettuce, tomato, pickles, and our secret sauce.',
                'price'       => 12.99,
                'sale_price'  => null,
                'image'       => 'demo/products/burger.svg',
                'is_featured' => true,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'BBQ Bacon Burger',
                'description' => 'Smoky BBQ sauce, crispy bacon, caramelized onions, and pepper jack cheese.',
                'price'       => 14.99,
                'sale_price'  => 11.99,
                'image'       => 'demo/products/burger.svg',
                'is_featured' => true,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'Spicy Crispy Chicken Burger',
                'description' => 'Crispy fried chicken thigh, spicy slaw, pickled jalapeños, sriracha mayo.',
                'price'       => 13.49,
                'sale_price'  => null,
                'image'       => 'demo/products/chicken.svg',
                'is_featured' => true,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Mushroom Swiss Burger',
                'description' => 'Sautéed mushrooms, Swiss cheese, garlic aioli, arugula on a brioche bun.',
                'price'       => 13.99,
                'sale_price'  => null,
                'image'       => 'demo/products/burger.svg',
                'is_featured' => false,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'Veggie Bean Burger',
                'description' => 'Black bean & quinoa patty, guacamole, roasted red pepper, sprouts.',
                'price'       => 11.99,
                'sale_price'  => 9.99,
                'image'       => 'demo/products/burger.svg',
                'is_featured' => false,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Tower Burger',
                'description' => 'Triple patty tower with bacon, egg, cheese, and all the toppings.',
                'price'       => 18.99,
                'sale_price'  => null,
                'image'       => 'demo/products/burger.svg',
                'is_featured' => false,
                'options'     => $burgerExtras,
            ],
        ];

        foreach ($burgers as $i => $data) {
            Product::create([
                'restaurant_id'    => $restaurant->id,
                'category_id'      => $catBurgers->id,
                'name'             => $data['name'],
                'slug'             => Str::slug($data['name']).'-'.Str::random(4),
                'description'      => $data['description'],
                'image'            => $data['image'],
                'price'            => $data['price'],
                'sale_price'       => $data['sale_price'],
                'is_available'     => true,
                'is_featured'      => $data['is_featured'],
                'options'          => $data['options'],
                'sort_order'       => $i,
                'preparation_time' => 15,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════════
        // CATEGORY 2 — Sides & Starters
        // ═══════════════════════════════════════════════════════════════════
        $catSides = Category::create([
            'restaurant_id' => $restaurant->id,
            'menu_id'       => $menu->id,
            'name'          => 'Sides & Starters',
            'description'   => 'Perfect companions for your main',
            'is_active'     => true,
            'sort_order'    => 2,
        ]);

        $sides = [
            [
                'name'        => 'Loaded Cheese Fries',
                'description' => 'Crispy fries topped with cheddar sauce, bacon bits, sour cream, and chives.',
                'price'       => 7.99,
                'sale_price'  => 5.99,
                'image'       => 'demo/products/fries.svg',
                'is_featured' => true,
                'options'     => [['name'=>'Extra Cheese Sauce','price'=>1.00],['name'=>'Add Jalapeños','price'=>0.50]],
            ],
            [
                'name'        => 'Onion Rings',
                'description' => 'Golden battered onion rings served with ranch dipping sauce.',
                'price'       => 5.99,
                'sale_price'  => null,
                'image'       => 'demo/products/rings.svg',
                'is_featured' => false,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Crispy Chicken Nuggets',
                'description' => 'Bite-sized crispy chicken nuggets served with your choice of dipping sauce.',
                'price'       => 10.99,
                'sale_price'  => null,
                'image'       => 'demo/products/nuggets.svg',
                'is_featured' => false,
                'options'     => [['name'=>'BBQ Sauce','price'=>0],['name'=>'Honey Mustard','price'=>0],['name'=>'Ranch','price'=>0],['name'=>'Extra Sauce','price'=>0.50]],
            ],
            [
                'name'        => 'Sweet Potato Fries',
                'description' => 'Seasoned sweet potato fries with chipotle mayo dip.',
                'price'       => 6.49,
                'sale_price'  => null,
                'image'       => 'demo/products/fries.svg',
                'is_featured' => false,
                'options'     => [],
            ],
        ];

        foreach ($sides as $i => $data) {
            Product::create([
                'restaurant_id'    => $restaurant->id,
                'category_id'      => $catSides->id,
                'name'             => $data['name'],
                'slug'             => Str::slug($data['name']).'-'.Str::random(4),
                'description'      => $data['description'],
                'image'            => $data['image'],
                'price'            => $data['price'],
                'sale_price'       => $data['sale_price'],
                'is_available'     => true,
                'is_featured'      => $data['is_featured'],
                'options'          => $data['options'],
                'sort_order'       => $i,
                'preparation_time' => 10,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════════
        // CATEGORY 3 — Drinks
        // ═══════════════════════════════════════════════════════════════════
        $catDrinks = Category::create([
            'restaurant_id' => $restaurant->id,
            'menu_id'       => $menu->id,
            'name'          => 'Drinks',
            'description'   => 'Cold beverages and shakes',
            'is_active'     => true,
            'sort_order'    => 3,
        ]);

        $drinks = [
            [
                'name'        => 'Classic Milkshake',
                'description' => 'Thick and creamy shake. Choose from vanilla, chocolate, or strawberry.',
                'price'       => 6.99,
                'sale_price'  => null,
                'image'       => 'demo/products/shake.svg',
                'is_featured' => true,
                'options'     => [['name'=>'Vanilla','price'=>0],['name'=>'Chocolate','price'=>0],['name'=>'Strawberry','price'=>0],['name'=>'Whipped Cream','price'=>0.50]],
            ],
            [
                'name'        => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice, served cold with ice.',
                'price'       => 4.49,
                'sale_price'  => 3.49,
                'image'       => 'demo/products/juice.svg',
                'is_featured' => false,
                'options'     => $drinkExtras,
            ],
            [
                'name'        => 'Craft Soda',
                'description' => 'House-made craft sodas in rotating seasonal flavors.',
                'price'       => 3.99,
                'sale_price'  => null,
                'image'       => 'demo/products/soda.svg',
                'is_featured' => false,
                'options'     => $drinkExtras,
            ],
        ];

        foreach ($drinks as $i => $data) {
            Product::create([
                'restaurant_id'    => $restaurant->id,
                'category_id'      => $catDrinks->id,
                'name'             => $data['name'],
                'slug'             => Str::slug($data['name']).'-'.Str::random(4),
                'description'      => $data['description'],
                'image'            => $data['image'],
                'price'            => $data['price'],
                'sale_price'       => $data['sale_price'],
                'is_available'     => true,
                'is_featured'      => $data['is_featured'],
                'options'          => $data['options'],
                'sort_order'       => $i,
                'preparation_time' => 5,
            ]);
        }

        // ═══════════════════════════════════════════════════════════════════
        // CATEGORY 4 — Desserts
        // ═══════════════════════════════════════════════════════════════════
        $catDesserts = Category::create([
            'restaurant_id' => $restaurant->id,
            'menu_id'       => $menu->id,
            'name'          => 'Desserts',
            'description'   => 'Sweet endings to a perfect meal',
            'is_active'     => true,
            'sort_order'    => 4,
        ]);

        $desserts = [
            [
                'name'        => 'Ice Cream Sundae',
                'description' => 'Three scoops of premium ice cream with hot fudge, whipped cream, and a cherry.',
                'price'       => 7.99,
                'sale_price'  => null,
                'image'       => 'demo/products/icecream.svg',
                'is_featured' => true,
                'options'     => [['name'=>'Extra Fudge','price'=>0.50],['name'=>'Sprinkles','price'=>0]],
            ],
            [
                'name'        => 'Birthday Cake Slice',
                'description' => 'Moist layered cake with buttercream frosting and colorful sprinkles.',
                'price'       => 6.99,
                'sale_price'  => 5.49,
                'image'       => 'demo/products/cake.svg',
                'is_featured' => false,
                'options'     => [['name'=>'Chocolate','price'=>0],['name'=>'Vanilla','price'=>0],['name'=>'Red Velvet','price'=>0]],
            ],
        ];

        foreach ($desserts as $i => $data) {
            Product::create([
                'restaurant_id'    => $restaurant->id,
                'category_id'      => $catDesserts->id,
                'name'             => $data['name'],
                'slug'             => Str::slug($data['name']).'-'.Str::random(4),
                'description'      => $data['description'],
                'image'            => $data['image'],
                'price'            => $data['price'],
                'sale_price'       => $data['sale_price'],
                'is_available'     => true,
                'is_featured'      => $data['is_featured'],
                'options'          => $data['options'],
                'sort_order'       => $i,
                'preparation_time' => 8,
            ]);
        }

        $this->command->info('✅ Demo restaurant "Burger House" created successfully!');
        $this->command->info('   Storefront URL : '.url('/restaurant/'.self::SLUG));
        $this->command->info('   Owner login    : owner@burgerhouse.demo / Demo@123456');
    }
}

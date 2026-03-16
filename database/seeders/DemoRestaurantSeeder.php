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
    public function run(): void
    {
        // ── Find or use Pro plan ──────────────────────────────────────────
        $plan = Plan::where('name', 'Pro')->first() ?? Plan::first();
        if (!$plan) return;

        // ── Create Demo Restaurant ────────────────────────────────────────
        $slug = 'burger-house-demo';

        if (Restaurant::where('slug', $slug)->exists()) {
            $this->command->info('Demo restaurant already exists, skipping.');
            return;
        }

        $restaurant = Restaurant::create([
            'name'          => 'Burger House',
            'slug'          => $slug,
            'description'   => 'Serving the juiciest burgers in town since 2010. Fresh ingredients, bold flavors, and unforgettable taste.',
            'logo'          => null,
            'cover_image'   => null,
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

        // ── Extras (reusable option sets) ─────────────────────────────────
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
                'image'       => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'BBQ Bacon Burger',
                'description' => 'Smoky BBQ sauce, crispy bacon, caramelized onions, and pepper jack cheese.',
                'price'       => 14.99,
                'sale_price'  => 11.99,
                'image'       => 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'Mushroom Swiss Burger',
                'description' => 'Sautéed mushrooms, Swiss cheese, garlic aioli, arugula on a brioche bun.',
                'price'       => 13.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => array_merge($burgerExtras, $sauceExtras),
            ],
            [
                'name'        => 'Spicy Crispy Chicken Burger',
                'description' => 'Crispy fried chicken thigh, spicy slaw, pickled jalapeños, sriracha mayo.',
                'price'       => 13.49,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Veggie Bean Burger',
                'description' => 'Black bean & quinoa patty, guacamole, roasted red pepper, sprouts.',
                'price'       => 11.99,
                'sale_price'  => 9.99,
                'image'       => 'https://images.unsplash.com/photo-1520072959219-c595dc870360?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Tower Burger',
                'description' => 'Triple patty tower with bacon, egg, cheese, and all the toppings.',
                'price'       => 18.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1561758033-d89a9ad46330?w=400&h=400&fit=crop',
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
                'image'       => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => [['name'=>'Extra Cheese Sauce','price'=>1.00],['name'=>'Add Jalapeños','price'=>0.50]],
            ],
            [
                'name'        => 'Onion Rings',
                'description' => 'Golden battered onion rings served with ranch dipping sauce.',
                'price'       => 5.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => $sauceExtras,
            ],
            [
                'name'        => 'Chicken Wings (6pc)',
                'description' => 'Crispy wings tossed in your choice of buffalo, BBQ, or honey garlic sauce.',
                'price'       => 10.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1608039755401-742074f0548d?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => [['name'=>'Buffalo Sauce','price'=>0],['name'=>'BBQ Sauce','price'=>0],['name'=>'Honey Garlic','price'=>0],['name'=>'Extra Sauce','price'=>0.50]],
            ],
            [
                'name'        => 'Sweet Potato Fries',
                'description' => 'Seasoned sweet potato fries with chipotle mayo dip.',
                'price'       => 6.49,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1574894709920-11b28e7367e3?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => [],
            ],
            [
                'name'        => 'Mac & Cheese Bites',
                'description' => 'Crispy fried mac and cheese bites, golden and gooey inside.',
                'price'       => 7.49,
                'sale_price'  => 5.99,
                'image'       => 'https://images.unsplash.com/photo-1543352634-a1c51d9f1fa7?w=400&h=400&fit=crop',
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
                'image'       => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => [['name'=>'Vanilla','price'=>0],['name'=>'Chocolate','price'=>0],['name'=>'Strawberry','price'=>0],['name'=>'Whipped Cream','price'=>0.50]],
            ],
            [
                'name'        => 'Fresh Lemonade',
                'description' => 'Freshly squeezed lemonade with mint and a pinch of sea salt.',
                'price'       => 4.49,
                'sale_price'  => 3.49,
                'image'       => 'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => $drinkExtras,
            ],
            [
                'name'        => 'Craft Soda',
                'description' => 'House-made craft sodas in rotating seasonal flavors.',
                'price'       => 3.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1527960471264-932f39eb5846?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => $drinkExtras,
            ],
            [
                'name'        => 'Iced Coffee',
                'description' => 'Cold brew iced coffee with your choice of milk.',
                'price'       => 4.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => [['name'=>'Oat Milk','price'=>0.50],['name'=>'Extra Shot','price'=>1.00],['name'=>'Sugar-free Syrup','price'=>0]],
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
                'name'        => 'Brownie Sundae',
                'description' => 'Warm chocolate brownie, vanilla ice cream, hot fudge, whipped cream, cherry on top.',
                'price'       => 7.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1564355808539-22fda35bed7e?w=400&h=400&fit=crop',
                'is_featured' => true,
                'options'     => [['name'=>'Extra Fudge','price'=>0.50],['name'=>'No Ice Cream','price'=>-1.00]],
            ],
            [
                'name'        => 'NY Style Cheesecake',
                'description' => 'Rich and creamy New York cheesecake with strawberry compote.',
                'price'       => 6.99,
                'sale_price'  => 5.49,
                'image'       => 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => [['name'=>'Strawberry Topping','price'=>0],['name'=>'Blueberry Topping','price'=>0],['name'=>'Caramel Drizzle','price'=>0.50]],
            ],
            [
                'name'        => 'Churros & Chocolate',
                'description' => 'Crispy cinnamon churros served with warm Belgian chocolate dipping sauce.',
                'price'       => 5.99,
                'sale_price'  => null,
                'image'       => 'https://images.unsplash.com/photo-1624374053855-39a5a872c52f?w=400&h=400&fit=crop',
                'is_featured' => false,
                'options'     => [['name'=>'Extra Chocolate','price'=>0.75]],
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
        $this->command->info('   Storefront URL : '.url('/restaurant/'.$slug));
        $this->command->info('   Owner login    : owner@burgerhouse.demo / Demo@123456');
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\CartService;

class StorefrontController extends Controller
{
    /**
     * الصفحة الرئيسية للمطعم: تعرض المنتجات المميزة + معلومات المطعم
     */
    public function index(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // جلب التصنيفات النشطة مع منتجاتها المتاحة
        $categories = $restaurant->categories()
            ->withoutGlobalScopes()   // نحتاج ByPass لأن TenantService فارغ هنا
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['products' => function ($q) {
                $q->withoutGlobalScopes()
                  ->where('is_available', true)
                  ->orderBy('sort_order');
            }])
            ->get();

        $featured = $restaurant->products()
            ->withoutGlobalScopes()
            ->where('is_available', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        // Products with active sale price (offers section)
        $offers = $restaurant->products()
            ->withoutGlobalScopes()
            ->where('is_available', true)
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $cart = new CartService($slug);

        return view('customer.index', compact('restaurant', 'categories', 'featured', 'offers', 'cart'));
    }

    /**
     * صفحة القائمة الكاملة (مفيلترة حسب التصنيف)
     */
    public function menu(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $categories = $restaurant->categories()
            ->withoutGlobalScopes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['products' => function ($q) {
                $q->withoutGlobalScopes()
                  ->where('is_available', true)
                  ->orderBy('sort_order');
            }])
            ->get();

        $cart = new CartService($slug);

        return view('customer.menu', compact('restaurant', 'categories', 'cart'));
    }
}

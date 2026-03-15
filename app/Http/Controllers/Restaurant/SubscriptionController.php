<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    /**
     * صفحة الاشتراك الحالي مع إحصائيات الاستخدام
     */
    public function show()
    {
        $restaurant = auth()->user()->restaurant;
        $stats      = $this->subscriptionService->usageStats($restaurant);
        $plans      = Plan::active()->get();

        return view('restaurant.subscription.show', compact('stats', 'plans', 'restaurant'));
    }

    /**
     * صفحة انتهاء الاشتراك
     */
    public function expired()
    {
        $restaurant = auth()->user()->restaurant;
        $plans      = Plan::active()->get();

        return view('restaurant.subscription.expired', compact('restaurant', 'plans'));
    }
}

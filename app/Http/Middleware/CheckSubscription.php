<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;

/**
 * يتحقق أن المطعم لديه اشتراك نشط.
 *
 * إذا انتهى الاشتراك → يعيد التوجيه إلى صفحة "subscription.expired".
 * استثناءات: صفحة الإعدادات وصفحة الاشتراك نفسها تظل متاحة.
 */
class CheckSubscription
{
    public function __construct(private SubscriptionService $subscriptionService)
    {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        // السماح بالوصول لصفحات الاشتراك والإعدادات حتى لو انتهى الاشتراك
        if ($request->routeIs('restaurant.subscription.*') ||
            $request->routeIs('restaurant.settings.*')) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! $user->restaurant) {
            return $next($request);
        }

        if ($this->subscriptionService->isExpired($user->restaurant)) {
            return redirect()->route('restaurant.subscription.expired');
        }

        return $next($request);
    }
}

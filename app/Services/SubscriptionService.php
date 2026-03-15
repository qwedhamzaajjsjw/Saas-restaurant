<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Subscription;

/**
 * خدمة الاشتراكات: تتحقق من حدود الخطة وتوفر إحصائيات الاستخدام.
 *
 * كل method تأخذ Restaurant وتعيد قرار منطقي.
 * null في max_* يعني "بلا حد" (unlimited).
 */
class SubscriptionService
{
    // ── الاشتراك النشط ────────────────────────────────────────────────────

    /**
     * يعيد الاشتراك النشط مع بياناته والخطة.
     */
    public function getActive(Restaurant $restaurant): ?Subscription
    {
        return $restaurant->subscriptions()
            ->with('plan')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>', now());
            })
            ->latest('starts_at')
            ->first();
    }

    /**
     * هل الاشتراك منتهي (لا يوجد اشتراك نشط)؟
     */
    public function isExpired(Restaurant $restaurant): bool
    {
        return $this->getActive($restaurant) === null;
    }

    // ── فحص حدود الخطة ──────────────────────────────────────────────────

    /**
     * هل يمكن إضافة منتج جديد؟
     */
    public function canAddProduct(Restaurant $restaurant): bool
    {
        $subscription = $this->getActive($restaurant);
        if (! $subscription) {
            return false;
        }

        $limit = $subscription->plan->max_products ?? null;
        if ($limit === null || $limit === 0) {
            return true; // بلا حد
        }

        $current = $restaurant->products()->withoutTrashed()->count();
        return $current < $limit;
    }

    /**
     * هل يمكن إضافة تصنيف جديد؟
     */
    public function canAddCategory(Restaurant $restaurant): bool
    {
        $subscription = $this->getActive($restaurant);
        if (! $subscription) {
            return false;
        }

        $limit = $subscription->plan->max_categories ?? null;
        if ($limit === null || $limit === 0) {
            return true;
        }

        $current = $restaurant->categories()->withoutTrashed()->count();
        return $current < $limit;
    }

    /**
     * هل يمكن استقبال طلب جديد هذا الشهر؟
     */
    public function canReceiveOrder(Restaurant $restaurant): bool
    {
        $subscription = $this->getActive($restaurant);
        if (! $subscription) {
            return false;
        }

        $limit = $subscription->plan->max_orders_per_month ?? null;
        if ($limit === null || $limit === 0) {
            return true;
        }

        $current = $restaurant->orders()
            ->withoutGlobalScopes()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        return $current < $limit;
    }

    // ── إحصائيات الاستخدام ───────────────────────────────────────────────

    /**
     * يعيد مصفوفة كاملة بالاستخدام الحالي مقارنةً بالخطة.
     *
     * @return array{
     *   subscription: ?Subscription,
     *   plan: ?object,
     *   products: array{used: int, limit: ?int, percentage: int},
     *   categories: array{used: int, limit: ?int, percentage: int},
     *   orders: array{used: int, limit: ?int, percentage: int},
     *   is_expired: bool,
     *   days_remaining: ?int,
     * }
     */
    public function usageStats(Restaurant $restaurant): array
    {
        $subscription = $this->getActive($restaurant);
        $plan         = $subscription?->plan;

        $productsUsed    = $restaurant->products()->withoutTrashed()->count();
        $categoriesUsed  = $restaurant->categories()->withoutTrashed()->count();
        $ordersUsed      = $restaurant->orders()
            ->withoutGlobalScopes()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('status', ['cancelled'])
            ->count();

        $productsLimit   = $plan?->max_products   ?? null;
        $categoriesLimit = $plan?->max_categories ?? null;
        $ordersLimit     = $plan?->max_orders_per_month ?? null;

        $daysRemaining = $subscription?->ends_at
            ? (int) now()->diffInDays($subscription->ends_at, false)
            : null;

        return [
            'subscription'   => $subscription,
            'plan'           => $plan,
            'is_expired'     => $subscription === null,
            'days_remaining' => $daysRemaining,

            'products' => [
                'used'       => $productsUsed,
                'limit'      => $productsLimit,
                'percentage' => $this->pct($productsUsed, $productsLimit),
            ],
            'categories' => [
                'used'       => $categoriesUsed,
                'limit'      => $categoriesLimit,
                'percentage' => $this->pct($categoriesUsed, $categoriesLimit),
            ],
            'orders' => [
                'used'       => $ordersUsed,
                'limit'      => $ordersLimit,
                'percentage' => $this->pct($ordersUsed, $ordersLimit),
            ],
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function pct(int $used, ?int $limit): int
    {
        if ($limit === null || $limit === 0) {
            return 0; // بلا حد → لا نعرض شريط تقدم
        }
        return (int) min(100, round($used / $limit * 100));
    }
}

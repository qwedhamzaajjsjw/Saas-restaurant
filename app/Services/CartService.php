<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * إدارة سلة التسوق عبر Session
 * كل مطعم له مفتاح session مستقل: cart.{slug}
 */
class CartService
{
    protected string $sessionKey;

    public function __construct(string $restaurantSlug)
    {
        $this->sessionKey = 'cart.'.$restaurantSlug;
    }

    // ── قراءة السلة ──────────────────────────────────────────────────

    public function all(): array
    {
        return session($this->sessionKey, []);
    }

    public function count(): int
    {
        return collect($this->all())->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return empty($this->all());
    }

    // ── إضافة منتج ───────────────────────────────────────────────────

    public function add(Product $product, int $quantity = 1, ?string $notes = null): void
    {
        $cart = $this->all();
        $key  = $product->id;

        if (isset($cart[$key])) {
            // إذا كان موجوداً نزيد الكمية فقط
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'price'        => (float) ($product->sale_price ?? $product->price),
                'image'        => $product->image,
                'quantity'     => $quantity,
                'notes'        => $notes,
            ];
        }

        session([$this->sessionKey => $cart]);
    }

    // ── تحديث الكمية ─────────────────────────────────────────────────

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->all();

        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session([$this->sessionKey => $cart]);
        }
    }

    // ── حذف منتج ─────────────────────────────────────────────────────

    public function remove(int $productId): void
    {
        $cart = $this->all();
        unset($cart[$productId]);
        session([$this->sessionKey => $cart]);
    }

    // ── إفراغ السلة ──────────────────────────────────────────────────

    public function clear(): void
    {
        session()->forget($this->sessionKey);
    }

    // ── الإجماليات ───────────────────────────────────────────────────

    public function subtotal(): float
    {
        return collect($this->all())
            ->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function total(float $deliveryFee = 0, float $taxRate = 0): float
    {
        $subtotal = $this->subtotal();
        $tax      = $subtotal * ($taxRate / 100);
        return $subtotal + $tax + $deliveryFee;
    }

    // ── تحويل السلة إلى Collection للـ Views ─────────────────────────

    public function items(): Collection
    {
        return collect($this->all());
    }
}

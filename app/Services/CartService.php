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

    public function add(Product $product, int $quantity = 1, ?string $notes = null, array $extras = []): void
    {
        $cart = $this->all();

        // Unique key: productId alone if no extras, productId_hash if with extras
        $key = empty($extras) ? (string) $product->id : $product->id.'_'.substr(md5(json_encode($extras)), 0, 8);

        // Extras price sum
        $extrasPrice = collect($extras)->sum('price');
        $unitPrice   = (float) ($product->sale_price ?? $product->price) + (float) $extrasPrice;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'cart_key'   => $key,
                'name'       => $product->name,
                'price'      => $unitPrice,
                'image'      => $product->image,
                'quantity'   => $quantity,
                'notes'      => $notes,
                'extras'     => $extras,
            ];
        }

        session([$this->sessionKey => $cart]);
    }

    // ── تحديث الكمية ─────────────────────────────────────────────────

    public function update(string $cartKey, int $quantity): void
    {
        $cart = $this->all();

        if ($quantity <= 0) {
            $this->remove($cartKey);
            return;
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $quantity;
            session([$this->sessionKey => $cart]);
        }
    }

    // ── حذف منتج ─────────────────────────────────────────────────────

    public function remove(string $cartKey): void
    {
        $cart = $this->all();
        unset($cart[$cartKey]);
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

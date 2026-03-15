<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * تأكيد الطلب وحفظه في قاعدة البيانات
     * POST /restaurant/{slug}/checkout
     */
    public function checkout(Request $request, string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        if (! $restaurant->accepts_orders) {
            return back()->with('error', 'عذراً، المطعم لا يقبل طلبات حالياً.');
        }

        $cart = new CartService($slug);

        if ($cart->isEmpty()) {
            return redirect()->route('customer.index', $slug)
                ->with('error', 'سلتك فارغة، أضف منتجات أولاً.');
        }

        $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:150',
            'type'             => 'required|in:dine_in,takeaway,delivery',
            'delivery_address' => 'required_if:type,delivery|nullable|string|max:255',
            'delivery_city'    => 'nullable|string|max:100',
            'payment_method'   => 'required|in:cash,card,online',
            'notes'            => 'nullable|string|max:500',
        ]);

        // حساب الإجماليات
        $deliveryFee = $request->type === 'delivery' ? (float) ($restaurant->settings()->where('key','delivery_fee')->value('value') ?? 0) : 0;
        $taxRate     = (float) ($restaurant->settings()->where('key','tax_rate')->value('value') ?? 0);
        $subtotal    = $cart->subtotal();
        $taxAmount   = $subtotal * ($taxRate / 100);
        $total       = $subtotal + $taxAmount + $deliveryFee;

        DB::beginTransaction();
        try {
            $order = Order::withoutGlobalScopes()->create([
                'restaurant_id'    => $restaurant->id,
                'customer_id'      => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'type'             => $request->type,
                'delivery_address' => $request->delivery_address,
                'delivery_city'    => $request->delivery_city,
                'delivery_notes'   => $request->notes,
                'subtotal'         => $subtotal,
                'tax_amount'       => $taxAmount,
                'delivery_fee'     => $deliveryFee,
                'discount_amount'  => 0,
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
                'status'           => 'pending',
                'notes'            => $request->notes,
            ]);

            // حفظ عناصر الطلب (snapshot للأسعار)
            foreach ($cart->all() as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'unit_price'   => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                    'notes'        => $item['notes'] ?? null,
                ]);
            }

            $cart->clear();
            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تسجيل الطلب، يرجى المحاولة مجدداً.');
        }

        return redirect()->route('customer.order.confirmation', [$slug, $order->id]);
    }

    /**
     * صفحة تأكيد الطلب
     * GET /restaurant/{slug}/order/{order}/confirmation
     */
    public function confirmation(string $slug, int $order)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $order = Order::withoutGlobalScopes()
            ->where('id', $order)
            ->where('restaurant_id', $restaurant->id)
            ->with('items')
            ->firstOrFail();

        return view('customer.confirmation', compact('restaurant', 'order'));
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restaurant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * عرض صفحة السلة
     */
    public function index(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $cart = new CartService($slug);

        return view('customer.cart', compact('restaurant', 'cart'));
    }

    /**
     * إضافة منتج إلى السلة
     * POST /restaurant/{slug}/cart/add
     * body: product_id, quantity (optional), notes (optional)
     */
    public function add(Request $request, string $slug)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:99',
            'notes'      => 'sometimes|nullable|string|max:255',
            'extras'     => 'sometimes|nullable|json',
        ]);

        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // التحقق أن المنتج ينتمي لهذا المطعم
        $product = Product::withoutGlobalScopes()
            ->where('id', $request->product_id)
            ->where('restaurant_id', $restaurant->id)
            ->where('is_available', true)
            ->firstOrFail();

        $extras = $request->extras ? json_decode($request->extras, true) : [];

        $cart = new CartService($slug);
        $cart->add($product, $request->integer('quantity', 1), $request->notes, $extras ?? []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $cart->count(),
                'message' => 'تمت الإضافة إلى السلة',
            ]);
        }

        return back()->with('success', 'تمت الإضافة إلى السلة بنجاح');
    }

    /**
     * تحديث كمية منتج في السلة
     * PATCH /restaurant/{slug}/cart/{item}  — item = product_id
     */
    public function update(Request $request, string $slug, string $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $cart = new CartService($slug);
        $cart->update($item, $request->integer('quantity'));

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'count'    => $cart->count(),
                'subtotal' => number_format($cart->subtotal(), 2),
            ]);
        }

        return back();
    }

    /**
     * حذف منتج من السلة
     * DELETE /restaurant/{slug}/cart/{item}  — item = product_id
     */
    public function remove(Request $request, string $slug, string $item)
    {
        $cart = new CartService($slug);
        $cart->remove($item);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => $cart->count(),
            ]);
        }

        return back()->with('success', 'تم حذف المنتج من السلة');
    }
}

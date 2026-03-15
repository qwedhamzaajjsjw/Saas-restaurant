<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('status')) {
            $query->where('is_available', $request->status === 'available');
        }

        $products   = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::active()->get();

        return view('restaurant.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('menu')->active()->get();
        return view('restaurant.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'      => ['required', 'exists:categories,id'],
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'sale_price'       => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'is_available'     => ['boolean'],
            'is_featured'      => ['boolean'],
            'preparation_time' => ['integer', 'min:1'],
            'calories'         => ['nullable', 'integer', 'min:0'],
            'sort_order'       => ['integer', 'min:0'],
            'image'            => ['nullable', 'image', 'max:2048'],
        ]);

        $data['is_available']     = $request->boolean('is_available', true);
        $data['is_featured']      = $request->boolean('is_featured');
        $data['slug']             = Str::slug($data['name']);
        $data['preparation_time'] = $data['preparation_time'] ?? 15;

        // رفع الصورة
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('restaurant.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::with('menu')->active()->get();
        return view('restaurant.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'      => ['required', 'exists:categories,id'],
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'sale_price'       => ['nullable', 'numeric', 'min:0'],
            'is_available'     => ['boolean'],
            'is_featured'      => ['boolean'],
            'preparation_time' => ['integer', 'min:1'],
            'calories'         => ['nullable', 'integer', 'min:0'],
            'sort_order'       => ['integer', 'min:0'],
            'image'            => ['nullable', 'image', 'max:2048'],
        ]);

        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured']  = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('restaurant.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('restaurant.products.index')
            ->with('success', 'Product deleted.');
    }

    // تبديل حالة التوفر بسرعة من الجدول
    public function toggleAvailable(Product $product)
    {
        $product->update(['is_available' => ! $product->is_available]);
        return back()->with('success', 'Product availability updated.');
    }
}

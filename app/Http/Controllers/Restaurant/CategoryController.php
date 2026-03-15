<?php
namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('menu')->withCount('products')->latest()->get();
        return view('restaurant.categories.index', compact('categories'));
    }

    public function create()
    {
        $menus = Menu::active()->get();
        return view('restaurant.categories.create', compact('menus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'menu_id'     => ['required', 'exists:menus,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Category::create($data);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        $menus = Menu::active()->get();
        return view('restaurant.categories.edit', compact('category', 'menus'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'menu_id'     => ['required', 'exists:menus,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);

        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('restaurant.categories.index')
            ->with('success', 'Category deleted.');
    }
}

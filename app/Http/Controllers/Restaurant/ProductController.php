<?php
namespace App\Http\Controllers\Restaurant;
use App\Http\Controllers\Controller;
class ProductController extends Controller
{
    public function index()  { return view('restaurant.products.index'); }
    public function create() { return view('restaurant.products.create'); }
    public function store()  { /* Phase 7 */ }
    public function edit($id)   { return view('restaurant.products.edit'); }
    public function update($id) { /* Phase 7 */ }
    public function destroy($id) { /* Phase 7 */ }
}

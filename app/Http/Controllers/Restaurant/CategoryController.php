<?php
namespace App\Http\Controllers\Restaurant;
use App\Http\Controllers\Controller;
class CategoryController extends Controller
{
    public function index()  { return view('restaurant.categories.index'); }
    public function create() { return view('restaurant.categories.create'); }
    public function store()  { /* Phase 7 */ }
    public function edit($id)   { return view('restaurant.categories.edit'); }
    public function update($id) { /* Phase 7 */ }
    public function destroy($id) { /* Phase 7 */ }
}

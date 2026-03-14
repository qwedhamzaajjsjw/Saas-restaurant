<?php
namespace App\Http\Controllers\Restaurant;
use App\Http\Controllers\Controller;
class MenuController extends Controller
{
    public function index()  { return view('restaurant.menus.index'); }
    public function create() { return view('restaurant.menus.create'); }
    public function store()  { /* Phase 7 */ }
    public function edit($id)   { return view('restaurant.menus.edit'); }
    public function update($id) { /* Phase 7 */ }
    public function destroy($id) { /* Phase 7 */ }
}

<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
class RestaurantController extends Controller
{
    public function index()  { return view('super-admin.restaurants.index'); }
    public function create() { return view('super-admin.restaurants.create'); }
    public function store()  { /* Phase 6 */ }
    public function show($id)  { /* Phase 6 */ }
    public function edit($id)  { return view('super-admin.restaurants.edit'); }
    public function update($id) { /* Phase 6 */ }
    public function destroy($id) { /* Phase 6 */ }
}

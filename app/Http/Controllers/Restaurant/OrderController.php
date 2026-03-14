<?php
namespace App\Http\Controllers\Restaurant;
use App\Http\Controllers\Controller;
class OrderController extends Controller
{
    public function index()         { return view('restaurant.orders.index'); }
    public function show($id)       { return view('restaurant.orders.show'); }
    public function updateStatus()  { /* Phase 8 */ }
}

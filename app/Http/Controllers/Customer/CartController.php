<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
class CartController extends Controller
{
    public function index($slug)       { return view('customer.cart'); }
    public function add($slug)         { /* Phase 8 */ }
    public function update($slug, $item) { /* Phase 8 */ }
    public function remove($slug, $item) { /* Phase 8 */ }
}

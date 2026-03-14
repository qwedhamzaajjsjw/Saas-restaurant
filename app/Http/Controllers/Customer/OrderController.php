<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
class OrderController extends Controller
{
    public function checkout($slug)              { /* Phase 8 */ }
    public function confirmation($slug, $order)  { return view('customer.confirmation'); }
}

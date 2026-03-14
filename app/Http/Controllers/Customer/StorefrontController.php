<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
class StorefrontController extends Controller
{
    public function index($slug) { return view('customer.index'); }
    public function menu($slug)  { return view('customer.menu'); }
}

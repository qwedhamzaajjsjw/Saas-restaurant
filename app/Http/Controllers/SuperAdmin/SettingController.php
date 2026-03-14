<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
class SettingController extends Controller
{
    public function index()  { return view('super-admin.settings.index'); }
    public function update() { /* Phase 6 */ }
}

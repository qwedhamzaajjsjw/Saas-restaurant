<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
class UserController extends Controller
{
    public function index()  { return view('super-admin.users.index'); }
    public function create() { return view('super-admin.users.create'); }
    public function store()  { /* Phase 6 */ }
    public function edit($id)  { return view('super-admin.users.edit'); }
    public function update($id) { /* Phase 6 */ }
    public function destroy($id) { /* Phase 6 */ }
}

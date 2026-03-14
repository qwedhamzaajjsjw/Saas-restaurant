<?php
namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
class PlanController extends Controller
{
    public function index()  { return view('super-admin.plans.index'); }
    public function create() { return view('super-admin.plans.create'); }
    public function store()  { /* Phase 9 */ }
    public function edit($id)  { return view('super-admin.plans.edit'); }
    public function update($id) { /* Phase 9 */ }
    public function destroy($id) { /* Phase 9 */ }
}

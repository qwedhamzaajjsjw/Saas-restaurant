<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Services\InstallerService;
use Illuminate\Http\Request;

class InstallerController extends Controller
{
    public function __construct(protected InstallerService $installer) {}

    /** Step 1 – Server requirements */
    public function index()
    {
        $php        = $this->installer->checkPhpVersion();
        $extensions = $this->installer->checkExtensions();
        $permissions = $this->installer->checkPermissions();
        $allPassed  = $this->installer->allRequirementsPassed();

        return view('installer.index', compact('php', 'extensions', 'permissions', 'allPassed'));
    }

    /** Step 2 – Database form */
    public function database()
    {
        return view('installer.database');
    }

    /** Step 2 POST – save DB config & write .env */
    public function saveDatabase(Request $request)
    {
        // Full validation & logic in Phase 4
        return redirect()->route('installer.migrate');
    }

    /** Step 3+4 – Run migrations */
    public function migrate(Request $request)
    {
        // Full logic in Phase 4
        return redirect()->route('installer.admin');
    }

    /** Step 5 – Super admin form */
    public function admin()
    {
        return view('installer.admin');
    }

    /** Step 5 POST – Create super admin */
    public function saveAdmin(Request $request)
    {
        // Full logic in Phase 4
        return redirect()->route('installer.complete');
    }

    /** Step 6 – Complete */
    public function complete()
    {
        return view('installer.complete');
    }
}

<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Installer\AdminRequest;
use App\Http\Requests\Installer\DatabaseRequest;
use App\Services\InstallerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallerController extends Controller
{
    public function __construct(protected InstallerService $installer) {}

    // ── Step 1: Requirements ─────────────────────────────────────────────

    public function index()
    {
        $php         = $this->installer->checkPhpVersion();
        $extensions  = $this->installer->checkExtensions();
        $permissions = $this->installer->checkPermissions();
        $allPassed   = $this->installer->allRequirementsPassed();

        return view('installer.index', compact('php', 'extensions', 'permissions', 'allPassed'));
    }

    // ── Step 2: Database Form ────────────────────────────────────────────

    public function database()
    {
        if (! $this->installer->allRequirementsPassed()) {
            return redirect()->route('installer.index')
                ->with('error', 'Please fix all requirement issues before continuing.');
        }

        return view('installer.database', [
            'defaults' => [
                'app_url' => request()->root(),
                'db_host' => '127.0.0.1',
                'db_port' => '3306',
                'db_name' => '',
                'db_user' => 'root',
                'db_pass' => '',
            ],
        ]);
    }

    // ── Step 2 POST: Save DB Config → write .env ─────────────────────────

    public function saveDatabase(DatabaseRequest $request)
    {
        $data = $request->validated();

        // Test the connection before writing anything
        if (! $this->installer->testDatabaseConnection($data)) {
            return back()
                ->withInput()
                ->with('error', 'Could not connect to the database. Please check your credentials.');
        }

        // Write the .env file
        $this->installer->writeEnvFile($data);

        // Store DB data temporarily in session for the migrate step
        session(['installer_db' => $data]);

        return redirect()->route('installer.migrate');
    }

    // ── Step 3+4: Run Migrations ─────────────────────────────────────────

    public function migrate()
    {
        if (! session()->has('installer_db')) {
            return redirect()->route('installer.database')
                ->with('error', 'Please configure the database first.');
        }

        // Run migrations
        $migrations = $this->installer->runMigrations();

        if (! $migrations['success']) {
            return redirect()->route('installer.database')
                ->with('error', 'Migration failed: '.$migrations['output']);
        }

        // Seed plans
        $seeding = $this->installer->runSeeders();

        return view('installer.migrate', [
            'migrationOutput' => $migrations['output'],
            'seedOutput'      => $seeding['output'] ?? '',
            'success'         => $migrations['success'],
        ]);
    }

    // ── Step 5: Create Super Admin ───────────────────────────────────────

    public function admin()
    {
        if (! session()->has('installer_db')) {
            return redirect()->route('installer.database');
        }

        return view('installer.admin');
    }

    public function saveAdmin(AdminRequest $request)
    {
        $data = $request->validated();

        // Check if a super_admin already exists (prevent re-running)
        if (\App\Models\User::where('role', 'super_admin')->exists()) {
            return redirect()->route('installer.complete')
                ->with('warning', 'A super admin account already exists.');
        }

        $admin = $this->installer->createSuperAdmin($data);

        // Mark installation as complete
        $this->installer->markAsInstalled();

        // Flush installer session data
        session()->forget('installer_db');

        // Log the admin in directly
        Auth::login($admin);

        return redirect()->route('installer.complete');
    }

    // ── Step 6: Complete ─────────────────────────────────────────────────

    public function complete()
    {
        return view('installer.complete');
    }
}

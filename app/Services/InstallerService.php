<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InstallerService
{
    protected string $minPhpVersion = '8.2.0';

    protected array $requiredExtensions = [
        'pdo', 'pdo_mysql', 'mbstring', 'openssl',
        'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo',
    ];

    protected array $writablePaths = [
        'storage'               => '',
        'storage/logs'          => 'logs',
        'storage/framework'     => 'framework',
        'storage/framework/cache'    => 'framework/cache',
        'storage/framework/sessions' => 'framework/sessions',
        'storage/framework/views'    => 'framework/views',
        'bootstrap/cache'       => '',
    ];

    // ── Requirements ─────────────────────────────────────────────────────

    public function checkPhpVersion(): array
    {
        $current = PHP_VERSION;
        $passed  = version_compare($current, $this->minPhpVersion, '>=');
        return ['label' => 'PHP >= '.$this->minPhpVersion, 'current' => $current, 'passed' => $passed];
    }

    public function checkExtensions(): array
    {
        return array_map(fn($ext) => [
            'label'  => $ext,
            'passed' => extension_loaded($ext),
        ], $this->requiredExtensions);
    }

    public function checkPermissions(): array
    {
        $results = [];
        foreach ($this->writablePaths as $label => $rel) {
            $path = $rel ? storage_path($rel) : (str_contains($label, 'bootstrap') ? base_path('bootstrap/cache') : storage_path());
            $results[] = ['label' => $label, 'passed' => is_writable($path)];
        }
        return $results;
    }

    public function allRequirementsPassed(): bool
    {
        if (! $this->checkPhpVersion()['passed']) return false;
        foreach ($this->checkExtensions() as $e)  { if (! $e['passed'])  return false; }
        foreach ($this->checkPermissions() as $p) { if (! $p['passed']) return false; }
        return true;
    }

    // ── Database ─────────────────────────────────────────────────────────

    public function testDatabaseConnection(array $data): bool
    {
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', $data['db_host'], $data['db_port'], $data['db_name']);
            new \PDO($dsn, $data['db_user'], $data['db_pass'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function writeEnvFile(array $data): bool
    {
        // Read the current .env (already created/seeded by public/index.php pre-boot)
        // Fall back to .env.example if somehow .env is still missing.
        $envPath  = base_path('.env');
        $template = File::exists($envPath)
            ? File::get($envPath)
            : File::get(base_path('.env.example'));

        $appName = $data['app_name'] ?? 'Restaurant SaaS';
        $appUrl  = rtrim($data['app_url'] ?? request()->root(), '/');

        $map = [
            '/^APP_NAME=.*$/m'        => 'APP_NAME="'.str_replace('"', '\\"', $appName).'"',
            '/^APP_ENV=.*$/m'         => 'APP_ENV=production',
            '/^APP_DEBUG=.*$/m'       => 'APP_DEBUG=false',
            '/^APP_URL=.*$/m'         => 'APP_URL='.$appUrl,
            '/^APP_TIMEZONE=.*$/m'    => 'APP_TIMEZONE='.($data['timezone'] ?? 'UTC'),
            '/^DB_CONNECTION=.*$/m'   => 'DB_CONNECTION=mysql',
            '/^DB_HOST=.*$/m'         => 'DB_HOST='.($data['db_host'] ?? '127.0.0.1'),
            '/^DB_PORT=.*$/m'         => 'DB_PORT='.($data['db_port'] ?? '3306'),
            '/^DB_DATABASE=.*$/m'     => 'DB_DATABASE='.($data['db_name'] ?? ''),
            '/^DB_USERNAME=.*$/m'     => 'DB_USERNAME='.($data['db_user'] ?? ''),
            '/^DB_PASSWORD=.*$/m'     => 'DB_PASSWORD='.($data['db_pass'] ?? ''),
        ];

        // Apply regex replacements (handles any existing value, not just defaults)
        $contents = $template;
        foreach ($map as $pattern => $replacement) {
            $contents = preg_replace($pattern, $replacement, $contents);
        }

        // Remove SQLite-only / DB_URL lines
        $lines = array_filter(explode("\n", $contents), fn($line) => ! str_contains($line, 'DB_URL='));

        File::put(base_path('.env'), implode("\n", $lines));

        // Reload config so new DB values are picked up
        Artisan::call('config:clear');
        Artisan::call('key:generate', ['--force' => true]);

        return true;
    }

    // ── Migrations & Seeds ───────────────────────────────────────────────

    public function runMigrations(): array
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            return ['success' => true, 'output' => $output];
        } catch (\Throwable $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }

    public function runSeeders(): array
    {
        try {
            Artisan::call('db:seed', ['--class' => 'PlanSeeder', '--force' => true]);
            return ['success' => true, 'output' => Artisan::output()];
        } catch (\Throwable $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }

    // ── Super Admin ──────────────────────────────────────────────────────

    public function createSuperAdmin(array $data): User
    {
        return User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'role'              => 'super_admin',
            'restaurant_id'     => null,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
    }

    // ── Lock File ────────────────────────────────────────────────────────

    public function markAsInstalled(): void
    {
        File::put(storage_path('installed.lock'), now()->toDateTimeString());
    }

    public function isInstalled(): bool
    {
        return File::exists(storage_path('installed.lock'));
    }
}

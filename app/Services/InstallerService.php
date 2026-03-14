<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Handles all installation steps:
 * - Server requirement checks
 * - .env file generation
 * - Database connection testing
 * - Running migrations
 * - Writing the installed.lock file
 */
class InstallerService
{
    /**
     * PHP extensions required to run the application.
     */
    protected array $requiredExtensions = [
        'pdo',
        'pdo_mysql',
        'mbstring',
        'openssl',
        'tokenizer',
        'xml',
        'ctype',
        'json',
        'bcmath',
        'fileinfo',
    ];

    /**
     * Minimum PHP version required.
     */
    protected string $minPhpVersion = '8.2.0';

    // ── Requirement Checks ───────────────────────────────────────────────

    public function checkPhpVersion(): array
    {
        $current = PHP_VERSION;
        $passed  = version_compare($current, $this->minPhpVersion, '>=');

        return [
            'label'   => 'PHP Version (>= '.$this->minPhpVersion.')',
            'current' => $current,
            'passed'  => $passed,
        ];
    }

    public function checkExtensions(): array
    {
        $results = [];
        foreach ($this->requiredExtensions as $ext) {
            $results[] = [
                'label'  => $ext,
                'passed' => extension_loaded($ext),
            ];
        }
        return $results;
    }

    public function checkPermissions(): array
    {
        $paths = [
            storage_path(),
            storage_path('logs'),
            storage_path('framework'),
            base_path('bootstrap/cache'),
        ];

        $results = [];
        foreach ($paths as $path) {
            $results[] = [
                'label'  => str_replace(base_path().'/', '', $path),
                'passed' => is_writable($path),
            ];
        }
        return $results;
    }

    public function allRequirementsPassed(): bool
    {
        if (! $this->checkPhpVersion()['passed']) {
            return false;
        }
        foreach ($this->checkExtensions() as $ext) {
            if (! $ext['passed']) return false;
        }
        foreach ($this->checkPermissions() as $perm) {
            if (! $perm['passed']) return false;
        }
        return true;
    }

    // ── .env Management ──────────────────────────────────────────────────

    public function testDatabaseConnection(array $data): bool
    {
        try {
            $dsn = "mysql:host={$data['db_host']};port={$data['db_port']};dbname={$data['db_name']}";
            $pdo = new \PDO($dsn, $data['db_user'], $data['db_pass']);
            return $pdo !== null;
        } catch (\Exception) {
            return false;
        }
    }

    public function writeEnvFile(array $data): bool
    {
        $envExample = base_path('.env.example');
        $envTarget  = base_path('.env');

        $contents = File::get($envExample);

        $replacements = [
            'APP_NAME=Laravel'      => 'APP_NAME="Restaurant SaaS"',
            'APP_ENV=local'         => 'APP_ENV=production',
            'APP_DEBUG=true'        => 'APP_DEBUG=false',
            'APP_URL=http://localhost' => 'APP_URL='.$data['app_url'],
            'DB_HOST=127.0.0.1'     => 'DB_HOST='.$data['db_host'],
            'DB_PORT=3306'          => 'DB_PORT='.$data['db_port'],
            'DB_DATABASE=laravel'   => 'DB_DATABASE='.$data['db_name'],
            'DB_USERNAME=root'      => 'DB_USERNAME='.$data['db_user'],
            'DB_PASSWORD='          => 'DB_PASSWORD='.$data['db_pass'],
        ];

        $contents = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $contents
        );

        File::put($envTarget, $contents);

        // Generate app key
        Artisan::call('key:generate', ['--force' => true]);

        return true;
    }

    // ── Migrations ───────────────────────────────────────────────────────

    public function runMigrations(): array
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return ['success' => true, 'output' => Artisan::output()];
        } catch (\Exception $e) {
            return ['success' => false, 'output' => $e->getMessage()];
        }
    }

    // ── Installation Lock ────────────────────────────────────────────────

    public function markAsInstalled(): void
    {
        File::put(storage_path('installed.lock'), date('Y-m-d H:i:s'));
    }

    public function isInstalled(): bool
    {
        return File::exists(storage_path('installed.lock'));
    }
}

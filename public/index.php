<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Auto-detect the application root (one level up from public/)
define('APP_BASE_PATH', dirname(__DIR__));

// ── Pre-boot: self-setup without terminal access ──────────────────────────────
//
//  This block runs before Laravel boots so that a fresh upload works out
//  of the box.  It is intentionally kept simple (no framework classes).
//
(function () {
    $base    = APP_BASE_PATH;
    $env     = $base . '/.env';
    $example = $base . '/.env.example';

    // 1. Auto-create .env from .env.example the very first time
    if (! file_exists($env) && file_exists($example)) {
        copy($example, $env);
    }

    // 2. Auto-generate APP_KEY if missing (needed for sessions during install)
    if (file_exists($env)) {
        $contents = file_get_contents($env);
        if (preg_match('/^APP_KEY=\s*$/m', $contents)) {
            $key      = 'base64:' . base64_encode(random_bytes(32));
            $contents = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $contents);
            file_put_contents($env, $contents);
        }
    }

    // 3. Auto-create required writable directories if they are missing
    $dirs = [
        $base . '/storage',
        $base . '/storage/app',
        $base . '/storage/app/private',
        $base . '/storage/app/public',
        $base . '/storage/framework',
        $base . '/storage/framework/cache',
        $base . '/storage/framework/cache/data',
        $base . '/storage/framework/sessions',
        $base . '/storage/framework/testing',
        $base . '/storage/framework/views',
        $base . '/storage/logs',
        $base . '/bootstrap/cache',
    ];

    foreach ($dirs as $dir) {
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
})();

// ── Boot Laravel ──────────────────────────────────────────────────────────────

// Maintenance mode check
if (file_exists($maintenance = APP_BASE_PATH . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require APP_BASE_PATH . '/vendor/autoload.php';

// Bootstrap and handle the request
/** @var Application $app */
$app = require_once APP_BASE_PATH . '/bootstrap/app.php';

$app->handleRequest(Request::capture());

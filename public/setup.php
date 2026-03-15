<?php
/**
 * Hostinger Deployment Diagnostic & Setup Helper
 * ================================================
 * Upload this file to public_html/setup.php
 * Visit: https://yourdomain.com/setup.php
 * DELETE THIS FILE after deployment is complete!
 */

// ── Security: simple token check (change this!) ──────────────────────────────
$token = $_GET['token'] ?? '';
if ($token !== 'setup2024') {
    http_response_code(403);
    die('<h2 style="color:red">Access denied. Add ?token=setup2024 to the URL.</h2>');
}

$action = $_GET['action'] ?? 'check';

// ── Fix permissions ───────────────────────────────────────────────────────────
if ($action === 'fix') {
    $appRoot = dirname(__DIR__);
    $dirs = [
        $appRoot . '/storage',
        $appRoot . '/storage/app',
        $appRoot . '/storage/app/private',
        $appRoot . '/storage/app/public',
        $appRoot . '/storage/framework',
        $appRoot . '/storage/framework/cache',
        $appRoot . '/storage/framework/cache/data',
        $appRoot . '/storage/framework/sessions',
        $appRoot . '/storage/framework/testing',
        $appRoot . '/storage/framework/views',
        $appRoot . '/storage/logs',
        $appRoot . '/bootstrap/cache',
    ];

    $results = [];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            $ok = mkdir($dir, 0755, true);
            $results[] = ['path' => str_replace($appRoot, '', $dir), 'action' => 'created', 'ok' => $ok];
        } else {
            $ok = chmod($dir, 0755);
            $results[] = ['path' => str_replace($appRoot, '', $dir), 'action' => 'chmod 755', 'ok' => $ok];
        }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo '<h2>Permission Fix Results</h2><ul>';
    foreach ($results as $r) {
        $icon = $r['ok'] ? '✅' : '❌';
        echo "<li>$icon <code>{$r['path']}</code> — {$r['action']}</li>";
    }
    echo '</ul>';
    echo '<p><a href="?token=' . htmlspecialchars($token) . '">← Back to diagnostic</a></p>';
    exit;
}

// ── Gather diagnostics ───────────────────────────────────────────────────────
$appRoot = dirname(__DIR__);

$phpOk      = version_compare(PHP_VERSION, '8.2.0', '>=');
$vendorOk   = is_dir($appRoot . '/vendor');
$envOk      = file_exists($appRoot . '/.env');
$keyOk      = false;

if ($envOk) {
    $env = file_get_contents($appRoot . '/.env');
    $keyOk = (bool) preg_match('/^APP_KEY=base64:.+/m', $env);
}

$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$extResults = [];
foreach ($extensions as $ext) {
    $extResults[$ext] = extension_loaded($ext);
}

$paths = [
    '/storage'                        => $appRoot . '/storage',
    '/storage/logs'                   => $appRoot . '/storage/logs',
    '/storage/framework/sessions'     => $appRoot . '/storage/framework/sessions',
    '/storage/framework/views'        => $appRoot . '/storage/framework/views',
    '/storage/framework/cache'        => $appRoot . '/storage/framework/cache',
    '/bootstrap/cache'                => $appRoot . '/bootstrap/cache',
];

$permResults = [];
foreach ($paths as $label => $path) {
    $permResults[$label] = [
        'exists'   => is_dir($path),
        'writable' => is_writable($path),
    ];
}

$allGood = $phpOk && $vendorOk && $envOk && $keyOk
    && !in_array(false, $extResults)
    && !in_array(['exists' => false, 'writable' => false], $permResults);

// ── HTML Output ──────────────────────────────────────────────────────────────
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deployment Diagnostic — Restaurant SaaS</title>
<style>
  body { font-family: monospace; max-width: 800px; margin: 30px auto; padding: 0 20px; background: #f8f8f8; }
  h1   { color: #e85d04; }
  h2   { border-bottom: 1px solid #ddd; padding-bottom: 6px; }
  .ok  { color: #16a34a; } .fail { color: #dc2626; } .warn { color: #d97706; }
  table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
  td, th { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
  th { background: #f0f0f0; }
  .banner { padding: 14px 18px; border-radius: 8px; margin: 20px 0; font-size: 1.05em; }
  .banner.ok   { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
  .banner.fail { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  a.btn { display:inline-block; background:#e85d04; color:#fff; padding:10px 18px; border-radius:6px; text-decoration:none; margin-top:10px; }
</style>
</head>
<body>
<h1>🍕 Restaurant SaaS — Deployment Diagnostic</h1>
<p>App root: <code><?= htmlspecialchars($appRoot) ?></code></p>

<?php if ($allGood): ?>
<div class="banner ok">✅ All checks passed! Your app should work. <strong>DELETE this file now!</strong></div>
<?php else: ?>
<div class="banner fail">❌ Some checks failed. See details below.</div>
<?php endif; ?>

<h2>1. PHP Version</h2>
<table>
  <tr><th>Check</th><th>Required</th><th>Current</th><th>Status</th></tr>
  <tr>
    <td>PHP Version</td>
    <td>>= 8.2.0</td>
    <td><?= PHP_VERSION ?></td>
    <td class="<?= $phpOk ? 'ok' : 'fail' ?>"><?= $phpOk ? '✅ OK' : '❌ FAIL — Change PHP version in Hostinger panel' ?></td>
  </tr>
</table>

<h2>2. Required Files</h2>
<table>
  <tr><th>File/Folder</th><th>Status</th></tr>
  <tr>
    <td><code>vendor/</code> (Composer dependencies)</td>
    <td class="<?= $vendorOk ? 'ok' : 'fail' ?>"><?= $vendorOk ? '✅ Exists' : '❌ MISSING — Run: composer install --no-dev' ?></td>
  </tr>
  <tr>
    <td><code>.env</code></td>
    <td class="<?= $envOk ? 'ok' : 'fail' ?>"><?= $envOk ? '✅ Exists' : '❌ MISSING — Copy .env.example to .env and configure it' ?></td>
  </tr>
  <tr>
    <td><code>APP_KEY</code> in .env</td>
    <td class="<?= $keyOk ? 'ok' : 'fail' ?>"><?= $keyOk ? '✅ Set' : '❌ MISSING — Run: php artisan key:generate' ?></td>
  </tr>
</table>

<h2>3. PHP Extensions</h2>
<table>
  <tr><th>Extension</th><th>Status</th></tr>
  <?php foreach ($extResults as $ext => $loaded): ?>
  <tr>
    <td><?= $ext ?></td>
    <td class="<?= $loaded ? 'ok' : 'fail' ?>"><?= $loaded ? '✅ Loaded' : '❌ Missing — Enable in Hostinger PHP settings' ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<h2>4. Directory Permissions</h2>
<table>
  <tr><th>Path</th><th>Exists</th><th>Writable</th></tr>
  <?php foreach ($permResults as $label => $info): ?>
  <tr>
    <td><code><?= $label ?></code></td>
    <td class="<?= $info['exists'] ? 'ok' : 'fail' ?>"><?= $info['exists'] ? '✅' : '❌ missing' ?></td>
    <td class="<?= $info['writable'] ? 'ok' : 'fail' ?>"><?= $info['writable'] ? '✅' : '❌ not writable' ?></td>
  </tr>
  <?php endforeach; ?>
</table>

<?php
$needsFix = false;
foreach ($permResults as $info) {
    if (!$info['exists'] || !$info['writable']) { $needsFix = true; break; }
}
if ($needsFix):
?>
<a class="btn" href="?token=<?= htmlspecialchars($token) ?>&action=fix">🔧 Auto-fix directory permissions</a>
<?php endif; ?>

<h2>5. Next Steps</h2>
<ol>
  <?php if (!$phpOk): ?><li class="fail">Go to Hostinger hPanel → <strong>PHP Configuration</strong> → Select PHP <strong>8.2 or 8.3</strong></li><?php endif; ?>
  <?php if (!$vendorOk): ?><li class="fail">Upload the <strong>vendor/</strong> folder or run <code>composer install --no-dev</code> via SSH/terminal</li><?php endif; ?>
  <?php if (!$envOk): ?><li class="fail">Create <strong>.env</strong> file: copy <code>.env.example</code> and set APP_KEY, DB credentials, APP_URL</li><?php endif; ?>
  <?php if (!$keyOk && $envOk): ?><li class="fail">Run <code>php artisan key:generate</code> or add a valid APP_KEY to .env</li><?php endif; ?>
  <?php if ($needsFix): ?><li class="warn">Click the button above to auto-fix directory permissions</li><?php endif; ?>
  <li>Visit <strong>/install/</strong> to start the installation wizard</li>
  <li class="fail"><strong>DELETE setup.php after successful deployment!</strong></li>
</ol>

<p style="color:#999;font-size:.85em;margin-top:40px">⚠️ This file exposes server information. Delete it after use.</p>
</body>
</html>

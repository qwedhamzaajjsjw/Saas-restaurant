<?php
/**
 * Hostinger Deployment Diagnostic & Setup Helper
 * ================================================
 * Visit: https://yourdomain.com/setup.php
 * A unique access token is auto-generated on first visit and
 * stored in ../.setup_token (outside public/).
 * DELETE THIS FILE (or click the button) after deployment.
 */

// ── Auto-generate a one-time token per installation ──────────────────────────
$tokenFile = dirname(__DIR__) . '/.setup_token';

if (!file_exists($tokenFile)) {
    // First visit: generate & persist a cryptographically random token
    $newToken = bin2hex(random_bytes(24)); // 48-char hex
    file_put_contents($tokenFile, $newToken, LOCK_EX);
    chmod($tokenFile, 0600);

    // Show the token to the deployer — they must bookmark/copy this URL
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
          <title>Setup — First Visit</title>
          <style>body{font-family:monospace;max-width:640px;margin:60px auto;padding:0 20px}
          .box{background:#fef9c3;border:1px solid #fde047;padding:20px 24px;border-radius:8px}
          code{background:#f3f4f6;padding:3px 8px;border-radius:4px;font-size:1em}
          a.btn{display:inline-block;background:#e85d04;color:#fff;padding:10px 20px;
                border-radius:6px;text-decoration:none;margin-top:16px}
          </style></head><body>
          <h2>🔐 Setup Token Generated</h2>
          <div class="box">
            <p>Your unique setup token for this installation:</p>
            <p><strong><code>' . htmlspecialchars($newToken) . '</code></strong></p>
            <p>Bookmark or copy the link below — it works only from this server:</p>
          </div>
          <a class="btn" href="?token=' . urlencode($newToken) . '">→ Open Diagnostic Page</a>
          <p style="color:#6b7280;font-size:.85em;margin-top:32px">
            Token stored in <code>../.setup_token</code> outside the web root.<br>
            <strong>Delete setup.php after deployment.</strong>
          </p>
          </body></html>';
    exit;
}

$savedToken = trim(file_get_contents($tokenFile));

// ── Validate token (constant-time compare prevents timing attacks) ────────────
$provided = $_GET['token'] ?? '';
if (!hash_equals($savedToken, $provided)) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><body style="font-family:monospace;max-width:500px;margin:60px auto">
          <h2 style="color:#dc2626">403 — Access Denied</h2>
          <p>Invalid or missing setup token.</p>
          <p>If this is your first visit, go to
             <a href="/setup.php">/setup.php</a> (without a token) to generate one.</p>
          </body></html>';
    exit;
}

$action  = $_GET['action'] ?? 'check';
$appRoot = dirname(__DIR__);

// ── Self-delete action ────────────────────────────────────────────────────────
if ($action === 'delete') {
    $me   = __FILE__;
    $tf   = $tokenFile;
    // Schedule deletion after output is sent
    register_shutdown_function(function () use ($me, $tf) {
        @unlink($tf);
        @unlink($me);
    });
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><body style="font-family:monospace;max-width:500px;margin:60px auto">
          <h2 style="color:#16a34a">✅ Cleanup Complete</h2>
          <p><code>setup.php</code> and the token file have been deleted.</p>
          <p>Your site is now fully secured. <a href="/">Go to homepage →</a></p>
          </body></html>';
    exit;
}

// ── Fix permissions action ────────────────────────────────────────────────────
if ($action === 'fix') {
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
            $ok = @mkdir($dir, 0755, true);
            $results[] = ['path' => str_replace($appRoot, '', $dir), 'action' => 'created', 'ok' => $ok];
        } else {
            $ok = @chmod($dir, 0755);
            $results[] = ['path' => str_replace($appRoot, '', $dir), 'action' => 'chmod 755', 'ok' => $ok];
        }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo '<h2>Permission Fix Results</h2><ul>';
    foreach ($results as $r) {
        echo '<li>' . ($r['ok'] ? '✅' : '❌') . ' <code>' . htmlspecialchars($r['path']) . '</code> — ' . $r['action'] . '</li>';
    }
    echo '</ul><p><a href="?token=' . urlencode($provided) . '">← Back to diagnostic</a></p>';
    exit;
}

// ── Gather diagnostics ────────────────────────────────────────────────────────
$phpOk    = version_compare(PHP_VERSION, '8.2.0', '>=');
$vendorOk = is_dir($appRoot . '/vendor');
$envOk    = file_exists($appRoot . '/.env');
$keyOk    = false;
$debugOff = true;

if ($envOk) {
    $env   = file_get_contents($appRoot . '/.env');
    $keyOk = (bool) preg_match('/^APP_KEY=base64:.+/m', $env);
    // Warn if APP_DEBUG=true is still set
    $debugOff = !preg_match('/^APP_DEBUG=true\s*$/im', $env);
}

$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
$extResults = [];
foreach ($extensions as $ext) {
    $extResults[$ext] = extension_loaded($ext);
}

$paths = [
    '/storage'                    => $appRoot . '/storage',
    '/storage/logs'               => $appRoot . '/storage/logs',
    '/storage/framework/sessions' => $appRoot . '/storage/framework/sessions',
    '/storage/framework/views'    => $appRoot . '/storage/framework/views',
    '/storage/framework/cache'    => $appRoot . '/storage/framework/cache',
    '/bootstrap/cache'            => $appRoot . '/bootstrap/cache',
];

$permResults = [];
$needsFix    = false;
foreach ($paths as $label => $path) {
    $exists   = is_dir($path);
    $writable = $exists && is_writable($path);
    if (!$exists || !$writable) { $needsFix = true; }
    $permResults[$label] = ['exists' => $exists, 'writable' => $writable];
}

$allGood = $phpOk && $vendorOk && $envOk && $keyOk && $debugOff
    && !in_array(false, $extResults, true)
    && !$needsFix;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deployment Diagnostic — Restaurant SaaS</title>
<style>
  body  { font-family: monospace; max-width: 820px; margin: 30px auto; padding: 0 20px; background: #f8f8f8; }
  h1    { color: #e85d04; }
  h2    { border-bottom: 1px solid #ddd; padding-bottom: 6px; }
  .ok   { color: #16a34a; } .fail { color: #dc2626; } .warn { color: #d97706; }
  table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
  td,th { border: 1px solid #ddd; padding: 8px 12px; text-align: left; }
  th    { background: #f0f0f0; }
  .banner { padding: 14px 18px; border-radius: 8px; margin: 20px 0; font-size: 1.05em; }
  .banner.ok   { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
  .banner.fail { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  a.btn        { display:inline-block; padding:10px 18px; border-radius:6px; text-decoration:none; margin-top:10px; color:#fff; }
  a.btn.orange { background:#e85d04; }
  a.btn.red    { background:#dc2626; }
</style>
</head>
<body>
<h1>🍕 Restaurant SaaS — Deployment Diagnostic</h1>

<?php if ($allGood): ?>
<div class="banner ok">✅ All checks passed! Your app should be working correctly.</div>
<?php else: ?>
<div class="banner fail">❌ Some checks failed. See details below and fix them.</div>
<?php endif; ?>

<h2>1. PHP Version</h2>
<table>
  <tr><th>Check</th><th>Required</th><th>Current</th><th>Status</th></tr>
  <tr>
    <td>PHP Version</td><td>&gt;= 8.2.0</td><td><?= PHP_VERSION ?></td>
    <td class="<?= $phpOk ? 'ok' : 'fail' ?>"><?= $phpOk ? '✅ OK' : '❌ Go to hPanel → PHP Configuration → select 8.2+' ?></td>
  </tr>
</table>

<h2>2. Required Files & Configuration</h2>
<table>
  <tr><th>Item</th><th>Status</th></tr>
  <tr>
    <td><code>vendor/</code></td>
    <td class="<?= $vendorOk ? 'ok' : 'fail' ?>"><?= $vendorOk ? '✅ Present' : '❌ MISSING — upload vendor/ or run: composer install --no-dev' ?></td>
  </tr>
  <tr>
    <td><code>.env</code></td>
    <td class="<?= $envOk ? 'ok' : 'fail' ?>"><?= $envOk ? '✅ Present' : '❌ MISSING — copy .env.example to .env and configure' ?></td>
  </tr>
  <tr>
    <td><code>APP_KEY</code></td>
    <td class="<?= $keyOk ? 'ok' : 'fail' ?>"><?= $keyOk ? '✅ Set' : '❌ MISSING — run: php artisan key:generate' ?></td>
  </tr>
  <tr>
    <td><code>APP_DEBUG=false</code></td>
    <td class="<?= $debugOff ? 'ok' : 'fail' ?>"><?= $debugOff ? '✅ Debug is OFF (secure)' : '❌ APP_DEBUG=true — change to false in .env before going live!' ?></td>
  </tr>
</table>

<h2>3. PHP Extensions</h2>
<table>
  <tr><th>Extension</th><th>Status</th></tr>
  <?php foreach ($extResults as $ext => $loaded): ?>
  <tr>
    <td><?= $ext ?></td>
    <td class="<?= $loaded ? 'ok' : 'fail' ?>"><?= $loaded ? '✅ Loaded' : '❌ Missing — enable in Hostinger PHP settings' ?></td>
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

<?php if ($needsFix): ?>
<a class="btn orange" href="?token=<?= urlencode($provided) ?>&action=fix">🔧 Auto-fix directory permissions</a>
<?php endif; ?>

<h2>5. Next Steps</h2>
<ol>
  <?php if (!$phpOk): ?><li class="fail">Set PHP 8.2+ in Hostinger hPanel → PHP Configuration</li><?php endif; ?>
  <?php if (!$vendorOk): ?><li class="fail">Upload <strong>vendor/</strong> folder</li><?php endif; ?>
  <?php if (!$envOk): ?><li class="fail">Create <strong>.env</strong> from <code>.env.example</code> and configure DB + APP_URL</li><?php endif; ?>
  <?php if (!$keyOk): ?><li class="fail">Generate APP_KEY: <code>php artisan key:generate</code></li><?php endif; ?>
  <?php if (!$debugOff): ?><li class="fail">Set <code>APP_DEBUG=false</code> in .env</li><?php endif; ?>
  <?php if ($needsFix): ?><li class="warn">Click Auto-fix button above to fix directory permissions</li><?php endif; ?>
  <li>Visit <strong>/install/</strong> to run the installation wizard</li>
</ol>

<?php if ($allGood): ?>
<hr style="margin-top:30px">
<h2>🗑️ Cleanup — Required Before Going Live</h2>
<p>Click below to permanently delete <code>setup.php</code> and the token file from your server:</p>
<a class="btn red" href="?token=<?= urlencode($provided) ?>&action=delete"
   onclick="return confirm('Delete setup.php? This cannot be undone.')">
  🗑️ Delete setup.php (recommended)
</a>
<?php endif; ?>

<p style="color:#9ca3af;font-size:.8em;margin-top:40px">
  ⚠️ This file exposes server diagnostic information. Delete it after deployment.
</p>
</body>
</html>

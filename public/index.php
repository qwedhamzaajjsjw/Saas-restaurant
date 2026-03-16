<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Absolute path to the Laravel application root on Hostinger
define('APP_BASE_PATH', '/home/u149018080/restaurant');

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = APP_BASE_PATH . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require APP_BASE_PATH . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once APP_BASE_PATH . '/bootstrap/app.php';

$app->handleRequest(Request::capture());

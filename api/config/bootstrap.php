<?php
/**
 * SERAPH BUILD CONSTRUCTION — Bootstrap for the shared API core.
 * Loads config + helper functions + classes used by admin/client panels.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Kolkata');

define('ROOT_PATH', dirname(__DIR__, 2));

// Load credentials & settings from .env BEFORE anything reads them.
require_once __DIR__ . '/env.php';
load_env();

$app_env = getenv('APP_ENV') ?: 'production';
define('APP_ENV', $app_env);

// Secure session defaults
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? '1' : '0');
    ini_set('session.gc_maxlifetime', '7200');
    session_name('SERAPHSSID');
    session_start();
}

// Paths to include
$apiDir = dirname(__DIR__);
$paths = [
    __DIR__ . '/database.php',
    $apiDir . '/functions/helpers.php',
    $apiDir . '/classes/Database.php',
    $apiDir . '/classes/Auth.php',
    $apiDir . '/classes/CSRF.php',
    $apiDir . '/classes/RateLimiter.php',
    $apiDir . '/classes/Project.php',
    $apiDir . '/classes/DailyUpdate.php',
    $apiDir . '/classes/Notification.php',
    $apiDir . '/classes/Audit.php',
    $apiDir . '/classes/SSE.php',
    $apiDir . '/classes/Image.php',
    $apiDir . '/classes/ContactInquiry.php',
    $apiDir . '/classes/Mail.php',
];

foreach ($paths as $file) {
    if (is_file($file)) {
        require_once $file;
    }
}

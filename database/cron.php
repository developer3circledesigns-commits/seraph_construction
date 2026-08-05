<?php
/**
 * SERAPH — Daily maintenance jobs (run via cron).
 * Usage: php database/cron.php [task]
 *   cleanup  — purge expired sessions, stale login attempts, old SSE events
 *
 * Cron example (every day at 3:00 AM):
 *   0 3 * * * /usr/bin/php /path/to/database/cron.php cleanup > /dev/null 2>&1
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("This script must be run from the command line.\n");
}

require dirname(__DIR__) . '/api/config/bootstrap.php';

$task = $argv[1] ?? 'cleanup';

switch ($task) {
    case 'cleanup':
        // Expired sessions
        Database::execute('DELETE FROM user_sessions WHERE expires_at < NOW()');
        // Login attempts older than 24h
        Database::execute('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
        // SSE events older than 1 day
        Database::execute('DELETE FROM sse_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');
        echo "Cleanup complete.\n";
        break;

    default:
        echo "Unknown task: {$task}\n";
        exit(1);
}

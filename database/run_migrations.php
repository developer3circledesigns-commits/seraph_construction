<?php
/**
 * SERAPH BUILD CONSTRUCTION — CLI database installer.
 *
 * Usage (from project root):
 *   php database/run_migrations.php
 *
 * Reads DB credentials from env vars (same as the app) or the
 * defaults in api/config/database.php. Run INSIDE the app container
 * (Docker) or on a machine that can reach the database.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("This script must be run from the command line.\n");
}

require dirname(__DIR__) . '/api/config/database.php';

$c = db_config();
$dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $c['host'], $c['port']);

echo "Connecting to {$c['host']}:{$c['port']}...\n";
$pdo = new PDO($dsn, $c['username'], $c['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$migrations = glob(dirname(__DIR__) . '/database/migrations/*.sql');
natsort($migrations);

foreach ($migrations as $file) {
    $name = basename($file);
    echo "Applying migration: {$name}\n";
    $sql = file_get_contents($file);
    try {
        $pdo->exec($sql);
        echo "  OK\n";
    } catch (PDOException $e) {
        echo "  SKIP (already applied?): " . $e->getMessage() . "\n";
    }
}

echo "\nDone. To seed demo data:\n";
echo "  php database/run_seed.php\n";
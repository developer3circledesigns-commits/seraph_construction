<?php
/**
 * SERAPH BUILD CONSTRUCTION — Database configuration.
 *
 * Credentials come from the environment, populated from a `.env` file
 * (see api/config/env.php) or real environment variables/SetEnv.
 *
 * SECURITY: In `APP_ENV=production` the app FAILS CLOSED — if any required
 * DB credential is missing it raises a clear error instead of silently
 * connecting with weak local defaults. Local dev defaults are only applied
 * outside production.
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';
load_env();

/** Detect if the app is running in production mode. */
function is_production(): bool
{
    $env = env('APP_ENV');
    return ($env === null || $env === '') ? true : ($env === 'production');
}

/**
 * Resolve database credentials from the environment.
 *
 * @throws RuntimeException when production and required creds are missing.
 */
function db_config(): array
{
    load_env();

    $prod = is_production();

    $host     = env('DB_HOST');
    $port     = env('DB_PORT');
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');

    // --- Fail closed in production: never use known local defaults. ---
    if ($prod) {
        $missing = [];
        if ($host === null)     $missing[] = 'DB_HOST';
        if ($port === null)     $missing[] = 'DB_PORT';
        if ($database === null) $missing[] = 'DB_DATABASE';
        if ($username === null) $missing[] = 'DB_USERNAME';
        if ($password === null) $missing[] = 'DB_PASSWORD';

        if ($missing !== []) {
            throw new \RuntimeException(
                'Database credentials are not configured. Set ' . implode(', ', $missing)
                . ' via a .env file or environment variables. (APP_ENV=production)'
            );
        }
    }

    // --- Local dev defaults (XAMPP / docker-compose) only outside production. ---
    if (!$prod) {
        $host     = $host     ?? '127.0.0.1'; // docker-compose overrides to "db"
        $port     = $port     ?? '3306';
        $database = $database ?? 'seraph_construction';
        $username = $username ?? 'seraph';
        $password = $password ?? 'seraph_password';
    }

    return [
        'host'     => (string)$host,
        'port'     => (string)($port ?: '3306'),
        'database' => (string)$database,
        'username' => (string)$username,
        'password' => (string)$password,
    ];
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $c = db_config();

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $c['host'],
        $c['port'],
        $c['database']
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
    ];

    try {
        $pdo = new PDO($dsn, $c['username'], $c['password'], $options);
    } catch (\PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed. Check credentials.']);
        exit;
    }

    return $pdo;
}
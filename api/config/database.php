<?php
/**
 * SERAPH BUILD CONSTRUCTION — Database configuration.
 * Reads credentials from environment variables with sensible local defaults.
 */

function db_config(): array
{
    return [
        'host'     => getenv('DB_HOST')     ?: '127.0.0.1',  // XAMPP local; docker-compose overrides to "db"
        'port'     => getenv('DB_PORT')     ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'seraph_construction',
        'username' => getenv('DB_USERNAME') ?: 'seraph',
        'password' => getenv('DB_PASSWORD') ?: 'seraph_password',
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
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed. Check credentials.']);
        exit;
    }

    return $pdo;
}

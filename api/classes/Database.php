<?php
/**
 * SERAPH BUILD CONSTRUCTION — Database wrapper.
 * Thin PDO facade so the rest of the app depends on one object.
 */

declare(strict_types=1);

class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        try {
            $c = db_config();
        } catch (\RuntimeException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            if (PHP_SAPI === 'cli') {
                fwrite(STDERR, "Configuration error: {$e->getMessage()}\n");
                exit(1);
            }
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html><head><title>Service Unavailable</title></head><body style="font-family:sans-serif;background:#0a0e14;color:#eef2f7;display:grid;place-items:center;height:100vh;margin:0">'
                . '<div style="text-align:center"><h1 style="color:#C79A56">Not Configured</h1>'
                . '<p>The application is missing required environment settings. Please configure your <code>.env</code> file and try again.</p></div></body></html>';
            exit;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $c['host'],
            $c['port'],
            $c['database']
        );

        try {
            self::$pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            if (PHP_SAPI === 'cli') {
                fwrite(STDERR, "Database connection failed: {$e->getMessage()}\n");
                exit(1);
            }
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html><head><title>Service Unavailable</title></head><body style="font-family:sans-serif;background:#0a0e14;color:#eef2f7;display:grid;place-items:center;height:100vh;margin:0">'
                . '<div style="text-align:center"><h1 style="color:#C79A56">Service Unavailable</h1>'
                . '<p>We could not connect to the database. Please try again shortly.</p></div></body></html>';
            exit;
        }

        return self::$pdo;
    }

    /** Run a prepared query with bindings. */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch a single row. */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch all rows. */
    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /** Fetch a single scalar value. */
    public static function scalar(string $sql, array $params = []): mixed
    {
        return self::query($sql, $params)->fetchColumn();
    }

    /** Insert and return last insert id. */
    public static function insert(string $sql, array $params = []): int
    {
        self::query($sql, $params);
        return (int)self::pdo()->lastInsertId();
    }

    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }
}

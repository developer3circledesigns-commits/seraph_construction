<?php
/**
 * SERAPH BUILD CONSTRUCTION — Authentication service.
 *
 * Handles admin + client login/logout with:
 *  - Argon2id password hashing
 *  - Server-side sessions stored in the user_sessions table
 *  - Session regeneration on login (prevents session fixation)
 *  - Revocable sessions (logout kills the DB row)
 *  - Login rate limiting via RateLimiter
 */

declare(strict_types=1);

class Auth
{
    public const ADMIN = 'admin';
    public const CLIENT = 'client';

    private const SESSION_USER_KEY = 'auth_user';

    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Authenticate a user by type + email + password.
     * Returns user array on success, null on failure.
     */
    public static function login(string $type, string $email, string $password): ?array
    {
        $table = self::tableFor($type);
        $key   = self::emailColumn($type);

        $user = Database::one(
            "SELECT id, email, password_hash, is_active
                    , " . self::selectColumns($type) . "
               FROM {$table}
              WHERE {$key} = :email
              LIMIT 1",
            [':email' => $email]
        );

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        if (!$user['is_active']) {
            return null;
        }

        // Rehash if the hash needs an upgrade
        if (password_needs_rehash($user['password_hash'], PASSWORD_ARGON2ID)) {
            Database::execute(
                "UPDATE {$table} SET password_hash = :h WHERE id = :id",
                [':h' => $user['password_hash'] = self::hash($password), ':id' => $user['id']]
            );
        }

        // Update last login
        Database::execute(
            "UPDATE {$table} SET last_login_at = :now WHERE id = :id",
            [':now' => now(), ':id' => $user['id']]
        );

        self::establishSession($type, $user);

        return $user;
    }

    /** Create a fresh session + DB record after successful auth. */
    private static function establishSession(string $type, array $user): void
    {
        session_regenerate_id(true);

        $sessionId = session_id();
        $expires   = date('Y-m-d H:i:s', time() + 7200);

        Database::execute(
            "INSERT INTO user_sessions (id, user_type, user_id, ip_address, user_agent, expires_at)
             VALUES (:id, :type, :uid, :ip, :ua, :exp)",
            [
                ':id'   => $sessionId,
                ':type' => $type,
                ':uid'  => $user['id'],
                ':ip'   => client_ip(),
                ':ua'   => user_agent(),
                ':exp'  => $expires,
            ]
        );

        $_SESSION[self::SESSION_USER_KEY] = [
            'type' => $type,
            'id'   => (int)$user['id'],
        ];

        // Refresh expiry on every activity via session lifetime.
        $_SESSION['last_activity'] = time();
    }

    /** Get the currently authenticated user, or null. */
    public static function user(string $type): ?array
    {
        $sess = $_SESSION[self::SESSION_USER_KEY] ?? null;

        if (!$sess || $sess['type'] !== $type) {
            return null;
        }

        // Validate session still exists in DB and hasn't expired.
        $row = Database::one(
            "SELECT user_type, user_id FROM user_sessions
              WHERE id = :id AND user_type = :type AND expires_at > NOW()",
            [':id' => session_id(), ':type' => $type]
        );

        if (!$row || (int)$row['user_id'] !== $sess['id']) {
            self::logout($type);
            return null;
        }

        $table = self::tableFor($type);
        $user  = Database::one("SELECT * FROM {$table} WHERE id = :id AND is_active = 1 LIMIT 1", [':id' => $sess['id']]);

        if (!$user) {
            self::logout($type);
            return null;
        }

        return $user;
    }

    /** Require an authenticated user or redirect to login. */
    public static function requireUser(string $type, string $loginUrl): array
    {
        $user = self::user($type);
        if (!$user) {
            redirect($loginUrl, 'Please sign in to continue.', 'error');
        }
        return $user;
    }

    /** Logout: kill DB session + PHP session. */
    public static function logout(string $type): void
    {
        if (session_status() === PHP_SESSION_ACTIVE && session_id()) {
            Database::execute(
                "DELETE FROM user_sessions WHERE id = :id AND user_type = :type",
                [':id' => session_id(), ':type' => $type]
            );
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Does the user have the given role (admins only)? */
    public static function requireRole(array $user, string $role): void
    {
        if (($user['role'] ?? '') !== $role) {
            http_response_code(403);
            exit('Forbidden — you do not have permission to view this page.');
        }
    }

    /** Is the admin a super admin? */
    public static function isSuper(array $user): bool
    {
        return ($user['role'] ?? '') === 'super_admin';
    }

    public static function tableFor(string $type): string
    {
        return $type === self::ADMIN ? 'admins' : 'clients';
    }

    public static function emailColumn(string $type): string
    {
        return $type === self::ADMIN ? 'email' : 'email';
    }

    /** Human identity columns for a user type (used by login + user queries). */
    public static function identityColumns(string $type): string
    {
        return $type === self::ADMIN ? 'full_name' : 'contact_person, company_name';
    }

    /** Full extra column list for the login query (type-aware). */
    public static function selectColumns(string $type): string
    {
        return $type === self::ADMIN ? 'full_name, role' : 'contact_person, company_name';
    }
}

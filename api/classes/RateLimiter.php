<?php
/**
 * SERAPH BUILD CONSTRUCTION — Rate limiter.
 *
 * Backed by the login_attempts table. Enforces:
 *  - Login: 5 attempts per identifier per 15 minutes
 *  - Global: 20 attempts per IP per hour
 */

declare(strict_types=1);

class RateLimiter
{
    /** Throttle login attempts for an identifier/IP combo. */
    public static function attempt(string $identifier): bool
    {
        Database::insert(
            "INSERT INTO login_attempts (identifier, ip_address, success) VALUES (:id, :ip, 0)",
            [':id' => $identifier, ':ip' => client_ip()]
        );
        return true;
    }

    /** Record a successful login (resets the failure count). */
    public static function recordSuccess(string $identifier): void
    {
        Database::execute(
            "UPDATE login_attempts SET success = 1
              WHERE identifier = :id AND ip_address = :ip
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [':id' => $identifier, ':ip' => client_ip()]
        );
    }

    /**
     * Check if an identifier is currently locked out.
     * Returns remaining seconds of lockout, or 0 if allowed.
     */
    public static function lockedOut(string $identifier): int
    {
        // Per-identifier: more than 5 failures in 15 min locks for 15 min
        $failures = (int)Database::scalar(
            "SELECT COUNT(*) FROM login_attempts
              WHERE identifier = :id AND success = 0
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [':id' => $identifier]
        );

        if ($failures >= 5) {
            $oldest = Database::scalar(
                "SELECT MIN(attempted_at) FROM login_attempts
                  WHERE identifier = :id AND success = 0
                    AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
                [':id' => $identifier]
            );
            $until = strtotime((string)$oldest) + 900; // 15 min lock
            return max(0, $until - time());
        }

        // Per-IP: more than 20 failures in 1 hour locks for 30 min
        $ipFailures = (int)Database::scalar(
            "SELECT COUNT(*) FROM login_attempts
              WHERE ip_address = :ip AND success = 0
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [':ip' => client_ip()]
        );

        if ($ipFailures >= 20) {
            return 1800;
        }

        return 0;
    }
}

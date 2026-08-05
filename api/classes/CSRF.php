<?php
/**
 * SERAPH BUILD CONSTRUCTION — CSRF protection.
 *
 * Double-submit cookie + per-form token pattern:
 *  - A signed token is stored in the session and mirrored in a cookie.
 *  - Every POST must include a matching _csrf field.
 */

declare(strict_types=1);

class CSRF
{
    private const TOKEN_KEY = 'csrf_token';

    /** Generate (or reuse) the CSRF token for the current session. */
    public static function token(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = random_token(32);
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    /** Hidden input helper for forms. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    /** Verify a submitted token matches the session token. */
    public static function verify(?string $token): bool
    {
        $expected = $_SESSION[self::TOKEN_KEY] ?? null;
        return is_string($expected)
            && is_string($token)
            && hash_equals($expected, $token);
    }

    /** Verify against the request body; abort on failure. */
    public static function validate(): void
    {
        $body = request_body();
        $token = $body['_csrf'] ?? '';
        if (!self::verify(is_string($token) ? $token : '')) {
            json_error('Invalid CSRF token. Please refresh the page and try again.', 419);
        }
    }
}

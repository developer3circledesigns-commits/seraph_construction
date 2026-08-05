<?php
/**
 * SERAPH BUILD CONSTRUCTION — Shared helper functions.
 */

declare(strict_types=1);

/** Escape output for safe HTML rendering. */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Return JSON response and stop. */
function json_response($data, int $status = 200): void
{
    // 419 has no reason phrase in PHP's table; Apache rejects the empty
    // phrase as HTTP/1.1 500, so send it explicitly.
    if ($status === 419) {
        header('HTTP/1.1 419 Authentication Timeout', true, 419);
    } else {
        http_response_code($status);
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Return error JSON. */
function json_error(string $message, int $status = 400): void
{
    json_response(['success' => false, 'error' => $message], $status);
}

/** Return success JSON. */
function json_success($data = [], int $status = 200): void
{
    json_response(['success' => true, 'data' => $data], $status);
}

/** Get request JSON body or POST fields as array. */
function request_body(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (str_contains($contentType, 'application/json')) {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    return $_POST;
}

/** Redirect with optional flash message. */
function redirect(string $url, ?string $flash = null, string $flashType = 'success'): void
{
    if ($flash !== null) {
        $_SESSION['flash'] = ['message' => $flash, 'type' => $flashType];
    }
    header('Location: ' . $url);
    exit;
}

/** Render a flash message block if present. */
function flash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return sprintf(
        '<div class="alert alert--%s" role="alert">%s</div>',
        e($f['type']),
        e($f['message'])
    );
}

/** Generate a random token (crypto-safe). */
function random_token(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/** Current date-time string. */
function now(): string
{
    return date('Y-m-d H:i:s');
}

/** Client IP address. */
function client_ip(): string
{
    $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = explode(',', (string)$_SERVER[$k])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

/** Client user agent. */
function user_agent(): string
{
    return substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 512);
}

/** JSON decode a column safely. */
function json_decode_col(?string $value): mixed
{
    if ($value === null || $value === '') {
        return null;
    }
    $decoded = json_decode($value, true);
    return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
}

/** Format a money value. */
function money($value): string
{
    return '₹' . number_format((float)$value, 2);
}

/** Human friendly "time ago" string. */
function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    return date('d M Y', strtotime($datetime));
}

/** Validate a string is in an allowed list. */
function in_list(mixed $value, array $allowed): bool
{
    return in_array($value, $allowed, true);
}

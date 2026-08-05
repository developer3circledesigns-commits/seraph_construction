<?php
/**
 * SERAPH BUILD CONSTRUCTION — Minimal .env loader.
 *
 * Loads KEY=VALUE pairs from a `.env` file into the PHP process
 * environment (getenv/$_ENV). Real environment variables always win
 * over values from the file (variables already set are never overridden).
 *
 * Search order (first existing, readable file wins):
 *   1. <repo>/.env
 *   2. <repo>/../.env   (one level ABOVE the web root — preferred on shared hosting)
 *
 * This lets DB/backend credentials live in a file that is gitignored
 * and, when placed outside public_html, never reachable over HTTP.
 */

declare(strict_types=1);

/**
 * Resolve the repo root regardless of how/where this file is loaded.
 */
function env_root(): string
{
    if (defined('ROOT_PATH')) {
        return ROOT_PATH;
    }
    return dirname(__DIR__, 2);
}

/**
 * Candidate .env locations, most-specific first.
 */
function env_candidate_paths(): array
{
    $root = env_root();
    return [
        $root . '/.env',
        dirname($root) . '/.env',
    ];
}

/**
 * Load the first available .env file (idempotent guarded by a file sentinel).
 */
function load_env(): void
{
    static $loaded = null;

    foreach (env_candidate_paths() as $file) {
        if (!is_file($file) || !is_readable($file)) {
            continue;
        }
        // Avoid re-reading the same file on repeated calls.
        if ($loaded === $file) {
            return;
        }
        $loaded = $file;
        env_parse_file($file);
        return;
    }
}

/**
 * Parse a single .env file. Returns immediately on the first file.
 */
function env_parse_file(string $path): void
{
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }
        // Support "export KEY=value" prefix commonly seen in .env files.
        if (stripos($line, 'export ') === 0) {
            $line = trim(substr($line, 7));
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if ($key === '') {
            continue;
        }

        // Strip surrounding quotes and inline comments.
        if ((strlen($value) >= 2)
            && (($value[0] === '"' && $value[strlen($value) - 1] === '"')
                || ($value[0] === "'" && $value[strlen($value) - 1] === "'"))) {
            $value = substr($value, 1, -1);
        } else {
            $hash = strpos($value, ' #');
            if ($hash !== false) {
                $value = rtrim(substr($value, 0, $hash));
            }
        }

        // Never override a value that is already set (env wins over file).
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Read an env value (file or real environment) with an optional default.
 */
function env(string $key, ?string $default = null): ?string
{
    $val = getenv($key);
    return ($val === false || $val === '') ? $default : $val;
}
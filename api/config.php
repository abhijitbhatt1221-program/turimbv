<?php
/**
 * TOURIM runtime config helper.
 * Reads values from environment variables first, then from root .env if present.
 * Never commit live secrets into this file.
 */
declare(strict_types=1);

function tourim_env(string $key, ?string $default = null): ?string {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    static $envFile = null;
    if ($envFile === null) {
        $envFile = [];
        $path = dirname(__DIR__) . '/.env';
        if (is_readable($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $envFile[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
            }
        }
    }
    return $envFile[$key] ?? $default;
}

function tourim_base_url(): string {
    $configured = tourim_env('APP_URL', '');
    if ($configured) {
        return rtrim($configured, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $dir = preg_replace('~/api$~', '', $dir);
    return $scheme . '://' . $host . ($dir ? $dir : '');
}

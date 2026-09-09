<?php
/**
 * Minimal WordPress compatibility layer for local previews.
 *
 * This file is loaded only when index.php is run outside WordPress, typically
 * with: php -S localhost:8000 index.php
 */

if (defined('ABSPATH')) {
    return;
}

define('ABSPATH', __DIR__ . DIRECTORY_SEPARATOR);

function tourim_local_base_url(): string {
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $themeRoot = realpath(__DIR__);

    if ($documentRoot && $themeRoot) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $themeRoot = str_replace('\\', '/', $themeRoot);
        if (str_starts_with(strtolower($themeRoot), strtolower(rtrim($documentRoot, '/')))) {
            $relative = trim(substr($themeRoot, strlen(rtrim($documentRoot, '/'))), '/');
            return $relative === '' ? '' : '/' . $relative;
        }
    }

    return '';
}

function get_template_directory(): string { return __DIR__; }
function get_template_directory_uri(): string { return tourim_local_base_url(); }
function home_url(string $path = ''): string {
    return rtrim(tourim_local_base_url(), '/') . '/' . ltrim($path, '/');
}
function trailingslashit(string $value): string { return rtrim($value, '/\\') . '/'; }
function esc_url($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function esc_attr($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function esc_html($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function wp_kses_post($value): string { return (string)$value; }
function wp_json_encode($value): string { return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); }
function wp_head(): void {}
function wp_footer(): void {}
function language_attributes(): void { echo 'lang="en"'; }
function body_class(): void { echo 'class="tourim-local-preview"'; }
function bloginfo(string $show): void { if ($show === 'charset') { echo 'UTF-8'; } }

// Registration hooks in functions.php are irrelevant without WordPress.
function add_action(...$args): bool { return true; }
function add_filter(...$args): bool { return true; }
function add_theme_support(...$args): bool { return true; }

require_once __DIR__ . '/functions.php';

function tourim_local_route(): void {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = tourim_local_base_url();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }

    $key = isset($_GET['route']) ? trim((string)$_GET['route'], '/') : trim(rawurldecode($path), '/');
    $key = $key === '' ? '' : basename($key);
    if (str_ends_with(strtolower($key), '.php')) {
        $key = substr($key, 0, -4);
    }

    $map = tourim_wp_page_map();
    $template = $map[$key] ?? $map[strtolower($key)] ?? null;
    if ($template === null || !is_readable(__DIR__ . '/' . $template)) {
        http_response_code(404);
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Not found</title>';
        echo '<body><h1>404 - Page not found</h1><p><a href="' . esc_url(home_url('/')) . '">Return home</a></p></body></html>';
        return;
    }

    http_response_code(200);
    ob_start(function (string $html): string {
        return preg_replace_callback(
            '/href=(["\'])([^"\'#?]+)\.html([^"\']*)\1/i',
            static function (array $match): string {
                $route = basename($match[2]);
                return 'href=' . $match[1] . 'index.php?route=' . rawurlencode($route) . $match[3] . $match[1];
            },
            $html
        ) ?? $html;
    });
    require __DIR__ . '/' . $template;
    ob_end_flush();
}

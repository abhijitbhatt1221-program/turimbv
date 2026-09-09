<?php
if (!defined('ABSPATH')) { exit; }

define('TOURIM_WP_THEME_VERSION', '1.0.0');

function tourim_wp_theme_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
}
add_action('after_setup_theme', 'tourim_wp_theme_setup');

function tourim_wp_page_map(): array {
    return [
        '' => 'front-page.php',
        'home' => 'front-page.php',
        'index' => 'front-page.php',
        'index.html' => 'front-page.php',
        'about' => 'about.php',
        'about.html' => 'about.php',
        'destinations' => 'destinations.php',
        'destinations.html' => 'destinations.php',
        'destination-detail' => 'destination-detail.php',
        'destination-detail.html' => 'destination-detail.php',
        'packages' => 'packages.php',
        'packages.html' => 'packages.php',
        'services' => 'services.php',
        'services.html' => 'services.php',
        'package-detail' => 'package-detail.php',
        'package-detail.html' => 'package-detail.php',
        'hotels' => 'hotels.php',
        'hotels.html' => 'hotels.php',
        'blogs' => 'blogs.php',
        'blogs.html' => 'blogs.php',
        'blog-detail' => 'blog-detail.php',
        'blog-detail.html' => 'blog-detail.php',
        'offers' => 'offers.php',
        'offers.html' => 'offers.php',
        'contact' => 'contact.php',
        'contact.html' => 'contact.php',
        'gallery' => 'gallery.php',
        'gallery.html' => 'gallery.php',
        'policy' => 'policy.php',
        'policy.html' => 'policy.php',
        'privacy' => 'privacy.php',
        'privacy.html' => 'privacy.php',
        'terms' => 'terms.php',
        'terms.html' => 'terms.php',
        'production-tools' => 'production-tools.php',
        'production-tools.html' => 'production-tools.php',
        'launch-check' => 'launch-check.php',
        'launch-check.html' => 'launch-check.php',
        'admin' => 'admin.php',
        'admin.html' => 'admin.php',
        'ADMIN' => 'admin.php',
    ];
}

function tourim_wp_include_template(string $template): void {
    $file = get_template_directory() . '/' . ltrim($template, '/');
    if (is_readable($file)) {
        status_header(200);
        nocache_headers();
        include $file;
        exit;
    }
}

function tourim_wp_route_static_pages(): void {
    if (is_admin() || wp_doing_ajax()) { return; }
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $home_path = parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    if ($home_path !== '/' && str_starts_with($path, rtrim($home_path, '/'))) {
        $path = substr($path, strlen(rtrim($home_path, '/')));
    }
    $path = trim($path, '/');
    $key = $path === '' ? '' : basename($path);
    $map = tourim_wp_page_map();
    if (isset($map[$path])) { tourim_wp_include_template($map[$path]); }
    if (isset($map[$key])) { tourim_wp_include_template($map[$key]); }
    $lower_path = strtolower($path);
    $lower_key = strtolower($key);
    if (isset($map[$lower_path])) { tourim_wp_include_template($map[$lower_path]); }
    if (isset($map[$lower_key])) { tourim_wp_include_template($map[$lower_key]); }
}
add_action('template_redirect', 'tourim_wp_route_static_pages', 0);

function tourim_wp_disable_canonical_for_html($redirect_url, $requested_url) {
    $path = parse_url($requested_url, PHP_URL_PATH) ?: '';
    if (str_ends_with(strtolower($path), '.html') || preg_match('~/ADMIN/?$~', $path)) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'tourim_wp_disable_canonical_for_html', 10, 2);

function tourim_wp_create_page(string $title, string $slug, string $template): int {
    $existing = get_page_by_path($slug);
    if ($existing instanceof WP_Post) {
        update_post_meta($existing->ID, '_wp_page_template', $template);
        return (int)$existing->ID;
    }
    $page_id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '<!-- TOURIM page rendered by theme template. -->',
    ]);
    if (!is_wp_error($page_id)) {
        update_post_meta((int)$page_id, '_wp_page_template', $template);
        return (int)$page_id;
    }
    return 0;
}

function tourim_wp_after_switch_theme(): void {
    $pages = [
        ['Home', 'home', 'front-page.php'],
        ['About', 'about', 'about.php'],
        ['Destinations', 'destinations', 'destinations.php'],
        ['Packages', 'packages', 'packages.php'],
        ['Services', 'services', 'services.php'],
        ['Hotels', 'hotels', 'hotels.php'],
        ['Blogs', 'blogs', 'blogs.php'],
        ['Offers', 'offers', 'offers.php'],
        ['Contact', 'contact', 'contact.php'],
        ['Gallery', 'gallery', 'gallery.php'],
        ['Policy', 'policy', 'policy.php'],
        ['Privacy', 'privacy', 'privacy.php'],
        ['Terms', 'terms', 'terms.php'],
        ['Customer Queries', 'admin', 'admin.php'],
    ];
    $home_id = 0;
    foreach ($pages as $p) {
        $id = tourim_wp_create_page($p[0], $p[1], $p[2]);
        if ($p[1] === 'home') { $home_id = $id; }
    }
    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }
}
add_action('after_switch_theme', 'tourim_wp_after_switch_theme');

function tourim_wp_queries_menu(): void {
    add_menu_page(
        'Customer Queries',
        'Customer Queries',
        'edit_pages',
        'tourim-customer-queries',
        function() {
            echo '<div class="wrap"><h1>Customer Queries</h1><p><a class="button button-primary" href="' . esc_url(home_url('/admin')) . '">Open Query Manager</a></p></div>';
        },
        'dashicons-email-alt',
        3
    );
}
add_action('admin_menu', 'tourim_wp_queries_menu');

function tourim_wp_template_meta(): void {
    $theme_uri = get_template_directory_uri();
    echo "\n<script>\n";
    echo "window.TOURIM_THEME_URI = " . wp_json_encode($theme_uri) . ";\n";
    echo "window.TOURIM_API_BASE = " . wp_json_encode(trailingslashit($theme_uri) . 'api/') . ";\n";
    echo "window.TOURIM_WORDPRESS_HOME = " . wp_json_encode(home_url('/')) . ";\n";
    echo "</script>\n";
}

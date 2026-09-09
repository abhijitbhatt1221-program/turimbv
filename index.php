<?php
/**
 * Main index for TOURIM preview & WordPress theme compatibility.
 */

if (!defined('ABSPATH')) {
    require_once __DIR__ . '/local-preview.php';
    tourim_local_route();
    exit;
}

get_header();
if (is_front_page()) {
    get_template_part('front-page');
} else {
    tourim_wp_route_static_pages();
}
get_footer();

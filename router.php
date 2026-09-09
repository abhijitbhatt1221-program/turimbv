<?php
/**
 * Local preview router for PHP built-in server.
 * Run with: php -S localhost:8000 router.php
 */

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
$filePath = __DIR__ . $path;

// Serve static assets directly if file exists and is not PHP
if ($path !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    if ($ext !== 'php') {
        return false; // let PHP built-in server handle the file
    }
    // Direct API executions
    if (str_starts_with($path, '/api/')) {
        require $filePath;
        return true;
    }
}

// Check if direct API endpoint requested without .php extension
if (str_starts_with($path, '/api/')) {
    $apiFile = $filePath . '.php';
    if (file_exists($apiFile)) {
        require $apiFile;
        return true;
    }
}

// Route through local WordPress preview shim
require_once __DIR__ . '/local-preview.php';
tourim_local_route();
return true;

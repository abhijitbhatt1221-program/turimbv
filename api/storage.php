<?php
/**
 * TOURIM storage helper.
 * Default: JSON file storage for simple hosting.
 * Production: set DB_DRIVER=mysql and DB_HOST/DB_NAME/DB_USER/DB_PASS to store the CMS database in MySQL.
 */
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function tourim_mysql_enabled(): bool {
    return strtolower((string)tourim_env('DB_DRIVER', 'json')) === 'mysql';
}

function tourim_pdo(): ?PDO {
    if (!tourim_mysql_enabled()) {
        return null;
    }
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $host = tourim_env('DB_HOST', 'localhost');
    $name = tourim_env('DB_NAME', 'tourim');
    $user = tourim_env('DB_USER', '');
    $pass = tourim_env('DB_PASS', '');
    if (!$user || !$name) {
        return null;
    }
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("CREATE TABLE IF NOT EXISTS tourim_kv (k VARCHAR(80) PRIMARY KEY, v LONGTEXT NOT NULL, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        return $pdo;
    } catch (Throwable $e) {
        error_log('TOURIM MySQL connection failed: ' . $e->getMessage());
        return null;
    }
}

function tourim_ensure_file(string $dbFile, string $seedFile): void {
    $dir = dirname($dbFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!file_exists($dbFile)) {
        if (file_exists($seedFile)) {
            copy($seedFile, $dbFile);
        } else {
            file_put_contents($dbFile, '{}', LOCK_EX);
        }
    }
}

function tourim_read_file_db(string $dbFile, string $seedFile): array {
    tourim_ensure_file($dbFile, $seedFile);
    $raw = file_get_contents($dbFile);
    $data = json_decode($raw ?: '{}', true);
    if (!is_array($data)) {
        $raw = file_exists($seedFile) ? file_get_contents($seedFile) : '{}';
        $data = json_decode($raw ?: '{}', true);
        if (!is_array($data)) {
            $data = [];
        }
        tourim_write_file_db($dbFile, $data);
    }
    return $data;
}

function tourim_write_file_db(string $dbFile, array $data): void {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('Invalid JSON data');
    }
    if (file_put_contents($dbFile, $json, LOCK_EX) === false) {
        throw new RuntimeException('Could not write data file. Check file permissions.');
    }
}

function tourim_read_db(string $dbFile, string $seedFile): array {
    $pdo = tourim_pdo();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT v FROM tourim_kv WHERE k='site_db' LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row && isset($row['v'])) {
                $data = json_decode((string)$row['v'], true);
                if (is_array($data)) {
                    $data['storage_driver'] = 'mysql';
                    return $data;
                }
            }
            $seed = tourim_read_file_db($dbFile, $seedFile);
            tourim_write_db($dbFile, $seed);
            $seed['storage_driver'] = 'mysql';
            return $seed;
        } catch (Throwable $e) {
            error_log('TOURIM MySQL read failed, falling back to JSON: ' . $e->getMessage());
        }
    }
    $data = tourim_read_file_db($dbFile, $seedFile);
    $data['storage_driver'] = 'json';
    return $data;
}

function tourim_write_db(string $dbFile, array $data): void {
    unset($data['storage_driver']);
    $pdo = tourim_pdo();
    if ($pdo) {
        try {
            $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new RuntimeException('Invalid JSON payload');
            }
            $stmt = $pdo->prepare("INSERT INTO tourim_kv (k, v) VALUES ('site_db', :v) ON DUPLICATE KEY UPDATE v=VALUES(v)");
            $stmt->execute([':v' => $json]);
            return;
        } catch (Throwable $e) {
            error_log('TOURIM MySQL write failed, falling back to JSON: ' . $e->getMessage());
        }
    }
    tourim_write_file_db($dbFile, $data);
}

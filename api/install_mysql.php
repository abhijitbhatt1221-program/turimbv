<?php
/**
 * TOURIM MySQL installer.
 * Use after setting DB_DRIVER=mysql, DB_HOST, DB_NAME, DB_USER, DB_PASS in .env.
 * Recommended: delete or rename this file after successful installation.
 */
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/storage.php';
$pdo = tourim_pdo();
if (!$pdo) { echo json_encode(['ok'=>false,'error'=>'MySQL not configured. Set DB_DRIVER=mysql and credentials in .env.']); exit; }
$sql = file_get_contents(__DIR__ . '/../database/mysql_schema.sql') ?: '';
$sql = preg_replace('/^\s*--.*$/m', '', $sql);
$statements = array_filter(array_map('trim', explode(';', $sql)));
$executed = 0;
try {
    foreach ($statements as $statement) {
        if ($statement !== '') { $pdo->exec($statement); $executed++; }
    }
    $dbFile = __DIR__ . '/../data/tourim_db.json';
    $seedFile = __DIR__ . '/../data/tourim_seed.json';
    $db = tourim_read_file_db($dbFile, $seedFile);
    tourim_write_db($dbFile, $db);
    echo json_encode(['ok'=>true,'message'=>'TOURIM MySQL tables are ready and seed data has been synced.','statements_executed'=>$executed]);
} catch (Throwable $e) {
    echo json_encode(['ok'=>false,'error'=>$e->getMessage(),'statements_executed'=>$executed]);
}

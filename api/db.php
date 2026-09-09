<?php
/**
 * TOURIM production-ready PHP data API.
 * - JSON storage by default for shared hosting.
 * - MySQL mode when DB_DRIVER=mysql is configured in .env.
 * - Session admin authentication, password hashing, public/private data separation.
 */
declare(strict_types=1);
$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secureCookie,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
require_once __DIR__ . '/storage.php';

$dbFile = __DIR__ . '/../data/tourim_db.json';
$seedFile = __DIR__ . '/../data/tourim_seed.json';

function respond(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function read_db(string $dbFile, string $seedFile): array {
    try {
        return tourim_read_db($dbFile, $seedFile);
    } catch (Throwable $e) {
        respond(['ok' => false, 'error' => $e->getMessage()], 500);
    }
}

function write_db(string $dbFile, array $data): void {
    try {
        tourim_write_db($dbFile, $data);
    } catch (Throwable $e) {
        respond(['ok' => false, 'error' => $e->getMessage()], 500);
    }
}

function public_db(array $db): array {
    if (isset($db['settings']['adminPassword'])) $db['settings']['adminPassword'] = '';
    if (isset($db['settings']['adminPasswordHash'])) $db['settings']['adminPasswordHash'] = '';
    if (isset($db['users'])) {
        $db['users'] = array_map(function($u){ unset($u['password_hash'], $u['password']); return $u; }, $db['users']);
    }
    if (isset($db['settings']['smtpPassword'])) $db['settings']['smtpPassword'] = '';
    foreach (['queries','events','auditLogs','users','roles','bookings','payments','invoices','vouchers','media','whatsappTemplates','seoRoutes','dashboardStatsBackup'] as $privateKey) {
        unset($db[$privateKey]);
    }
    return $db;
}

function body_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}

function is_admin(): bool { return !empty($_SESSION['tourim_admin']); }

function current_admin_role(): string { return (string)($_SESSION['tourim_role'] ?? 'super_admin'); }

function role_can(string $permission): bool {
    $role = current_admin_role();
    if ($role === 'super_admin') return true;
    $map = [
        'admin' => ['manage_content','manage_enquiries','manage_bookings','manage_payments','view_analytics','manage_settings'],
        'editor' => ['manage_content','view_analytics'],
        'sales' => ['manage_enquiries','manage_bookings','manage_payments','view_analytics'],
        'viewer' => ['view_analytics']
    ];
    return in_array($permission, $map[$role] ?? [], true);
}
function require_permission(string $permission): void {
    if (!is_admin()) respond(['ok'=>false,'error'=>'Admin login required'],403);
    if (!role_can($permission)) respond(['ok'=>false,'error'=>'Permission denied for role '.current_admin_role()],403);
}


function verify_admin_login(array $db, string $login, string $password): array|false {
    $settings = $db['settings'] ?? [];
    $adminEmail = (string)($settings['adminEmail'] ?? 'owner.tourim@gmail.com');
    $adminUsername = (string)($settings['adminUsername'] ?? 'tourimadmin');
    $loginOk = hash_equals($adminEmail, $login) || hash_equals($adminUsername, $login);
    if ($loginOk && $password !== '') {
        $hash = (string)($settings['adminPasswordHash'] ?? '');
        if ($hash !== '' && password_verify($password, $hash)) {
            return ['id' => 'super', 'name' => 'TOURIM Owner', 'role' => 'super_admin'];
        }
        $legacy = (string)($settings['adminPassword'] ?? '');
        if ($legacy !== '' && hash_equals($legacy, $password)) {
            return ['id' => 'super', 'name' => 'TOURIM Owner', 'role' => 'super_admin', 'legacy' => true];
        }
    }
    foreach (($db['users'] ?? []) as $user) {
        if (($user['status'] ?? 'active') !== 'active') continue;
        $uLogin = hash_equals((string)($user['email'] ?? ''), $login) || hash_equals((string)($user['username'] ?? ''), $login);
        if ($uLogin && !empty($user['password_hash']) && password_verify($password, (string)$user['password_hash'])) {
            return ['id' => (string)($user['id'] ?? ''), 'name' => (string)($user['name'] ?? 'Admin'), 'role' => (string)($user['role'] ?? 'admin')];
        }
    }
    return false;
}

function upgrade_admin_password_hash(string $dbFile, array &$db, string $password): void {
    $hash = (string)($db['settings']['adminPasswordHash'] ?? '');
    if ($password !== '' && $hash === '') {
        $db['settings']['adminPasswordHash'] = password_hash($password, PASSWORD_DEFAULT);
        $db['settings']['adminPassword'] = '';
        write_db($dbFile, $db);
    }
}

function audit_log(array &$db, string $action, string $details = ''): void {
    $db['auditLogs'] = $db['auditLogs'] ?? [];
    array_unshift($db['auditLogs'], [
        'id' => 'audit_' . bin2hex(random_bytes(4)),
        'action' => $action,
        'details' => $details,
        'user' => $_SESSION['tourim_admin_name'] ?? 'System',
        'role' => $_SESSION['tourim_role'] ?? 'public',
        'created_at' => date('c')
    ]);
    $db['auditLogs'] = array_slice($db['auditLogs'], 0, 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'get';
$db = read_db($dbFile, $seedFile);

if ($action === 'health') {
    respond(['ok' => true, 'message' => 'TOURIM PHP API is running', 'storage' => $db['storage_driver'] ?? 'json']);
}

if ($action === 'get') {
    $private = ($_GET['private'] ?? '') === '1';
    respond(['ok' => true, 'db' => ($private && is_admin()) ? $db : public_db($db)]);
}

if ($action === 'login') {
    $_SESSION['tourim_login_attempts'] = $_SESSION['tourim_login_attempts'] ?? [];
    $_SESSION['tourim_login_attempts'] = array_values(array_filter($_SESSION['tourim_login_attempts'], fn($t) => $t > time() - 900));
    if (count($_SESSION['tourim_login_attempts']) >= 8) { respond(['ok'=>false,'error'=>'Too many login attempts. Try again after 15 minutes.'],429); }
    $input = body_json();
    $login = trim((string)($input['email'] ?? ''));
    $password = (string)($input['password'] ?? '');
    $user = verify_admin_login($db, $login, $password);
    if ($user !== false) {
        if (!empty($user['legacy'])) upgrade_admin_password_hash($dbFile, $db, $password);
        session_regenerate_id(true);
        $_SESSION['tourim_admin'] = true;
        $_SESSION['tourim_admin_name'] = $user['name'];
        $_SESSION['tourim_role'] = $user['role'];
        unset($_SESSION['tourim_login_attempts']);
        audit_log($db, 'login', 'Admin login successful');
        write_db($dbFile, $db);
        respond(['ok' => true, 'role' => $user['role'], 'db' => $db]);
    }
    $_SESSION['tourim_login_attempts'][] = time();
    respond(['ok' => false, 'error' => 'Wrong admin username/email or password'], 401);
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    respond(['ok' => true]);
}

if ($action === 'queries') {
    require_permission('manage_enquiries');
    $queries = array_values($db['queries'] ?? []);
    respond([
        'ok' => true,
        'queries' => $queries,
        'version' => hash('sha256', json_encode($queries, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]'),
    ]);
}

if ($action === 'update_query') {
    require_permission('manage_enquiries');
    $input = body_json();
    $queryId = trim((string)($input['id'] ?? ''));
    $status = trim((string)($input['status'] ?? ''));
    $allowedStatuses = ['New', 'Pending', 'Replied', 'Follow-up', 'Converted', 'Cancelled'];
    if ($queryId === '' || !in_array($status, $allowedStatuses, true)) {
        respond(['ok' => false, 'error' => 'Invalid query or status'], 400);
    }
    $updated = null;
    $db['queries'] = array_values($db['queries'] ?? []);
    foreach ($db['queries'] as &$query) {
        if ((string)($query['id'] ?? '') === $queryId) {
            $query['status'] = $status;
            $query['updated_at'] = date('c');
            $updated = $query;
            break;
        }
    }
    unset($query);
    if ($updated === null) respond(['ok' => false, 'error' => 'Customer query not found'], 404);
    audit_log($db, 'update_query', $queryId . ' changed to ' . $status);
    write_db($dbFile, $db);
    $queries = array_values($db['queries'] ?? []);
    respond([
        'ok' => true,
        'query' => $updated,
        'version' => hash('sha256', json_encode($queries, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]'),
    ]);
}

if ($action === 'delete_query') {
    require_permission('manage_enquiries');
    $input = body_json();
    $queryId = trim((string)($input['id'] ?? ''));
    $before = count($db['queries'] ?? []);
    $db['queries'] = array_values(array_filter($db['queries'] ?? [], fn($query) => (string)($query['id'] ?? '') !== $queryId));
    if ($queryId === '' || count($db['queries']) === $before) {
        respond(['ok' => false, 'error' => 'Customer query not found'], 404);
    }
    audit_log($db, 'delete_query', $queryId . ' deleted');
    write_db($dbFile, $db);
    respond([
        'ok' => true,
        'version' => hash('sha256', json_encode($db['queries'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '[]'),
    ]);
}

if ($action === 'save') {
    require_permission('manage_content');
    $input = body_json();
    $newDb = $input['db'] ?? $input;
    if (!is_array($newDb)) respond(['ok' => false, 'error' => 'Invalid database payload'], 400);
    if (isset($newDb['settings'])) {
        $newPass = trim((string)($newDb['settings']['adminPassword'] ?? ''));
        if ($newPass !== '') {
            $newDb['settings']['adminPasswordHash'] = password_hash($newPass, PASSWORD_DEFAULT);
            $newDb['settings']['adminPassword'] = '';
        } else {
            $newDb['settings']['adminPasswordHash'] = (string)($db['settings']['adminPasswordHash'] ?? '');
            $newDb['settings']['adminPassword'] = '';
        }
    }
    $newDb['updated_at'] = date('c');
    audit_log($newDb, 'save_db', 'Full CMS database saved from admin panel');
    write_db($dbFile, $newDb);
    respond(['ok' => true, 'db' => $newDb]);
}

if ($action === 'enquiry') {
    $input = body_json();
    $safe = [
        'id' => isset($input['id']) ? preg_replace('/[^A-Za-z0-9_#-]/', '', (string)$input['id']) : '#Q' . random_int(1000, 9999),
        'name' => trim(strip_tags((string)($input['name'] ?? 'Guest'))),
        'phone' => preg_replace('/[^0-9+ -]/', '', (string)($input['phone'] ?? '')),
        'email' => filter_var((string)($input['email'] ?? ''), FILTER_SANITIZE_EMAIL),
        'package' => trim(strip_tags((string)($input['package'] ?? 'Custom Package'))),
        'destination' => trim(strip_tags((string)($input['destination'] ?? ''))),
        'date' => trim(strip_tags((string)($input['date'] ?? ''))),
        'pax' => trim(strip_tags((string)($input['pax'] ?? ''))),
        'budget' => trim(strip_tags((string)($input['budget'] ?? 'Not shared'))),
        'country' => trim(strip_tags((string)($input['country'] ?? 'India'))),
        'source' => trim(strip_tags((string)($input['source'] ?? 'Website'))),
        'status' => 'New',
        'note' => trim(strip_tags((string)($input['note'] ?? ''))),
        'created_at' => date('c')
    ];
    $db['queries'] = $db['queries'] ?? [];
    array_unshift($db['queries'], $safe);
    $db['analytics']['enquiries'] = (int)($db['analytics']['enquiries'] ?? 0) + 1;
    $db['analytics']['interested'] = (int)($db['analytics']['interested'] ?? 0) + 1;
    $db['notifications'] = $db['notifications'] ?? [];
    array_unshift($db['notifications'], ['id'=>'query_'.preg_replace('/[^A-Za-z0-9_-]/', '', $safe['id']), 'queryId'=>$safe['id'], 'title'=>'New query received from '.$safe['name'], 'text'=>($safe['package'] ?: ($safe['destination'] ?: 'Custom Trip')).' - '.($safe['phone'] ?: 'No phone'), 'created_at'=>date('c'), 'read'=>false]);
    audit_log($db, 'new_enquiry', $safe['name'] . ' - ' . $safe['destination']);
    write_db($dbFile, $db);
    respond(['ok' => true, 'query' => $safe]);
}

if ($action === 'event') {
    $input = body_json();
    $eventType = preg_replace('/[^a-zA-Z0-9_:-]/', '', (string)($input['event_type'] ?? 'event'));
    $db['events'] = $db['events'] ?? [];
    $event = [
        'event_type' => $eventType,
        'item_type' => trim(strip_tags((string)($input['item_type'] ?? ''))),
        'item_id' => trim(strip_tags((string)($input['item_id'] ?? ''))),
        'title' => trim(strip_tags((string)($input['title'] ?? ''))),
        'path' => trim(strip_tags((string)($input['path'] ?? ($_SERVER['HTTP_REFERER'] ?? '')))),
        'device' => trim(strip_tags((string)($input['device'] ?? 'browser'))),
        'created_at' => $input['created_at'] ?? date('c')
    ];
    $db['events'][] = $event;
    $db['events'] = array_slice($db['events'], -3000);
    if ($eventType === 'page_view') $db['analytics']['visitors'] = (int)($db['analytics']['visitors'] ?? 0) + 1;
    if (in_array($eventType, ['package_view','destination_view','whatsapp_clicked','phone_clicked','enquiry_form_opened','offer_clicked'], true)) $db['analytics']['interested'] = (int)($db['analytics']['interested'] ?? 0) + 1;
    write_db($dbFile, $db);
    respond(['ok' => true]);
}

respond(['ok' => false, 'error' => 'Unknown action'], 404);

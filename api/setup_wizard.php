<?php
/**
 * TOURIM 100% Launch Readiness Wizard API.
 * This file does not require live secrets to be committed. It checks hosting,
 * directory permissions, PHP extensions, config placeholders, SEO domain files,
 * and can write safe non-secret launch settings when admin is logged in.
 */
declare(strict_types=1);
$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secureCookie,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/storage.php';

$dbFile = __DIR__ . '/../data/tourim_db.json';
$seedFile = __DIR__ . '/../data/tourim_seed.json';

function wizard_out(array $payload, int $status=200): void { http_response_code($status); echo json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); exit; }
function wizard_body(): array { $raw=file_get_contents('php://input'); $data=json_decode($raw?:'{}',true); return is_array($data)?$data:[]; }
function wizard_admin_required(): void { if(empty($_SESSION['tourim_admin'])) wizard_out(['ok'=>false,'error'=>'Admin login required'],403); }
function wizard_check(string $label, bool $pass, string $fix='', string $level='required'): array { return ['label'=>$label,'pass'=>$pass,'level'=>$level,'fix'=>$pass?'':$fix]; }
function wizard_is_placeholder(?string $v): bool { $v=trim((string)$v); return $v==='' || stripos($v,'PASTE_')!==false || stripos($v,'your_')!==false || stripos($v,'change-this')!==false || stripos($v,'example')!==false; }
function wizard_env_lines(): array {
    $path = dirname(__DIR__) . '/.env';
    if(!is_readable($path)) return [];
    $out=[]; $lines=file($path, FILE_IGNORE_NEW_LINES) ?: [];
    foreach($lines as $line){ if(trim($line)==='' || str_starts_with(trim($line),'#') || !str_contains($line,'=')) continue; [$k,$v]=explode('=',$line,2); $out[trim($k)] = trim($v, " \t\n\r\0\x0B\"'"); }
    return $out;
}
function wizard_write_env(array $updates): void {
    $path = dirname(__DIR__) . '/.env';
    $existing = wizard_env_lines();
    $merged = array_merge($existing, $updates);
    $header = "# TOURIM live environment file\n# Keep this file private. Never share it publicly.\n";
    $lines=[];
    foreach($merged as $k=>$v){ $k=preg_replace('/[^A-Z0-9_]/','', strtoupper((string)$k)); $lines[]=$k.'="'.str_replace('"','\\"',(string)$v).'"'; }
    if(file_put_contents($path, $header.implode("\n",$lines)."\n", LOCK_EX) === false){ throw new RuntimeException('Could not write .env. Check file permissions.'); }
}
function wizard_write_domain_files(string $url): void {
    $url = rtrim($url, '/');
    $root = dirname(__DIR__);
    $pages = ['', 'packages.html','destinations.html','hotels.html','blogs.html','offers.html','gallery.html','about.html','contact.html','privacy.html','terms.html'];
    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach($pages as $p){ $loc = $url . ($p ? '/'.$p : '/'); $xml .= "  <url><loc>".htmlspecialchars($loc, ENT_XML1)."</loc><changefreq>weekly</changefreq><priority>".($p===''?'1.0':'0.8')."</priority></url>\n"; }
    $xml .= "</urlset>\n";
    file_put_contents($root.'/sitemap.xml', $xml, LOCK_EX);
    file_put_contents($root.'/robots.txt', "User-agent: *\nAllow: /\nDisallow: /api/\nDisallow: /data/\nDisallow: /backups/\nSitemap: {$url}/sitemap.xml\n", LOCK_EX);
}
function wizard_readiness(): array {
    $root = dirname(__DIR__);
    $env = wizard_env_lines();
    $db = tourim_read_db(__DIR__ . '/../data/tourim_db.json', __DIR__ . '/../data/tourim_seed.json');
    $checks=[];
    $checks[] = wizard_check('PHP version 8.0+', version_compare(PHP_VERSION,'8.0.0','>='), 'Use PHP 8.0 or newer on hosting.');
    $checks[] = wizard_check('JSON extension enabled', extension_loaded('json'), 'Enable PHP JSON extension.');
    $checks[] = wizard_check('Fileinfo extension enabled for secure uploads', extension_loaded('fileinfo'), 'Enable PHP fileinfo extension.');
    $checks[] = wizard_check('PDO extension enabled', extension_loaded('pdo'), 'Enable PDO extension.', 'recommended');
    $checks[] = wizard_check('cURL extension enabled for Razorpay/WhatsApp API', extension_loaded('curl'), 'Enable PHP cURL extension.', 'recommended');
    $checks[] = wizard_check('Data folder protected by .htaccess', is_file($root.'/data/.htaccess'), 'Add data/.htaccess protection.');
    $checks[] = wizard_check('Uploads folder available and writable', is_dir($root.'/uploads/media') && is_writable($root.'/uploads/media'), 'Create uploads/media and set writable permission.');
    $checks[] = wizard_check('Documents folder available and writable', is_dir($root.'/documents') && is_writable($root.'/documents'), 'Create documents folder and set writable permission.');
    $checks[] = wizard_check('Backups folder available and writable', is_dir($root.'/backups') && is_writable($root.'/backups'), 'Create backups folder and set writable permission.');
    $checks[] = wizard_check('Runtime config helper exists', is_file(__DIR__.'/config.php'), 'Upload api/config.php.');
    $checks[] = wizard_check('Storage helper exists', is_file(__DIR__.'/storage.php'), 'Upload api/storage.php.');
    $checks[] = wizard_check('Production API exists', is_file(__DIR__.'/production.php'), 'Upload api/production.php.');
    $checks[] = wizard_check('Integrations API exists', is_file(__DIR__.'/integrations.php'), 'Upload api/integrations.php.');
    $checks[] = wizard_check('Admin password hash configured', !empty($db['settings']['adminPasswordHash']), 'Login once and set a strong admin password in Settings.');
    $checks[] = wizard_check('Plain admin password removed', empty($db['settings']['adminPassword'] ?? ''), 'Clear plaintext adminPassword from data.');
    $checks[] = wizard_check('App URL configured', !wizard_is_placeholder(tourim_env('APP_URL','')), 'Set APP_URL to the real TOURIM domain after hosting upload.', 'external');
    $checks[] = wizard_check('App secret configured', !wizard_is_placeholder(tourim_env('APP_SECRET','')), 'Set APP_SECRET to a long random value on the live server.', 'external');
    $checks[] = wizard_check('SMTP credentials configured', !wizard_is_placeholder(tourim_env('SMTP_PASSWORD','')), 'Add Gmail/SMTP app password on server only.', 'external');
    $checks[] = wizard_check('Razorpay keys configured', !wizard_is_placeholder(tourim_env('RAZORPAY_KEY_ID','')) && !wizard_is_placeholder(tourim_env('RAZORPAY_KEY_SECRET','')), 'Add Razorpay key id and secret when payment gateway is activated.', 'external');
    $checks[] = wizard_check('WhatsApp Business API configured', !wizard_is_placeholder(tourim_env('WHATSAPP_BUSINESS_PHONE_ID','')) && !wizard_is_placeholder(tourim_env('WHATSAPP_BUSINESS_TOKEN','')), 'Add Meta WhatsApp Business API phone id and access token after approval.', 'external');
    $required = array_values(array_filter($checks, fn($c)=>($c['level']??'required')==='required'));
    $requiredPass = count(array_filter($required, fn($c)=>$c['pass']));
    $allPass = count(array_filter($checks, fn($c)=>$c['pass']));
    $score = count($checks) ? round(($allPass / count($checks)) * 100) : 0;
    $softwareComplete = $requiredPass === count($required);
    return ['score'=>$score,'software_complete'=>$softwareComplete,'external_accounts_needed'=>array_values(array_filter($checks, fn($c)=>($c['level']??'')==='external' && !$c['pass'])),'checks'=>$checks,'storage'=>$db['storage_driver'] ?? 'json','base_url'=>tourim_base_url(),'generated_at'=>date('c')];
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'status';

if($action === 'status'){
    wizard_out(['ok'=>true,'readiness'=>wizard_readiness()]);
}

if($action === 'generate_secret'){
    wizard_admin_required();
    wizard_out(['ok'=>true,'secret'=>bin2hex(random_bytes(32))]);
}

if($action === 'save_domain'){
    wizard_admin_required();
    $input=wizard_body();
    $url=trim((string)($input['app_url']??''));
    if(!preg_match('~^https?://[A-Za-z0-9.-]+~',$url)) wizard_out(['ok'=>false,'error'=>'Enter a valid URL, for example https://tourim.in'],400);
    $secret=trim((string)($input['app_secret']??''));
    if($secret==='') $secret=bin2hex(random_bytes(32));
    try{
        wizard_write_env(['APP_ENV'=>'production','APP_URL'=>rtrim($url,'/'),'APP_SECRET'=>$secret]);
        wizard_write_domain_files($url);
        wizard_out(['ok'=>true,'message'=>'Domain, sitemap, robots.txt and APP_URL updated','readiness'=>wizard_readiness()]);
    }catch(Throwable $e){ wizard_out(['ok'=>false,'error'=>$e->getMessage()],500); }
}

if($action === 'save_external_placeholders'){
    wizard_admin_required();
    $input=wizard_body();
    $allowed=['SMTP_HOST','SMTP_PORT','SMTP_USERNAME','SMTP_PASSWORD','RAZORPAY_KEY_ID','RAZORPAY_KEY_SECRET','WHATSAPP_BUSINESS_PHONE_ID','WHATSAPP_BUSINESS_TOKEN','UPI_ID'];
    $updates=[];
    foreach($allowed as $k){ if(isset($input[$k])) $updates[$k]=(string)$input[$k]; }
    try{ wizard_write_env($updates); wizard_out(['ok'=>true,'message'=>'External integration settings saved to .env','readiness'=>wizard_readiness()]); }
    catch(Throwable $e){ wizard_out(['ok'=>false,'error'=>$e->getMessage()],500); }
}

wizard_out(['ok'=>false,'error'=>'Unknown setup action'],404);

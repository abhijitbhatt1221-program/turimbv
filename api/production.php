<?php
/**
 * TOURIM production modules API:
 * roles/users, media upload, payment tracker, invoice/voucher generator,
 * WhatsApp templates, SEO routes, advanced analytics and backups.
 */
declare(strict_types=1);
$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secureCookie,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/storage.php';

$dbFile = __DIR__ . '/../data/tourim_db.json';
$seedFile = __DIR__ . '/../data/tourim_seed.json';

function out(array $payload, int $status=200): void { http_response_code($status); echo json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); exit; }
function db_read(): array { global $dbFile,$seedFile; return tourim_read_db($dbFile,$seedFile); }
function db_write(array $db): void { global $dbFile; tourim_write_db($dbFile,$db); }
function admin_required(): void { if (empty($_SESSION['tourim_admin'])) out(['ok'=>false,'error'=>'Admin login required'],403); }
function clean(string $value): string { return trim(strip_tags($value)); }
function uid(string $prefix): string { return $prefix . '_' . date('ymdHis') . '_' . bin2hex(random_bytes(3)); }
function body(): array { $raw=file_get_contents('php://input'); $data=json_decode($raw?:'{}',true); return is_array($data)?$data:[]; }
function audit(array &$db, string $action, string $details=''): void { $db['auditLogs']=$db['auditLogs']??[]; array_unshift($db['auditLogs'],['id'=>uid('audit'),'action'=>$action,'details'=>$details,'user'=>$_SESSION['tourim_admin_name']??'Admin','role'=>$_SESSION['tourim_role']??'admin','created_at'=>date('c')]); $db['auditLogs']=array_slice($db['auditLogs'],0,500); }
function public_user(array $u): array { unset($u['password_hash'],$u['password']); return $u; }

function prod_role_can(string $permission): bool {
    $role = (string)($_SESSION['tourim_role'] ?? 'viewer');
    if ($role === 'super_admin') return true;
    $map = [
        'admin' => ['manage_users','manage_bookings','manage_payments','manage_media','manage_settings','view_analytics','backup'],
        'sales' => ['manage_bookings','manage_payments','view_analytics'],
        'editor' => ['manage_media','manage_settings','view_analytics'],
        'viewer' => ['view_analytics']
    ];
    return in_array($permission, $map[$role] ?? [], true);
}
function prod_require(string $permission): void { admin_required(); if(!prod_role_can($permission)) out(['ok'=>false,'error'=>'Permission denied for role '.($_SESSION['tourim_role'] ?? 'viewer')],403); }


$action = $_GET['action'] ?? $_POST['action'] ?? 'get';

if ($action === 'get') {
    admin_required();
    $db = db_read();
    $db['users'] = array_map('public_user', $db['users'] ?? []);
    out(['ok'=>true,'modules'=>[
        'users'=>$db['users'] ?? [],
        'roles'=>$db['roles'] ?? [],
        'payments'=>$db['payments'] ?? [],
        'bookings'=>$db['bookings'] ?? [],
        'invoices'=>$db['invoices'] ?? [],
        'vouchers'=>$db['vouchers'] ?? [],
        'media'=>$db['media'] ?? [],
        'whatsappTemplates'=>$db['whatsappTemplates'] ?? [],
        'seoRoutes'=>$db['seoRoutes'] ?? [],
        'auditLogs'=>array_slice($db['auditLogs'] ?? [],0,100),
        'analytics'=>$db['analytics'] ?? [],
        'events'=>array_slice($db['events'] ?? [],-300)
    ]]);
}

if ($action === 'user_save') {
    prod_require('manage_users');
    $input=body(); $db=db_read(); $db['users']=$db['users']??[];
    $id=clean((string)($input['id']??''));
    $user=[
        'id'=>$id ?: uid('user'),
        'name'=>clean((string)($input['name']??'Team Member')),
        'email'=>filter_var((string)($input['email']??''), FILTER_SANITIZE_EMAIL),
        'username'=>preg_replace('/[^a-zA-Z0-9_.-]/','', (string)($input['username']??'')),
        'role'=>clean((string)($input['role']??'sales')),
        'status'=>clean((string)($input['status']??'active')),
        'created_at'=>date('c')
    ];
    if (!empty($input['password'])) $user['password_hash']=password_hash((string)$input['password'], PASSWORD_DEFAULT);
    $found=false;
    foreach($db['users'] as &$u){ if(($u['id']??'')===$user['id']){ $user['created_at']=$u['created_at']??date('c'); if(empty($user['password_hash']) && !empty($u['password_hash'])) $user['password_hash']=$u['password_hash']; $u=$user; $found=true; break; } }
    if(!$found) $db['users'][]=$user;
    audit($db,'user_save',$user['name'].' · '.$user['role']); db_write($db);
    out(['ok'=>true,'user'=>public_user($user)]);
}

if ($action === 'payment_save') {
    prod_require('manage_payments');
    $input=body(); $db=db_read(); $db['payments']=$db['payments']??[];
    $payment=[
        'id'=>clean((string)($input['id']??'')) ?: uid('pay'),
        'customer'=>clean((string)($input['customer']??'')),
        'phone'=>preg_replace('/[^0-9+ -]/','',(string)($input['phone']??'')),
        'package'=>clean((string)($input['package']??'')),
        'amount'=>(float)($input['amount']??0),
        'mode'=>clean((string)($input['mode']??'UPI')),
        'transaction_id'=>clean((string)($input['transaction_id']??'')),
        'status'=>clean((string)($input['status']??'Received')),
        'payment_date'=>clean((string)($input['payment_date']??date('Y-m-d'))),
        'note'=>clean((string)($input['note']??'')),
        'created_at'=>date('c')
    ];
    $found=false; foreach($db['payments'] as &$p){ if(($p['id']??'')===$payment['id']){ $payment['created_at']=$p['created_at']??date('c'); $p=$payment; $found=true; break; } }
    if(!$found) array_unshift($db['payments'],$payment);
    audit($db,'payment_save',$payment['customer'].' · ₹'.$payment['amount']); db_write($db);
    out(['ok'=>true,'payment'=>$payment]);
}

if ($action === 'booking_save') {
    prod_require('manage_bookings');
    $input=body(); $db=db_read(); $db['bookings']=$db['bookings']??[];
    $booking=[
        'id'=>clean((string)($input['id']??'')) ?: uid('book'),
        'customer'=>clean((string)($input['customer']??'')),
        'phone'=>preg_replace('/[^0-9+ -]/','',(string)($input['phone']??'')),
        'email'=>filter_var((string)($input['email']??''), FILTER_SANITIZE_EMAIL),
        'package'=>clean((string)($input['package']??'')),
        'destination'=>clean((string)($input['destination']??'')),
        'travel_date'=>clean((string)($input['travel_date']??'')),
        'pax'=>clean((string)($input['pax']??'')),
        'total_amount'=>(float)($input['total_amount']??0),
        'advance'=>(float)($input['advance']??0),
        'balance'=>(float)($input['balance']??0),
        'status'=>clean((string)($input['status']??'Confirmed')),
        'hotel_plan'=>clean((string)($input['hotel_plan']??'')),
        'transport_plan'=>clean((string)($input['transport_plan']??'')),
        'created_at'=>date('c')
    ];
    $found=false; foreach($db['bookings'] as &$b){ if(($b['id']??'')===$booking['id']){ $booking['created_at']=$b['created_at']??date('c'); $b=$booking; $found=true; break; } }
    if(!$found) array_unshift($db['bookings'],$booking);
    audit($db,'booking_save',$booking['customer'].' · '.$booking['package']); db_write($db);
    out(['ok'=>true,'booking'=>$booking]);
}

function write_document(string $type, array $record): string {
    $safeId = preg_replace('/[^A-Za-z0-9_-]/','', $record['id'] ?? uid($type));
    $dir = dirname(__DIR__) . '/documents/' . ($type === 'invoice' ? 'invoices' : 'vouchers');
    if(!is_dir($dir)) mkdir($dir,0755,true);
    $file = $dir . '/' . $safeId . '.html';
    $title = strtoupper($type) . ' · TOURIM';
    $rows='';
    foreach($record as $k=>$v){ if(is_array($v)) continue; $rows .= '<tr><th>'.htmlspecialchars(str_replace('_',' ',ucwords((string)$k))).'</th><td>'.htmlspecialchars((string)$v).'</td></tr>'; }
    $html='<!doctype html><html><head><meta charset="utf-8"><title>'.$title.'</title><style>body{font-family:Arial,sans-serif;margin:0;background:#f7f1e8;color:#15232d}.sheet{max-width:850px;margin:30px auto;background:#fff;padding:34px;border-radius:18px;box-shadow:0 10px 30px #0001}.brand{display:flex;justify-content:space-between;gap:18px;border-bottom:3px solid #f47b20;padding-bottom:16px;margin-bottom:22px}.brand h1{margin:0;color:#f47b20}table{width:100%;border-collapse:collapse}th,td{text-align:left;border-bottom:1px solid #eee;padding:12px}th{width:230px;background:#fff8f1}.sign{margin-top:42px;display:flex;justify-content:space-between}.print{position:fixed;right:20px;top:20px}@media print{.print{display:none}.sheet{box-shadow:none;margin:0}}</style></head><body><button class="print" onclick="print()">Print / Save PDF</button><main class="sheet"><div class="brand"><div><h1>TOURIM</h1><p>See the world, Feel with TOURIM<br>Kachua more, Ashoknagar to Jirat road, Habra<br>owner.tourim@gmail.com · 7384732179 / 8972076635</p></div><div><h2>'.$title.'</h2><p>Date: '.date('d M Y').'</p></div></div><table>'.$rows.'</table><div class="sign"><p><b>Customer Signature</b><br><br>_________________</p><p><b>For TOURIM</b><br><br>_________________</p></div></main></body></html>';
    file_put_contents($file,$html,LOCK_EX);
    return tourim_base_url() . '/documents/' . ($type === 'invoice' ? 'invoices' : 'vouchers') . '/' . basename($file);
}

if ($action === 'document_generate') {
    prod_require('manage_bookings');
    $input=body(); $db=db_read();
    $type = clean((string)($input['type']??'invoice')) === 'voucher' ? 'voucher' : 'invoice';
    $record = $input['record'] ?? $input;
    if(!is_array($record)) $record=[];
    $record['id'] = clean((string)($record['id']??uid($type)));
    $record['document_type'] = $type;
    $record['generated_at'] = date('c');
    $url = write_document($type,$record);
    $record['url']=$url;
    $key = $type === 'invoice' ? 'invoices' : 'vouchers';
    $db[$key]=$db[$key]??[]; array_unshift($db[$key],$record);
    audit($db,'document_generate',$type.' · '.$record['id']); db_write($db);
    out(['ok'=>true,'document'=>$record,'url'=>$url]);
}

if ($action === 'upload') {
    prod_require('manage_media');
    if (empty($_FILES['file'])) out(['ok'=>false,'error'=>'No file uploaded'],400);
    $file=$_FILES['file'];
    if (($file['error']??UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) out(['ok'=>false,'error'=>'Upload error'],400);
    if (($file['size']??0) > 5*1024*1024) out(['ok'=>false,'error'=>'Max image size is 5 MB'],400);
    $mime = '';
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
    } elseif (function_exists('mime_content_type')) {
        $mime = mime_content_type($file['tmp_name']);
    } else {
        $mime = $file['type'] ?? '';
    }
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    if (!isset($allowed[$mime])) {
        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        $extMap = ['jpg'=>'jpg','jpeg'=>'jpg','png'=>'png','webp'=>'webp','gif'=>'gif'];
        if (isset($extMap[$ext])) {
            $mime = (string)array_search($extMap[$ext], $allowed, true);
        }
    }
    if(!isset($allowed[$mime])) out(['ok'=>false,'error'=>'Only JPG, PNG, WebP or GIF allowed'],400);
    $dir = dirname(__DIR__).'/uploads/media/'.date('Y/m');
    if(!is_dir($dir)) mkdir($dir,0755,true);
    $base = preg_replace('/[^a-zA-Z0-9_-]/','-', pathinfo((string)$file['name'], PATHINFO_FILENAME));
    $name = strtolower($base ?: 'tourim-media') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $dest = $dir.'/'.$name;
    if(!move_uploaded_file($file['tmp_name'],$dest)) out(['ok'=>false,'error'=>'Could not store uploaded file'],500);
    $url = tourim_base_url() . '/uploads/media/'.date('Y/m').'/'.$name;
    $db=db_read(); $db['media']=$db['media']??[];
    $media=['id'=>uid('media'),'file_url'=>$url,'alt_text'=>clean((string)($_POST['alt_text']??'')),'type'=>clean((string)($_POST['type']??'general')),'size'=>(int)$file['size'],'mime'=>$mime,'created_at'=>date('c')];
    array_unshift($db['media'],$media); audit($db,'media_upload',$url); db_write($db);
    out(['ok'=>true,'media'=>$media]);
}

if ($action === 'analytics_report') {
    prod_require('view_analytics');
    $db=db_read(); $events=$db['events']??[];
    $byType=[]; $byDay=[]; $topItems=[];
    foreach($events as $e){
        $type=$e['event_type']??'event'; $byType[$type]=($byType[$type]??0)+1;
        $day=substr((string)($e['created_at']??''),0,10); if($day) $byDay[$day]=($byDay[$day]??0)+1;
        $title=$e['title']??($e['item_id']??'Unknown'); if($title) $topItems[$title]=($topItems[$title]??0)+1;
    }
    arsort($byType); ksort($byDay); arsort($topItems);
    out(['ok'=>true,'report'=>['byType'=>$byType,'byDay'=>$byDay,'topItems'=>array_slice($topItems,0,10,true),'totals'=>$db['analytics']??[]]]);
}

if ($action === 'save_settings') {
    prod_require('manage_settings'); $input=body(); $db=db_read();
    foreach(['whatsappTemplates','seoRoutes','roles'] as $key){ if(isset($input[$key]) && is_array($input[$key])) $db[$key]=$input[$key]; }
    audit($db,'production_settings_save','WhatsApp / SEO / role settings updated'); db_write($db); out(['ok'=>true]);
}

if ($action === 'backup') {
    prod_require('backup'); $db=db_read(); $dir=dirname(__DIR__).'/backups'; if(!is_dir($dir)) mkdir($dir,0755,true);
    $file=$dir.'/tourim-backup-'.date('Ymd-His').'.json'; file_put_contents($file,json_encode($db,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE),LOCK_EX);
    out(['ok'=>true,'file'=>basename($file),'url'=>tourim_base_url().'/api/production.php?action=download_backup&file='.rawurlencode(basename($file))]);
}

if ($action === 'download_backup') {
    prod_require('backup');
    $name = preg_replace('/[^A-Za-z0-9_.-]/','', (string)($_GET['file'] ?? ''));
    $file = dirname(__DIR__).'/backups/'.$name;
    if(!$name || !is_file($file)) out(['ok'=>false,'error'=>'Backup not found'],404);
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="'.$name.'"');
    readfile($file); exit;
}

out(['ok'=>false,'error'=>'Unknown production action'],404);

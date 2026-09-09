<?php
/**
 * TOURIM external integration gateway.
 * Ready for Razorpay order creation and WhatsApp Cloud API when live keys are added.
 * Without keys it returns safe fallback instructions/links instead of breaking the app.
 */
declare(strict_types=1);
$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secureCookie,'httponly'=>true,'samesite'=>'Lax']);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/config.php';

function int_out(array $payload, int $status=200): void { http_response_code($status); echo json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT); exit; }
function int_body(): array { $raw=file_get_contents('php://input'); $data=json_decode($raw?:'{}',true); return is_array($data)?$data:[]; }
function int_admin_required(): void { if(empty($_SESSION['tourim_admin'])) int_out(['ok'=>false,'error'=>'Admin login required'],403); }
function int_placeholder(?string $v): bool { $v=trim((string)$v); return $v==='' || stripos($v,'PASTE_')!==false || stripos($v,'your_')!==false || stripos($v,'change-this')!==false; }
function int_clean_phone(string $phone): string { return preg_replace('/[^0-9]/','',$phone); }
function int_has_curl(): bool { return function_exists('curl_init'); }

$action=$_GET['action'] ?? $_POST['action'] ?? 'status';

if($action==='status'){
    int_out(['ok'=>true,'integrations'=>[
        'smtp'=>['configured'=>!int_placeholder(tourim_env('SMTP_PASSWORD','')),'username'=>tourim_env('SMTP_USERNAME','owner.tourim@gmail.com')],
        'razorpay'=>['configured'=>!int_placeholder(tourim_env('RAZORPAY_KEY_ID','')) && !int_placeholder(tourim_env('RAZORPAY_KEY_SECRET',''))],
        'whatsapp'=>['configured'=>!int_placeholder(tourim_env('WHATSAPP_BUSINESS_PHONE_ID','')) && !int_placeholder(tourim_env('WHATSAPP_BUSINESS_TOKEN',''))],
        'upi'=>['configured'=>!int_placeholder(tourim_env('UPI_ID','')),'upi_id'=>tourim_env('UPI_ID','')],
        'curl'=>['enabled'=>int_has_curl()]
    ]]);
}

if($action==='razorpay_order'){
    int_admin_required();
    $input=int_body();
    $amount=(float)($input['amount']??0);
    if($amount<=0) int_out(['ok'=>false,'error'=>'Amount is required'],400);
    $key=tourim_env('RAZORPAY_KEY_ID',''); $secret=tourim_env('RAZORPAY_KEY_SECRET','');
    $receipt='tourim_'.date('YmdHis').'_'.bin2hex(random_bytes(2));
    if(int_placeholder($key) || int_placeholder($secret) || !int_has_curl()){
        int_out(['ok'=>true,'mode'=>'fallback','needs_config'=>true,'message'=>'Razorpay keys/cURL not configured. Manual payment tracker is ready. Add keys in .env to activate live gateway.','receipt'=>$receipt,'amount'=>$amount]);
    }
    $payload=json_encode(['amount'=>(int)round($amount*100),'currency'=>'INR','receipt'=>$receipt,'notes'=>['source'=>'TOURIM','customer'=>(string)($input['customer']??'')]]);
    $ch=curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_USERPWD=>$key.':'.$secret,CURLOPT_TIMEOUT=>20]);
    $raw=curl_exec($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $err=curl_error($ch); curl_close($ch);
    if($raw===false || $code>=400) int_out(['ok'=>false,'error'=>$err ?: 'Razorpay order failed','status'=>$code,'response'=>$raw],500);
    $order=json_decode((string)$raw,true);
    int_out(['ok'=>true,'mode'=>'live','order'=>$order]);
}

if($action==='whatsapp_link'){
    $input=int_body();
    $phone=int_clean_phone((string)($input['phone']??tourim_env('BUSINESS_WHATSAPP','7384732179')));
    $msg=(string)($input['message']??'Hello TOURIM, I want to know more about a tour package.');
    int_out(['ok'=>true,'url'=>'https://wa.me/'.$phone.'?text='.rawurlencode($msg)]);
}

if($action==='whatsapp_send'){
    int_admin_required();
    $input=int_body();
    $to=int_clean_phone((string)($input['to']??''));
    $message=trim((string)($input['message']??''));
    if($to==='' || $message==='') int_out(['ok'=>false,'error'=>'Phone and message are required'],400);
    $phoneId=tourim_env('WHATSAPP_BUSINESS_PHONE_ID',''); $token=tourim_env('WHATSAPP_BUSINESS_TOKEN','');
    if(int_placeholder($phoneId) || int_placeholder($token) || !int_has_curl()){
        int_out(['ok'=>true,'mode'=>'fallback','needs_config'=>true,'url'=>'https://wa.me/'.$to.'?text='.rawurlencode($message),'message'=>'WhatsApp Business API is not configured yet. Opening wa.me link is available.']);
    }
    $payload=json_encode(['messaging_product'=>'whatsapp','to'=>$to,'type'=>'text','text'=>['preview_url'=>false,'body'=>$message]]);
    $ch=curl_init('https://graph.facebook.com/v20.0/'.$phoneId.'/messages');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$payload,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.$token],CURLOPT_TIMEOUT=>20]);
    $raw=curl_exec($ch); $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE); $err=curl_error($ch); curl_close($ch);
    if($raw===false || $code>=400) int_out(['ok'=>false,'error'=>$err ?: 'WhatsApp API send failed','status'=>$code,'response'=>$raw],500);
    int_out(['ok'=>true,'mode'=>'live','response'=>json_decode((string)$raw,true)]);
}

int_out(['ok'=>false,'error'=>'Unknown integration action'],404);

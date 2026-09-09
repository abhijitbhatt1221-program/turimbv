<?php
header('Content-Type: application/json');

$config = require __DIR__ . '/mail_config.php';

function json_response($success, $message, $code = 200, $extra = []) {
    http_response_code($code);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

function smtp_read($socket) {
    $data = '';
    while ($str = fgets($socket, 515)) {
        $data .= $str;
        if (isset($str[3]) && $str[3] === ' ') {
            break;
        }
    }
    return $data;
}

function smtp_expect($socket, $expectedCodes, $context) {
    $response = smtp_read($socket);
    $code = substr($response, 0, 3);
    if (!in_array($code, (array)$expectedCodes, true)) {
        throw new Exception($context . ' failed. SMTP response: ' . trim($response));
    }
    return $response;
}

function smtp_command($socket, $command, $expectedCodes, $context) {
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $expectedCodes, $context);
}

function encode_header_text($value) {
    $value = (string)$value;
    if (function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($value, 'UTF-8');
    }
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function build_mail_body($payload) {
    $action = isset($payload['action']) ? $payload['action'] : 'settings_updated';

    $reason = ($action === 'forgot_password')
        ? 'Forgot password request received from the TOURIM admin login page.'
        : (($action === 'test_mail')
            ? 'SMTP test mail sent from TOURIM Admin Settings.'
            : 'Admin username/email/password was updated from TOURIM Admin Settings.');

    $body  = $reason . "\n\n";
    $body .= "Website: " . ($payload['website'] ?? 'TOURIM') . "\n";
    $body .= "Admin Username: " . ($payload['username'] ?? '') . "\n";
    $body .= "Admin Login Email: " . ($payload['admin_email'] ?? '') . "\n";
    $body .= "Admin Password: " . ($payload['admin_password'] ?? '') . "\n\n";
    $body .= "Time: " . ($payload['generated_at'] ?? date('Y-m-d H:i:s')) . "\n\n";
    $body .= "Security Note: This message was generated automatically by the TOURIM admin panel. Please keep these details confidential.\n";
    return $body;
}

function smtp_send_mail($config, $to, $subject, $body) {
    $host = $config['SMTP_HOST'];
    $port = (int)$config['SMTP_PORT'];
    $secure = strtolower($config['SMTP_SECURE']);
    $username = $config['SMTP_USERNAME'];
    $password = preg_replace('/\s+/', '', (string)$config['SMTP_PASSWORD']);
    $fromEmail = $config['FROM_EMAIL'];
    $fromName = $config['FROM_NAME'];
    $replyTo = $config['REPLY_TO'] ?? $fromEmail;

    if ($password === '' || $password === 'PASTE_GMAIL_APP_PASSWORD_HERE') {
        throw new Exception('SMTP app password is not configured. Edit api/mail_config.php first.');
    }

    if (($secure === 'tls' || $secure === 'ssl') && !extension_loaded('openssl')) {
        throw new Exception('PHP OpenSSL extension is not enabled. Gmail SMTP requires OpenSSL/TLS. Enable openssl on hosting or use a host with SMTP TLS support.');
    }

    $remote = ($secure === 'ssl')
        ? "ssl://{$host}:{$port}"
        : "{$host}:{$port}";

    $socket = stream_socket_client($remote, $errno, $errstr, 30, STREAM_CLIENT_CONNECT);

    if (!$socket) {
        throw new Exception("Could not connect to SMTP server: {$errstr} ({$errno})");
    }

    stream_set_timeout($socket, 30);
    smtp_expect($socket, ['220'], 'SMTP connect');

    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    smtp_command($socket, "EHLO " . $serverName, ['250'], 'EHLO');

    if ($secure === 'tls') {
        smtp_command($socket, "STARTTLS", ['220'], 'STARTTLS');
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception('Could not enable TLS encryption.');
        }
        smtp_command($socket, "EHLO " . $serverName, ['250'], 'EHLO after STARTTLS');
    }

    smtp_command($socket, "AUTH LOGIN", ['334'], 'AUTH LOGIN');
    smtp_command($socket, base64_encode($username), ['334'], 'SMTP username');
    smtp_command($socket, base64_encode($password), ['235'], 'SMTP password');

    smtp_command($socket, "MAIL FROM:<{$fromEmail}>", ['250'], 'MAIL FROM');
    smtp_command($socket, "RCPT TO:<{$to}>", ['250', '251'], 'RCPT TO');
    smtp_command($socket, "DATA", ['354'], 'DATA');

    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [];
    $headers[] = "From: " . encode_header_text($fromName) . " <{$fromEmail}>";
    $headers[] = "To: <{$to}>";
    $headers[] = "Reply-To: {$replyTo}";
    $headers[] = "Subject: {$encodedSubject}";
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: 8bit";
    $headers[] = "X-Mailer: TOURIM PHP SMTP Mailer";

    $safeBody = str_replace(["\r\n", "\r"], "\n", $body);
    $safeBody = str_replace("\n.", "\n..", $safeBody);
    $safeBody = str_replace("\n", "\r\n", $safeBody);
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $safeBody . "\r\n.";

    fwrite($socket, $message . "\r\n");
    smtp_expect($socket, ['250'], 'Message send');
    smtp_command($socket, "QUIT", ['221'], 'QUIT');

    fclose($socket);
    return true;
}

function php_mail_fallback($config, $to, $subject, $body) {
    $fromEmail = $config['FROM_EMAIL'] ?? 'no-reply@example.com';
    $fromName = $config['FROM_NAME'] ?? 'TOURIM Admin';
    $replyTo = $config['REPLY_TO'] ?? $fromEmail;

    $headers  = "From: {$fromName} <{$fromEmail}>\r\n";
    $headers .= "Reply-To: {$replyTo}\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return @mail($to, $subject, $body, $headers);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!$payload || !is_array($payload)) {
    json_response(false, 'Invalid request', 400);
}

$username = trim($payload['username'] ?? '');
$adminEmail = trim($payload['admin_email'] ?? '');
$adminPassword = trim($payload['admin_password'] ?? '');

if ($username === '' || $adminEmail === '') {
    json_response(false, 'Username or email missing', 422);
}

$action = $payload['action'] ?? 'settings_updated';
$subject = ($action === 'forgot_password')
    ? 'TOURIM Admin Credential Recovery'
    : (($action === 'test_mail') ? 'TOURIM SMTP Test Mail' : 'TOURIM Admin Login Details Updated');

$to = $config['RECOVERY_EMAIL'] ?? 'abhijitbhatt1221@gmail.com';
$body = build_mail_body($payload);

try {
    if (($config['MAIL_DRIVER'] ?? 'smtp') === 'smtp') {
        smtp_send_mail($config, $to, $subject, $body);
        json_response(true, 'SMTP mail sent successfully', 200, ['driver' => 'smtp']);
    }

    if (php_mail_fallback($config, $to, $subject, $body)) {
        json_response(true, 'PHP mail sent successfully', 200, ['driver' => 'php_mail']);
    }

    throw new Exception('PHP mail failed.');
} catch (Throwable $e) {
    // Try plain PHP mail as fallback if SMTP fails.
    if (php_mail_fallback($config, $to, $subject, $body)) {
        json_response(true, 'SMTP failed, but PHP mail fallback sent successfully', 200, [
            'driver' => 'php_mail_fallback',
            'smtp_error' => $e->getMessage()
        ]);
    }

    json_response(false, $e->getMessage(), 500, ['driver' => 'smtp']);
}
?>

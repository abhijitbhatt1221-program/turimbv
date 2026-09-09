<?php
require_once __DIR__ . '/config.php';

/*
|--------------------------------------------------------------------------
| TOURIM SMTP Mail Configuration
|--------------------------------------------------------------------------
| This file is used by api/admin_mail.php.
|
| For Gmail SMTP:
| 1. Enable 2-Step Verification on the Gmail account.
| 2. Create an App Password from Google Account > Security > App passwords.
| 3. Paste ONLY the 16-character app password below.
|
| Do NOT use your normal Gmail password.
*/

return [
    // Use SMTP for professional delivery on InfinityFree/PHP hosting.
    'MAIL_DRIVER' => tourim_env('MAIL_DRIVER', 'smtp'),

    // Gmail SMTP settings.
    'SMTP_HOST' => tourim_env('SMTP_HOST', 'smtp.gmail.com'),
    'SMTP_PORT' => (int)tourim_env('SMTP_PORT', '587'),
    'SMTP_SECURE' => tourim_env('SMTP_SECURE', 'tls'),

    // Change this to the Gmail account that will send TOURIM admin emails.
    'SMTP_USERNAME' => tourim_env('SMTP_USERNAME', 'abhijitbhatt1221@gmail.com'),

    // Paste your Gmail App Password here.
    // Example format: 'abcd efgh ijkl mnop'
    'SMTP_PASSWORD' => tourim_env('SMTP_PASSWORD', 'nbskgfewrycxkhkw'),

    // Sender details shown in the email.
    'FROM_EMAIL' => tourim_env('FROM_EMAIL', tourim_env('SMTP_USERNAME', 'abhijitbhatt1221@gmail.com')),
    'FROM_NAME' => tourim_env('FROM_NAME', 'TOURIM Admin'),

    // Fixed recovery/security receiver.
    'RECOVERY_EMAIL' => tourim_env('RECOVERY_EMAIL', 'abhijitbhatt1221@gmail.com'),

    // Optional reply-to.
    'REPLY_TO' => tourim_env('REPLY_TO', tourim_env('SMTP_USERNAME', 'abhijitbhatt1221@gmail.com')),
];
?>

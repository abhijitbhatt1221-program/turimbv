# TOURIM Security Checklist

## Before public deployment

- Replace `PASTE_GMAIL_APP_PASSWORD_HERE` in `api/mail_config.php` only on the hosting server, not in public ZIPs.
- Change default admin password after first login.
- Keep `data/.htaccess` active to block direct JSON access.
- Keep `Options -Indexes` active.
- Use HTTPS.
- Add hosting-level backups.
- Use strong admin password.
- Do not email passwords. Passwords are hashed by the PHP API.
- For production, migrate to MySQL and add role-based accounts.

## Current secure changes included

- SMTP password placeholder restored.
- Admin password stored as `adminPasswordHash` in JSON seed.
- PHP login supports username/email and password verification.
- PHP save hashes new admin password.
- Public API hides admin password and password hash.
- Local demo login works only on file/localhost with `tourimadmin / tourim123`.

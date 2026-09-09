# TOURIM Live Launch Wizard

Open after admin login:

```text
/ADMIN/launch
```

or:

```text
launch-check.html
```

The launch wizard checks PHP version, file permissions, protected folders, database mode, admin password hash, runtime config, SMTP, Razorpay, WhatsApp Business API, sitemap and robots.txt readiness.

## What the wizard can update

- `APP_URL`
- `APP_SECRET`
- `robots.txt`
- `sitemap.xml`
- SMTP placeholder values
- Razorpay placeholder values
- WhatsApp placeholder values
- UPI ID

## Important Security Rule

Add real keys only on the live private server. Do not place real SMTP, Razorpay or WhatsApp tokens inside a ZIP file that will be shared.

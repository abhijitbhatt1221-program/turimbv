# External Account Setup for 100% Live TOURIM Launch

The software is complete. These services require real business accounts before live use.

## SMTP Email

Use a Gmail App Password or a transactional email provider. Put the value in `.env`:

```text
SMTP_USERNAME=owner.tourim@gmail.com
SMTP_PASSWORD=your_private_app_password
```

## Razorpay

Create a Razorpay business account, collect Key ID and Key Secret, then put them in `.env`:

```text
RAZORPAY_KEY_ID=your_key_id
RAZORPAY_KEY_SECRET=your_key_secret
```

`api/integrations.php?action=razorpay_order` is already implemented.

## WhatsApp Business API

After Meta approval, add:

```text
WHATSAPP_BUSINESS_PHONE_ID=your_phone_number_id
WHATSAPP_BUSINESS_TOKEN=your_access_token
```

Until then, TOURIM falls back to safe `wa.me` links.

## MySQL

Set:

```text
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=tourim
DB_USER=...
DB_PASS=...
```

Then run:

```text
api/install_mysql.php
```

Delete or rename `api/install_mysql.php` after installation.

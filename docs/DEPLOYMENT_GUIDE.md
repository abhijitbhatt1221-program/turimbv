# TOURIM Deployment Guide

## Shared PHP Hosting / InfinityFree

1. Upload all files from this folder to `htdocs` or `public_html`.
2. Visit `/api/health.php` and `/api/db.php?action=health`.
3. Visit `/admin` or `/ADMIN`.
4. Login with your hosted admin account. Initial demo credential is `owner.tourim@gmail.com` or `tourimadmin` with `tourim123` if using the included seed.
5. Immediately change password in Settings.
6. Edit `api/mail_config.php` on the server and add Gmail App Password if recovery/test email is needed.
7. Submit a test enquiry from `contact.html`.
8. Confirm it appears in Admin → Queries.
9. Update `sitemap.xml` domain.
10. Submit sitemap in Google Search Console.

## Recommended production steps

- Move from JSON file storage to MySQL using `database/mysql_schema.sql`.
- Move images to Cloudinary/S3.
- Add automated backups.
- Add WhatsApp Business API and payment gateway.

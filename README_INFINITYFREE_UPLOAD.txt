TOURIM - InfinityFree Upload Package
====================================

This folder is converted for InfinityFree-style hosting.
Your original project was Next.js + Node/Express + SQLite. InfinityFree free hosting does not run Node.js apps, so this package uses:

- index.html for the public website
- admin.html for the admin dashboard
- css/ and js/ frontend assets
- api/db.php PHP API for hosted data sync
- data/tourim_db.json for CMS/admin data storage
- .htaccess for /ADMIN shortcut and file protection

How to upload on InfinityFree
-----------------------------
1. Login to InfinityFree / VistaPanel.
2. Open File Manager or use FTP.
3. Go inside your domain's htdocs folder.
4. Upload/extract all files from this ZIP directly inside htdocs.
   Do not upload the outer folder as htdocs/htdocs.
5. Visit your domain.
6. Admin login URL: https://yourdomain.com/ADMIN

Default Admin Login
-------------------
Email: owner.tourim@gmail.com
Password: tourim123

After upload, login and change the password from Settings.

Important Notes
---------------
- Do not upload package.json, node_modules, .next, server/, or SQLite files to InfinityFree.
- This package already removed the Node/Next development files.
- Admin changes will sync through api/db.php and data/tourim_db.json on the hosted site.
- If saving does not work, check that data/tourim_db.json is writable in File Manager.
- If /ADMIN does not open, directly open admin.html.
- If your hosting blocks .htaccess rules, the website will still work with index.html and admin.html.

Recommended upload structure inside htdocs
-----------------------------------------
.htaccess
index.html
admin.html
api/db.php
css/styles.css
js/data.js
js/site.js
js/admin.js
assets/logo.svg
data/tourim_db.json
data/tourim_seed.json
public/assets/logo.png
public/uploads/

2026 UI Enhancement Update
--------------------------
- Frontend visual refresh added for a cleaner premium travel marketplace look.
- Pixelated-looking logo/background issue improved with a cleaned high-resolution TOURIM logo asset and CSS-based sharp hero/card backgrounds.
- Admin dashboard redesigned with a professional left sidebar navigation on desktop.
- Sidebar becomes an off-canvas mobile menu on smaller screens using the menu button.
- Responsive layout improvements added for dashboard cards, tables, forms, hero area, and navigation.

Upload all files from this ZIP into your InfinityFree htdocs/root folder, replacing existing files.


NEW IN THIS BUILD:
- Admin can now upload, edit, and preview images for Hero, Offer Banner, Package, Destination, Hotel, and Blog cards.
- Recommended image sizes are shown inside the admin panel.
- Homepage hero recommendation: 1600x520
- Package / Destination / Hotel / Blog card recommendation: 800x600
- Offer banner recommendation: 1600x450

- Admin header date now shows the real current date and live time automatically.


- Admin dashboard now has a protected "Reset Numbers to 0" action.
- Reset flow asks two confirmations and then password verification.
- Dashboard reset confirmation phrase: RESET / RESTORE
- Reset affects displayed dashboard counters/analytics safely without deleting website package/destination/hotel content.


- Admin settings now include Admin Username, Admin Login Email, Admin Password, and Recovery Email.
- When admin login details are changed and submitted, a security email is sent to abhijitbhatt1221@gmail.com.
- Login page now supports "Forgot username or password?" recovery.
- Forgot-password recovery sends saved admin username/email/password to abhijitbhatt1221@gmail.com.
- Mail is handled by api/admin_mail.php using PHP mail(). If hosting mail fails, the browser opens a pre-filled mail app as fallback.


SMTP MAILER UPDATE:
- Added PHP SMTP mailer as a free Nodemailer alternative for InfinityFree/PHP hosting.
- Configure Gmail SMTP in api/mail_config.php.
- Set SMTP_USERNAME to your sender Gmail.
- Paste a Gmail App Password in SMTP_PASSWORD.
- Do not use your normal Gmail password.
- The admin panel includes "Send Test Recovery Mail" in Settings.
- Recovery/security emails are sent to abhijitbhatt1221@gmail.com.


SMTP CONFIG APPLIED:
- SMTP username and Gmail App Password have been added in api/mail_config.php.
- Sender email has been set as provided.
- If Gmail SMTP test fails, set FROM_EMAIL to the same Gmail as SMTP_USERNAME because Gmail may reject or rewrite unverified sender aliases.


PICTURE SECTION UPDATE:
- Attractive destination/place-based images have been integrated into the website.
- Added files under assets/travel/.
- Package, Destination, Hotel, Blog, Hero, and Offer Banner image paths are now connected in js/data.js.
- Card images are prepared at 800x600.
- Hero image is prepared at 1600x520.
- Offer banner is prepared at 1600x450.

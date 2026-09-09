TOURIM InfinityFree WordPress Theme Package
===========================================

What this ZIP is
----------------
This is the WordPress-ready version of your existing TOURIM InfinityFree static/PHP build.
It is an installable WordPress theme folder named: tourim-infinityfree-wp

Converted modules included
--------------------------
- WordPress theme header: style.css
- Theme routing + setup: functions.php
- WordPress templates for all original pages:
  Home, About, Destinations, Destination Detail, Packages, Package Detail,
  Hotels, Blogs, Blog Detail, Offers, Contact, Gallery, Policy, Privacy, Terms
- Frontend TOURIM Command Center route:
  /ADMIN or /admin.html
- Existing TOURIM assets, CSS, JavaScript, images, data files, documents, and PHP API
- Route support for old .html links, so links like packages.html and package-detail.html?id=pkg_1 continue working

InfinityFree WordPress install steps
------------------------------------
1. Login to WordPress Admin: yourdomain.com/wp-admin
2. Go to Appearance > Themes > Add New > Upload Theme.
3. Upload: tourim-wordpress-infinityfree.zip
4. Click Install Now, then Activate.
5. Open your domain homepage.
6. Open TOURIM admin at: yourdomain.com/ADMIN
7. Default demo login may be username: tourimadmin. After first login, set a secure password from TOURIM Settings.

Important after activation
--------------------------
- The theme automatically creates basic WordPress pages and sets Home as the front page.
- The original .html links are intentionally preserved and routed by functions.php.
- The TOURIM CMS data API is stored in the theme folder under api/ and data/.
- If saving from TOURIM Admin fails, set permissions for the theme data folder so PHP can write to:
  wp-content/themes/tourim-infinityfree-wp/data/tourim_db.json
- For production, configure MySQL using .env or environment variables if your hosting supports it.

Security notes
--------------
- Keep WordPress admin password strong.
- Change TOURIM frontend admin credentials after first login.
- Do not upload a real .env file with public secrets unless you understand the hosting security model.
- data/.htaccess is included to prevent direct browser access to JSON data on Apache-based hosting.

Recommended URL checks
----------------------
- / or /index.html
- /packages.html
- /package-detail.html?id=pkg_1
- /destinations.html
- /contact.html
- /policy.html
- /ADMIN


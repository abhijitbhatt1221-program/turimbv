# TOURIM MySQL Setup

The app works with JSON by default. To enable MySQL:

1. Create a MySQL database from hosting panel.
2. Copy `.env.example` to `.env`.
3. Set:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_user
DB_PASS=your_password
```

4. Visit `/api/install_mysql.php` once.
5. Confirm `/api/db.php?action=health` shows `storage: mysql`.
6. Delete or rename `api/install_mysql.php` after setup.

The app stores the complete CMS data in `tourim_kv` for easy migration and backup while also providing normalized table definitions for future expansion.

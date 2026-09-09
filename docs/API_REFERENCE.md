# TOURIM PHP API Reference

Base endpoint: `api/db.php`

- `GET ?action=health` — API health check
- `GET ?action=get` — public site data, sensitive admin fields hidden
- `GET ?action=get&private=1` — private CMS data after admin session
- `POST ?action=login` — body `{ "email": "...", "password": "..." }`
- `POST ?action=logout` — destroys admin session
- `POST ?action=save` — saves full CMS DB, admin only
- `POST ?action=enquiry` — saves public enquiry
- `POST ?action=event` — saves analytics event

Admin mail endpoint: `api/admin_mail.php`
